<?php

namespace App\Http\Controllers;

use App\Models\EmployeeDiscount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeDiscountController extends Controller
{
    public function index($employeeId)
    {
        $discounts = EmployeeDiscount::forEmployee($employeeId)
            ->with(['applications', 'creator'])
            ->orderBy('estado', 'asc')
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return response()->json($discounts);
    }

    public function store(Request $request, $employeeId)
    {
        $validated = $request->validate([
            'descripcion' => 'required|string|min:3|max:255',
            'monto_total' => 'required|numeric|min:0.01',
            'descuento_semanal' => 'required|numeric|min:0.01',
            'fecha_inicio' => 'required|date',
            'notas' => 'nullable|string|max:1000',
        ]);

        // Validar que descuento_semanal no sea mayor que monto_total
        if ($validated['descuento_semanal'] > $validated['monto_total']) {
            return response()->json([
                'message' => 'El descuento semanal no puede ser mayor que el monto total',
            ], 422);
        }

        // Validar que el empleado existe
        $employee = User::findOrFail($employeeId);

        // Crear el descuento
        $discount = EmployeeDiscount::create([
            'employee_id' => $employeeId,
            'descripcion' => $validated['descripcion'],
            'monto_total' => $validated['monto_total'],
            'descuento_semanal' => $validated['descuento_semanal'],
            'fecha_inicio' => $validated['fecha_inicio'],
            'notas' => $validated['notas'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return response()->json($discount->load(['applications', 'creator']), 201);
    }

    public function update(Request $request, $employeeId, $discountId)
    {
        $discount = EmployeeDiscount::forEmployee($employeeId)->findOrFail($discountId);

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

        $discount->update($validated);

        return response()->json($discount->load(['applications', 'creator']));
    }

    public function destroy($employeeId, $discountId)
    {
        $discount = EmployeeDiscount::forEmployee($employeeId)->findOrFail($discountId);

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
