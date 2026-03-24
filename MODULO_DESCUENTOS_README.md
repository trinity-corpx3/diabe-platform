# Módulo de Descuentos de Empleados - Guía de Integración

## Resumen

Este módulo permite registrar y aplicar descuentos recurrentes a empleados en la nómina semanal (créditos, préstamos, anticipos, etc.).

## Backend Implementado

### Migraciones
- `2026_03_24_000001_create_employee_discounts_table.php`
- `2026_03_24_000002_create_payroll_discount_applications_table.php`

### Modelos
- `EmployeeDiscount` - Gestión de descuentos
- `PayrollDiscountApplication` - Registro de aplicaciones
- `PayrollEntry` - Actualizado con método `aplicarDescuentos()`

### Endpoints API
```
GET    /api/v1/employees/{id}/discounts
POST   /api/v1/employees/{id}/discounts
PATCH  /api/v1/employees/{id}/discounts/{discountId}
DELETE /api/v1/employees/{id}/discounts/{discountId}
GET    /api/v1/employees/{id}/discounts/pending-balance
GET    /api/v1/payroll/discounts/summary
```

### Permisos
Solo usuarios con rol `admin` o `rh` pueden gestionar descuentos.

## Frontend Implementado

### Componentes React
```
/diabe-ui/src/pages/nomina/common/
├── hooks/
│   └── useEmployeeDiscounts.ts
└── components/
    ├── DiscountCard.tsx
    ├── DiscountForm.tsx
    ├── DiscountsList.tsx
    ├── DiscountsSummaryChips.tsx
    ├── EmployeeDiscountsModal.tsx
    └── index.ts
```

### Traducciones
Agregadas en `/diabe-ui/src/resources/lang/es/es.json`

## Integración en Vista de Nómina

### Opción 1: Modal desde nombre de empleado

En `Nomina.tsx`, agregar:

```tsx
import { EmployeeDiscountsModal } from './common/components';
import { DiscountsSummaryChips } from './common/components';

// En el estado del componente:
const [selectedEmployee, setSelectedEmployee] = useState<{
  id: string;
  name: string;
  salary: number;
} | null>(null);

// En el encabezado (antes de la tabla de nómina):
<DiscountsSummaryChips week={currentWeekKey} />

// En la tabla, hacer el nombre del empleado clickeable:
<button
  onClick={() => setSelectedEmployee({
    id: worker.user_id,
    name: worker.worker_name,
    salary: worker.base_weekly_wage
  })}
  className="text-blue-600 hover:underline"
>
  {worker.worker_name}
</button>

// Al final del componente:
{selectedEmployee && (
  <EmployeeDiscountsModal
    visible={true}
    onClose={() => setSelectedEmployee(null)}
    employeeId={selectedEmployee.id}
    employeeName={selectedEmployee.name}
    employeeSalary={selectedEmployee.salary}
  />
)}
```

### Opción 2: Sección en vista de empleado individual

Si existe una vista detallada de empleado:

```tsx
import { DiscountsList } from '$app/pages/nomina/common/components';

<DiscountsList 
  employeeId={employee.id} 
  employeeSalary={employee.base_weekly_wage} 
/>
```

## Aplicación Automática de Descuentos

En el proceso de nómina semanal, después de calcular el pago base:

```php
// En el controlador o servicio de nómina
$payrollEntry = PayrollEntry::create([...]);

// Aplicar descuentos automáticamente
$totalDescuentos = $payrollEntry->aplicarDescuentos();

// El neto final está disponible en:
$netoFinal = $payrollEntry->net_pay;
```

## Pasos Pendientes

1. **Ejecutar migraciones en el servidor:**
   ```bash
   php artisan migrate
   ```

2. **Registrar Policy en AuthServiceProvider:**
   ```php
   // En app/Providers/AuthServiceProvider.php
   protected $policies = [
       EmployeeDiscount::class => EmployeeDiscountPolicy::class,
   ];
   ```

3. **Build y deploy del frontend:**
   ```bash
   cd diabe-ui
   npm run build
   bash sync-to-platform.sh
   ```

4. **Integrar en vista de nómina** según la opción elegida arriba

5. **Agregar llamada a `aplicarDescuentos()`** en el flujo de procesamiento de nómina

## Flujo de Retención en Liquidación (Pendiente)

Cuando un empleado es dado de baja, el sistema debe:

1. Detectar descuentos pendientes
2. Mostrar resumen al admin
3. Permitir retener saldo en liquidación final
4. Marcar descuentos como liquidados o cancelados

Este flujo se implementará en el módulo de liquidación/baja de empleados.

## Notas Técnicas

- Los descuentos se aplican en orden de `fecha_inicio` ASC
- Nunca dejan el neto en negativo
- Si el neto no alcanza, se pospone para la siguiente semana
- Los descuentos pausados no se aplican pero mantienen su saldo
- Los descuentos liquidados se muestran colapsados en la UI

## Soporte

Para dudas o problemas con este módulo, contactar al equipo de desarrollo.
