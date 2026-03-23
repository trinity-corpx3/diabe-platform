# Implementación: Card "Pagos Pendientes del Mes"

## Resumen
Se ha implementado una nueva card en el dashboard para mostrar los pagos pendientes del mes derivados de facturas con acuerdos de pago en parcialidades.

---

## Archivos Creados/Modificados

### Backend (diabe-platform)

#### 1. Migración de Base de Datos
**Archivo:** `database/migrations/2026_03_23_000001_add_payment_installments_to_invoices.php`

Agrega tres nuevas columnas a la tabla `invoices`:
- `installment_count` (int): Número total de parcialidades
- `installment_period` (string): Período de las parcialidades (mensual, días, etc.)
- `installment_schedule` (json): Calendario de parcialidades con fechas de vencimiento

**Acción requerida:** Ejecutar la migración
```bash
php artisan migrate
```

#### 2. Controlador de Dashboard
**Archivo:** `app/Http/Controllers/DashboardController.php`

Nuevo controlador con el método `pendingPayments()` que:
- Calcula las parcialidades pendientes del mes actual
- Determina el estado de cada parcialidad (vencido, pendiente, pagado, parcial)
- Cruza los pagos registrados con las parcialidades
- Ordena por prioridad: vencidos primero, luego pendientes, luego pagados
- Retorna resumen con contadores y monto total pendiente

**Endpoint:** `GET /api/v1/dashboard/pending_payments?month=YYYY-MM`

#### 3. Ruta API
**Archivo:** `routes/api.php`

Agregada la ruta:
```php
Route::get('dashboard/pending_payments', [DashboardController::class, 'pendingPayments'])
    ->name('dashboard.pending_payments');
```

#### 4. Traducciones
**Archivo:** `lang/es_ES.json`

Agregadas las siguientes traducciones:
- `pending_payments`: "Pagos pendientes"
- `installment`: "Parcialidad"
- `installments`: "Parcialidades"
- `total_pending_amount`: "Total pendiente del mes"
- `for`: "para"
- `last_updated`: "Última actualización"

### Frontend (diabe-ui)

#### 1. Componente PendingPayments
**Archivo:** `src/pages/dashboard/components/PendingPayments.tsx`

Componente React que:
- Consume el endpoint de pagos pendientes
- Muestra resumen con chips de contadores (vencidos, pendientes, pagados)
- Muestra monto total pendiente del mes
- Lista las parcialidades con:
  - Nombre del cliente
  - Número de factura + número de parcialidad
  - Fecha de vencimiento
  - Monto
  - Badge de estado con colores semánticos
- Actualización automática cada 5 minutos
- Timestamp de última actualización

#### 2. Integración en Dashboard
**Archivo:** `src/pages/dashboard/Dashboard.tsx`

- Importado el componente `PendingPayments`
- Agregado a la grilla del dashboard después de `RecentPayments`
- Condicionado al módulo de facturas (`ModuleBitmask.Invoices`)

---

## Estructura de Datos

### Respuesta del Endpoint

```json
{
  "data": [
    {
      "invoice_id": "abc123",
      "invoice_number": "0001",
      "client_id": "xyz789",
      "client_name": "Arabela",
      "installment_number": 3,
      "installment_total": 12,
      "due_date": "2026-03-15",
      "amount": 12000.00,
      "paid_amount": 0.00,
      "pending_amount": 12000.00,
      "status": "pending",
      "currency_id": "1"
    }
  ],
  "summary": {
    "total_pending": 24000.00,
    "count_overdue": 1,
    "count_pending": 2,
    "count_paid": 0,
    "count_partial": 0
  },
  "month": "2026-03",
  "last_updated": "2026-03-23T15:30:00-06:00"
}
```

### Estados de Parcialidad

- **VENCIDO** (`overdue`): Fecha de vencimiento pasada sin pago - Badge rojo
- **PENDIENTE** (`pending`): Fecha de vencimiento futura en el mes - Badge azul claro
- **PAGADO** (`paid`): Pago completo registrado - Badge verde
- **PARCIAL** (`partial`): Pago parcial registrado - Badge amarillo

---

## Pasos Siguientes Necesarios

