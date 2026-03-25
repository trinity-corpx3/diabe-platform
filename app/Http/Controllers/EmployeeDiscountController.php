<?php

namespace App\Http\Controllers;

use App\Models\EmployeeDiscount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeDiscountController extends Controller
{
    public function index($payrollEntryId)
    {
        $discounts = EmployeeDiscount::forPayrollEntry($payrollEntryId)
            ->with(['creator', 'payrollEntry'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($discounts);
    }

    public function store(Request $request, $payrollEntryId)
    {
        $validated = $request->validate([
            'descripcion' => 'required|string|min:3|max:255',
            'monto' => 'required|numeric|min:0.01',
            'notas' => 'nullable|string|max:1000',
        ]);

        // Verificar que el registro de nómina existe
        $payrollEntry = \App\Models\PayrollEntry::findOrFail($payrollEntryId);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Crear el descuento vinculado al registro específico
        $discount = EmployeeDiscount::create([
            'payroll_entry_id' => $payrollEntryId,
            'worker_name' => $payrollEntry->worker_name,
            'descripcion' => $validated['descripcion'],
            'monto' => $validated['monto'],
            'notas' => $validated['notas'] ?? null,
            'created_by' => $user->id,
        ]);

        return response()->json($discount->load(['creator', 'payrollEntry']), 201);
    }

    public function update(Request $request, $payrollEntryId, $discountId)
    {
        $discount = EmployeeDiscount::forPayrollEntry($payrollEntryId)
            ->findOrFail($discountId);

        $validated = $request->validate([
            'descripcion' => 'sometimes|string|min:3|max:255',
            'monto' => 'sometimes|numeric|min:0.01',
            'notas' => 'nullable|string|max:1000',
        ]);

        $discount->update($validated);

        return response()->json($discount->load(['creator', 'payrollEntry']));
    }

    public function destroy($payrollEntryId, $discountId)
    {
        $discount = EmployeeDiscount::forPayrollEntry($payrollEntryId)
            ->findOrFail($discountId);

        $discount->delete();

        return response()->json(null, 204);
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
