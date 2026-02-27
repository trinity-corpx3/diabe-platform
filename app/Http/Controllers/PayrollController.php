<?php

/**
 * Payroll Controller — Nómina Operativa.
 *
 * Provides CRUD for daily payroll entries and weekly summaries
 * with worker identification and project assignment.
 */

namespace App\Http\Controllers;

use App\Models\PayrollEntry;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class PayrollController extends BaseController
{
    /**
     * GET /api/v1/payroll
     *
     * List payroll entries. Supports filtering by:
     *  - week (ISO week number, e.g. ?week=9)
     *  - year (e.g. ?year=2026)
     *  - worker_name (e.g. ?worker_name=Juan)
     *  - project_id (e.g. ?project_id=2)
     *
     * Returns entries grouped by week with weekly totals.
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

        if ($request->has('project_id')) {
            $query->where('project_id', $request->input('project_id'));
        }

        $entries = $query->get();

        // Group by week
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
                    'total_wages' => 0.0,
                    'days_worked' => 0,
                    'days_absent' => 0,
                    'worker_count' => 0,
                    'workers' => [],
                    'entries' => [],
                ];
            }

            $workerName = $entry->worker_name;
            if (!isset($byWeek[$weekKey]['workers'][$workerName])) {
                $byWeek[$weekKey]['workers'][$workerName] = [
                    'worker_name' => $workerName,
                    'total_wage' => 0.0,
                    'days_worked' => 0,
                    'days_absent' => 0,
                    'daily' => [],
                ];
            }

            $dayData = [
                'id' => $entry->id,
                'date' => $entry->date->toDateString(),
                'day_name' => $this->spanishDayName($entry->date->dayOfWeek),
                'daily_wage' => round((float) $entry->daily_wage, 2),
                'attended' => (bool) $entry->attended,
                'project_id' => $entry->project_id,
                'project_name' => $entry->project ? $entry->project->name : 'Sin Proyecto',
                'notes' => $entry->notes ?? '',
            ];

            $byWeek[$weekKey]['workers'][$workerName]['daily'][] = $dayData;

            if ($entry->attended) {
                $byWeek[$weekKey]['total_wages'] += (float) $entry->daily_wage;
                $byWeek[$weekKey]['days_worked']++;
                $byWeek[$weekKey]['workers'][$workerName]['total_wage'] += (float) $entry->daily_wage;
                $byWeek[$weekKey]['workers'][$workerName]['days_worked']++;
            } else {
                $byWeek[$weekKey]['days_absent']++;
                $byWeek[$weekKey]['workers'][$workerName]['days_absent']++;
            }
        }

        // Finalize: convert workers map to array and round totals
        foreach ($byWeek as &$week) {
            $week['worker_count'] = count($week['workers']);
            $week['total_wages'] = round($week['total_wages'], 2);
            foreach ($week['workers'] as &$worker) {
                $worker['total_wage'] = round($worker['total_wage'], 2);
            }
            $week['workers'] = array_values($week['workers']);
        }

        return response()->json([
            'data' => array_values($byWeek),
        ]);
    }

    /**
     * POST /api/v1/payroll
     *
     * Create a new daily payroll entry for a worker.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'worker_name' => 'required|string|max:255',
            'date' => 'required|date',
            'daily_wage' => 'required|numeric|min:0',
            'project_id' => 'nullable|integer',
            'attended' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $company = $user->company();
        $date = Carbon::parse($request->input('date'));

        $entry = PayrollEntry::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'worker_name' => $request->input('worker_name'),
            'date' => $date->toDateString(),
            'daily_wage' => $request->input('daily_wage'),
            'project_id' => $request->input('project_id'),
            'attended' => $request->input('attended', true),
            'notes' => $request->input('notes'),
            'week_number' => $date->isoWeek,
        ]);

        return response()->json([
            'data' => $entry,
            'message' => 'Registro de nómina creado exitosamente.',
        ], 201);
    }

    /**
     * PUT /api/v1/payroll/{id}
     *
     * Update an existing payroll entry (e.g. change project, attendance, wage).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $company = $user->company();

        $entry = PayrollEntry::where('company_id', $company->id)
            ->findOrFail($id);

        $request->validate([
            'worker_name' => 'sometimes|string|max:255',
            'date' => 'sometimes|date',
            'daily_wage' => 'sometimes|numeric|min:0',
            'project_id' => 'nullable|integer',
            'attended' => 'sometimes|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $fillable = $request->only([
            'worker_name',
            'date',
            'daily_wage',
            'project_id',
            'attended',
            'notes',
        ]);

        if (isset($fillable['date'])) {
            $fillable['date'] = Carbon::parse($fillable['date'])->toDateString();
            $fillable['week_number'] = Carbon::parse($fillable['date'])->isoWeek;
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
     * Create multiple entries at once (e.g. fill an entire week for a worker).
     */
    public function bulkStore(Request $request): JsonResponse
    {
        $request->validate([
            'entries' => 'required|array|min:1',
            'entries.*.worker_name' => 'required|string|max:255',
            'entries.*.date' => 'required|date',
            'entries.*.daily_wage' => 'required|numeric|min:0',
            'entries.*.project_id' => 'nullable|integer',
            'entries.*.attended' => 'boolean',
            'entries.*.notes' => 'nullable|string|max:500',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $company = $user->company();

        $created = [];
        foreach ($request->input('entries') as $data) {
            $date = Carbon::parse($data['date']);
            $created[] = PayrollEntry::create([
                'company_id' => $company->id,
                'user_id' => $user->id,
                'worker_name' => $data['worker_name'],
                'date' => $date->toDateString(),
                'daily_wage' => $data['daily_wage'],
                'project_id' => $data['project_id'] ?? null,
                'attended' => $data['attended'] ?? true,
                'notes' => $data['notes'] ?? null,
                'week_number' => $date->isoWeek,
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
     * Returns a list of distinct worker names for autocomplete.
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

    /**
     * Helper: Translate day of week number to Spanish name.
     */
    private function spanishDayName(int $dayOfWeek): string
    {
        return match ($dayOfWeek) {
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            default => '',
        };
    }
}