### 1. Compilar el Frontend
```bash
cd /Users/dario.mata/Documents/Github/diabe-ui
npm run build
```

### 2. Sincronizar Build con Backend
Copiar los archivos compilados de `diabe-ui/dist` a `diabe-platform/public/build-admin`

### 3. Ejecutar Migración
```bash
cd /Users/dario.mata/Documents/Github/diabe-platform
php artisan migrate
```

### 4. Crear Interfaz para Acuerdos de Pago

**Pendiente:** Crear UI para que los usuarios puedan:
- Definir acuerdos de pago al crear/editar facturas
- Especificar número de parcialidades
- Elegir período (mensual, quincenal, etc.)
- Generar automáticamente el calendario de parcialidades

**Ubicación sugerida:** 
- Componente: `diabe-ui/src/pages/invoices/edit/components/InstallmentSettings.tsx`
- Agregar sección en el formulario de factura

### 5. Poblar Datos de Prueba

Para probar la funcionalidad, crear facturas con el siguiente formato en `installment_schedule`:

```json
[
  {
    "number": 1,
    "due_date": "2026-02-15",
    "amount": 12000.00
  },
  {
    "number": 2,
    "due_date": "2026-03-15",
    "amount": 12000.00
  },
  {
    "number": 3,
    "due_date": "2026-04-15",
    "amount": 12000.00
  }
]
```

### 6. Agregar Validaciones

- Validar que `installment_count` coincida con el array de `installment_schedule`
- Validar que la suma de parcialidades sea igual al monto total de la factura
- Validar fechas de vencimiento en orden cronológico

### 7. Mejorar Cálculo de Pagos

El método `calculatePaidAmountForInstallment()` actualmente asigna pagos en orden secuencial. Considerar:
- Permitir pagos específicos a parcialidades específicas
- Agregar campo `installment_number` a la tabla `payments`
- Mejorar la lógica de asignación de pagos

### 8. Agregar Iconos

El componente usa `CalendarClock` que debe estar disponible en:
`diabe-ui/src/components/icons/CalendarClock.tsx`

Si no existe, crear o usar un icono alternativo.

---

## Reglas de Negocio Implementadas

1. **Cálculo de parcialidad mensual:**
   ```
   monto_parcialidad = total_factura / numero_parcialidades
   ```

2. **Período mostrado:**
   - Prioridad: mes en curso
   - Incluye parcialidades vencidas de meses anteriores
   - Parámetro `month` permite navegar a otros meses

3. **Ordenamiento:**
   - Vencidos (por fecha descendente)
   - Pendientes (por fecha ascendente)
   - Pagados (al final)

4. **Relación con pagos:**
   - Los pagos se asignan secuencialmente a las parcialidades
   - Solo se consideran pagos con estado `COMPLETED` o `PARTIALLY_REFUNDED`

---

## Notas Técnicas

### TypeScript
Hay advertencias menores de TypeScript en `PendingPayments.tsx` relacionadas con tipos de `currency_id`. Estas no afectan la funcionalidad pero pueden corregirse convirtiendo explícitamente a string.

### Reactividad
El componente se actualiza automáticamente cuando:
- Se registra un nuevo pago (vía socket event `InvoiceWasPaid`)
- Cada 5 minutos (staleTime de React Query)

### Responsive
La card está optimizada para:
- Desktop: muestra todas las columnas
- Mobile: colapsa columnas secundarias (pendiente implementar en CSS)

---

## Testing

### Casos de Prueba Recomendados

1. **Factura sin parcialidades:** No debe aparecer en la lista
2. **Factura con 12 parcialidades mensuales:** Debe mostrar solo la del mes actual
3. **Parcialidad vencida:** Debe aparecer con badge rojo
4. **Parcialidad pagada completamente:** Debe aparecer con badge verde
5. **Parcialidad pagada parcialmente:** Debe aparecer con badge amarillo
6. **Mes sin parcialidades:** Debe mostrar estado vacío
7. **Cambio de mes:** Debe actualizar la lista correctamente

---

## Compatibilidad

- Laravel 9+
- React 18+
- TypeScript 4+
- TailwindCSS 3+

---

**Fecha de implementación:** 23 de marzo de 2026  
**Autor:** Sistema Cascade AI  
**Versión:** 1.0
