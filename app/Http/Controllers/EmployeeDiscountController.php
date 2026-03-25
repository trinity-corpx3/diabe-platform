<?php

namespace App\Http\Controllers;

use App\Models\EmployeeDiscount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeDiscountController extends Controller
{
    public function index($workerName)
    {
        // Decodificar el nombre del trabajador (viene URL encoded)
        $workerName = urldecode($workerName);
        
        $discounts = EmployeeDiscount::whereRaw('LOWER(TRIM(worker_name)) = ?', [strtolower(trim($workerName))])
            ->with(['applications', 'creator'])
            ->orderBy('estado', 'asc')
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return response()->json($discounts);
    }

    public function store(Request $request, $workerName)
    {
        // Decodificar el nombre del trabajador (viene URL encoded)
        $workerName = urldecode($workerName);
        
        $validated = $request->validate([
            'descripcion' => 'required|string|min:3|max:255',
            'descuento_semanal' => 'required|numeric|min:0.01',
            'fecha_inicio' => 'nullable|date',
            'notas' => 'nullable|string|max:1000',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Crear el descuento usando worker_name directamente
        $discount = EmployeeDiscount::create([
            'worker_name' => trim($workerName),
            'descripcion' => $validated['descripcion'],
            'monto_total' => $validated['descuento_semanal'],
            'descuento_semanal' => $validated['descuento_semanal'],
            'fecha_inicio' => $validated['fecha_inicio'] ?? now()->format('Y-m-d'),
            'notas' => $validated['notas'] ?? null,
            'created_by' => $user->id,
        ]);

        // Aplicar el descuento a registros de nómina existentes del trabajador
        $payrollEntries = \App\Models\PayrollEntry::whereRaw('LOWER(TRIM(worker_name)) = ?', [strtolower(trim($workerName))])
            ->whereDoesntHave('discountApplications')
            ->get();

        foreach ($payrollEntries as $entry) {
            $entry->aplicarDescuentos();
        }

        return response()->json($discount->load(['applications', 'creator']), 201);
    }

    public function update(Request $request, $workerName, $discountId)
    {
        // Decodificar el nombre del trabajador
        $workerName = urldecode($workerName);
        
        $discount = EmployeeDiscount::whereRaw('LOWER(TRIM(worker_name)) = ?', [strtolower(trim($workerName))])
            ->findOrFail($discountId);

        $validated = $request->validate([
            'descripcion' => 'sometimes|string|min:3|max:255',
            'descuento_semanal' => 'sometimes|numeric|min:0.01',
            'estado' => 'sometimes|in:activo,pausado,liquidado,cancelado',
            'notas' => 'nullable|string|max:1000',
        ]);

        // No permitir modificar descuento_semanal si ya tiene aplicaciones
        if (isset($validated['descuento_semanal']) && $discount->applications()->count() > 0) {
            return response()->json([
                'message' => 'No se puede modificar el descuento semanal porque ya tiene aplicaciones registradas',
            ], 422);
        }

        // Si se actualiza descuento_semanal, también actualizar monto_total
        if (isset($validated['descuento_semanal'])) {
            $validated['monto_total'] = $validated['descuento_semanal'];
        }

        $discount->update($validated);

        return response()->json($discount->load(['applications', 'creator']));
    }

    public function destroy($workerName, $discountId)
    {
        // Decodificar el nombre del trabajador
        $workerName = urldecode($workerName);
        
        $discount = EmployeeDiscount::whereRaw('LOWER(TRIM(worker_name)) = ?', [strtolower(trim($workerName))])
            ->findOrFail($discountId);

        // Si tiene aplicaciones, no permitir eliminación física
        if ($discount->applications()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar este descuento porque ya tiene aplicaciones registradas. Considere pausarlo en su lugar.',
            ], 409);
        }

        $discount->delete();

        return response()->json(['message' => 'Descuento eliminado correctamente'], 200);
    }

    public function summary(Request $request)
    {
        $week = $request->query('week'); // Formato: YYYY-WXX

        // Obtener todos los descuentos activos
        $activeDiscounts = EmployeeDiscount::active()->get();

        $totalDescuentos = $activeDiscounts->sum('descuento_semanal');
        $numeroDescuentosActivos = $activeDiscounts->count();
        $empleadosConDescuentos = $activeDiscounts->pluck('employee_id')->unique()->count();

        return response()->json([
            'total_descuentos_semana' => $totalDescuentos,
            'numero_descuentos_activos' => $numeroDescuentosActivos,
            'empleados_con_descuentos' => $empleadosConDescuentos,
        ]);
    }

    public function pendingBalance($employeeId)
    {
        $discounts = EmployeeDiscount::forEmployee($employeeId)
            ->where('estado', 'activo')
            ->where('saldo_restante', '>', 0)
            ->get(['id', 'descripcion', 'saldo_restante']);

        $totalPendiente = $discounts->sum('saldo_restante');

        return response()->json([
            'total_pendiente' => $totalPendiente,
            'descuentos' => $discounts,
        ]);
    }
}
