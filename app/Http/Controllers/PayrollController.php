<?php

/**
 * Payroll Controller — Nómina Operativa Semanal.
 *
 * Provides CRUD for weekly payroll entries grouped by project,
 * with overtime support and estimated tax calculations.
 */

namespace App\Http\Controllers;

use App\Models\PayrollEntry;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Utils\Traits\MakesHash;
use Carbon\Carbon;

class PayrollController extends BaseController
{
    use MakesHash;

    /**
     * GET /api/v1/payroll
     *
     * Returns entries grouped by week → project → workers.
     * Supports filtering by week, year, worker_name, project_id.
     */
    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $company = $user->company();

        $query = PayrollEntry::where('company_id', $company->id)
            ->whereNull('deleted_at')
            ->with('project')
            ->orderBy('date', 'desc');

        // Filters
        if ($request->has('week') && $request->has('year')) {
            $week = (int) $request->input('week');
            $year = (int) $request->input('year');
            $startOfWeek = Carbon::now()->setISODate($year, $week)->startOfWeek();
            $endOfWeek = $startOfWeek->copy()->endOfWeek();
            $query->whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()]);
        }

        if ($request->has('worker_name')) {
            $query->where('worker_name', 'like', '%' . $request->input('worker_name') . '%');
        }

        if ($request->has('project_id') && $request->input('project_id')) {
            $query->where('project_id', $this->decodePrimaryKey($request->input('project_id')));
        }

        $entries = $query->get();

        // Group: week → project → workers
        $byWeek = [];
        foreach ($entries as $entry) {
            $date = Carbon::parse($entry->date);
            $weekKey = $date->isoWeekYear . '-W' . str_pad($date->isoWeek, 2, '0', STR_PAD_LEFT);

            if (!isset($byWeek[$weekKey])) {
                $startOfWeek = $date->copy()->startOfWeek();
                $endOfWeek = $date->copy()->endOfWeek();
                $byWeek[$weekKey] = [
                    'week_key' => $weekKey,
                    'week_number' => $date->isoWeek,
                    'year' => $date->isoWeekYear,
                    'start_date' => $startOfWeek->toDateString(),
                    'end_date' => $endOfWeek->toDateString(),
                    'total_base' => 0.0,
                    'total_overtime' => 0.0,
                    'total_pay' => 0.0,
                    'worker_count' => 0,
                    'projects' => [],
                ];
            }

            $projectId = $entry->project_id ?? 0;
            $projectName = $entry->project ? $entry->project->name : 'Sin Proyecto';

            if (!isset($byWeek[$weekKey]['projects'][$projectId])) {
                $byWeek[$weekKey]['projects'][$projectId] = [
                    'project_id' => $entry->project_id,
                    'project_name' => $projectName,
                    'worker_count' => 0,
                    'subtotal_base' => 0.0,
                    'subtotal_overtime' => 0.0,
                    'subtotal_total' => 0.0,
                    'workers' => [],
                ];
            }

            $baseWage = (float) $entry->base_weekly_wage;
            $overtimeHours = (float) $entry->overtime_hours;
            $overtimeRate = (float) $entry->overtime_rate;
            $overtimePay = round($overtimeHours * $overtimeRate, 2);
            $totalPay = round($baseWage + $overtimePay, 2);

            // Calcular descuentos aplicados
            $totalDescuentos = $entry->discountApplications()->sum('monto_aplicado');
            $netPay = round($totalPay - $totalDescuentos, 2);

            $byWeek[$weekKey]['projects'][$projectId]['workers'][] = [
                'id' => $entry->id,
                'user_id' => $entry->user_id,
                'worker_name' => $entry->worker_name,
                'daily_wage' => (float) ($entry->daily_wage ?? 0),
                'base_weekly_wage' => $baseWage,
                'overtime_hours' => $overtimeHours,
                'overtime_rate' => $overtimeRate,
                'overtime_pay' => $overtimePay,
                'total_pay' => $totalPay,
                'total_discounts' => $totalDescuentos,
                'net_pay' => $netPay,
                'days_worked' => (int) ($entry->days_worked ?? 6),
                'notes' => $entry->notes ?? '',
            ];

            $byWeek[$weekKey]['projects'][$projectId]['subtotal_base'] += $baseWage;
            $byWeek[$weekKey]['projects'][$projectId]['subtotal_overtime'] += $overtimePay;
            $byWeek[$weekKey]['projects'][$projectId]['subtotal_total'] += $totalPay;
            $byWeek[$weekKey]['projects'][$projectId]['worker_count']++;

            $byWeek[$weekKey]['total_base'] += $baseWage;
            $byWeek[$weekKey]['total_overtime'] += $overtimePay;
            $byWeek[$weekKey]['total_pay'] += $totalPay;
        }

        // Finalize: round totals, convert maps to arrays, count unique workers
        foreach ($byWeek as &$week) {
            $week['total_base'] = round($week['total_base'], 2);
            $week['total_overtime'] = round($week['total_overtime'], 2);
            $week['total_pay'] = round($week['total_pay'], 2);

            $uniqueWorkers = [];
            foreach ($week['projects'] as &$project) {
                $project['subtotal_base'] = round($project['subtotal_base'], 2);
                $project['subtotal_overtime'] = round($project['subtotal_overtime'], 2);
                $project['subtotal_total'] = round($project['subtotal_total'], 2);
                foreach ($project['workers'] as $w) {
                    $uniqueWorkers[$w['worker_name']] = true;
                }
            }
            unset($project);

            $week['worker_count'] = count($uniqueWorkers);
            $week['projects'] = array_values($week['projects']);
        }
        unset($week);

        return response()->json([
            'data' => array_values($byWeek),
        ]);
    }

    /**
     * POST /api/v1/payroll
     *
     * Create a weekly payroll entry for a worker on a project.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'worker_name' => 'required|string|max:255',
            'date' => 'required|date',
            'base_weekly_wage' => 'required_without:daily_wage|numeric|min:0',
            'daily_wage' => 'required_without:base_weekly_wage|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'days_worked' => 'nullable|integer|min:0|max:7',
            'project_id' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $company = $user->company();
        $date = Carbon::parse($request->input('date'));

        $project_id = $request->input('project_id') ? $this->decodePrimaryKey($request->input('project_id')) : null;
        $daily_wage = (float) $request->input('daily_wage', 0);
        $days_worked = (int) $request->input('days_worked', 6);
        $base_weekly_wage = $request->has('daily_wage') ? round($daily_wage * $days_worked, 2) : (float) $request->input('base_weekly_wage');

        $entry = PayrollEntry::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'worker_name' => $request->input('worker_name'),
            'date' => $date->startOfWeek()->toDateString(),
            'daily_wage' => $daily_wage,
            'base_weekly_wage' => $base_weekly_wage,
            'overtime_hours' => $request->input('overtime_hours', 0),
            'overtime_rate' => $request->input('overtime_rate', 0),
            'days_worked' => $days_worked,
            'project_id' => $project_id,
            'notes' => $request->input('notes'),
            'week_number' => $date->isoWeek,
            'attended' => true,
        ]);

        // Aplicar descuentos activos del empleado automáticamente
        $totalDescuentos = $entry->aplicarDescuentos();

        return response()->json([
            'data' => $entry->fresh(), // Recargar para obtener net_pay actualizado
            'message' => 'Registro de nómina creado exitosamente.',
            'descuentos_aplicados' => $totalDescuentos,
        ], 201);
    }

    /**
     * PUT /api/v1/payroll/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $company = $user->company();

        $entry = PayrollEntry::where('company_id', $company->id)->findOrFail($id);

        $request->validate([
            'worker_name' => 'sometimes|string|max:255',
            'date' => 'sometimes|date',
            'base_weekly_wage' => 'nullable|numeric|min:0',
            'daily_wage' => 'nullable|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'days_worked' => 'nullable|integer|min:0|max:7',
            'project_id' => 'nullable', // Accept both string and integer
            'notes' => 'nullable|string|max:500',
        ]);

        $fillable = $request->only([
            'worker_name',
            'date',
            'daily_wage',
            'base_weekly_wage',
            'overtime_hours',
            'overtime_rate',
            'days_worked',
            'project_id',
            'notes',
        ]);

        if (isset($fillable['project_id']) && $fillable['project_id']) {
            // If it's already an integer, use it directly; otherwise decode the hash
            if (is_numeric($fillable['project_id'])) {
                $fillable['project_id'] = (int) $fillable['project_id'];
            } else {
                $fillable['project_id'] = $this->decodePrimaryKey($fillable['project_id']);
            }
        }

        // Recalculate base if daily_wage or days_worked changes
        if (isset($fillable['daily_wage']) || isset($fillable['days_worked'])) {
            $daily = (float) ($fillable['daily_wage'] ?? $entry->daily_wage);
            $days = (int) ($fillable['days_worked'] ?? $entry->days_worked);
            $fillable['base_weekly_wage'] = round($daily * $days, 2);
        }

        if (isset($fillable['date'])) {
            $date = Carbon::parse($fillable['date']);
            $fillable['date'] = $date->startOfWeek()->toDateString();
            $fillable['week_number'] = $date->isoWeek;
        }

        $entry->update($fillable);

        return response()->json([
            'data' => $entry->fresh(['project']),
            'message' => 'Registro de nómina actualizado.',
        ]);
    }

    /**
     * DELETE /api/v1/payroll/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $company = $user->company();

        $entry = PayrollEntry::where('company_id', $company->id)->findOrFail($id);
        $entry->delete();

        return response()->json([
            'message' => 'Registro de nómina eliminado.',
        ]);
    }

    /**
     * POST /api/v1/payroll/bulk
     *
     * Create multiple weekly entries at once.
     */
    public function bulkStore(Request $request): JsonResponse
    {
        $request->validate([
            'entries' => 'required|array|min:1',
            'entries.*.worker_name' => 'required|string|max:255',
            'entries.*.date' => 'required|date',
            'entries.*.base_weekly_wage' => 'required_without:entries.*.daily_wage|numeric|min:0',
            'entries.*.daily_wage' => 'required_without:entries.*.base_weekly_wage|numeric|min:0',
            'entries.*.overtime_hours' => 'nullable|numeric|min:0',
            'entries.*.overtime_rate' => 'nullable|numeric|min:0',
            'entries.*.days_worked' => 'nullable|integer|min:0|max:7',
            'entries.*.project_id' => 'nullable|string',
            'entries.*.notes' => 'nullable|string|max:500',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $company = $user->company();

        $created = [];
        foreach ($request->input('entries') as $data) {
            $date = Carbon::parse($data['date']);
            $project_id = isset($data['project_id']) && $data['project_id'] ? $this->decodePrimaryKey($data['project_id']) : null;
            $daily_wage = (float) ($data['daily_wage'] ?? 0);
            $days_worked = (int) ($data['days_worked'] ?? 6);
            $base_weekly_wage = isset($data['daily_wage']) ? round($daily_wage * $days_worked, 2) : (float) ($data['base_weekly_wage'] ?? 0);

            $created[] = PayrollEntry::create([
                'company_id' => $company->id,
                'user_id' => $user->id,
                'worker_name' => $data['worker_name'],
                'date' => $date->startOfWeek()->toDateString(),
                'daily_wage' => $daily_wage,
                'base_weekly_wage' => $base_weekly_wage,
                'overtime_hours' => $data['overtime_hours'] ?? 0,
                'overtime_rate' => $data['overtime_rate'] ?? 0,
                'days_worked' => $days_worked,
                'project_id' => $project_id,
                'notes' => $data['notes'] ?? null,
                'week_number' => $date->isoWeek,
                'attended' => true,
            ]);
        }

        return response()->json([
            'data' => $created,
            'message' => count($created) . ' registros de nómina creados.',
        ], 201);
    }

    /**
     * GET /api/v1/payroll/workers
     *
     * Returns distinct worker names for autocomplete.
     */
    public function workers(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $company = $user->company();

        $workers = PayrollEntry::where('company_id', $company->id)
            ->whereNull('deleted_at')
            ->distinct()
            ->pluck('worker_name')
            ->sort()
            ->values();

        return response()->json(['data' => $workers]);
    }
}
