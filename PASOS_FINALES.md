# Pasos Finales - Card "Pagos Pendientes del Mes"

## ✅ Completado

1. ✅ **Backend API creado** - Endpoint `/api/v1/dashboard/pending_payments`
2. ✅ **Componente React creado** - `PendingPayments.tsx`
3. ✅ **Integrado en Dashboard** - Card visible en el dashboard
4. ✅ **Traducciones agregadas** - Español (es_ES.json)
5. ✅ **Frontend compilado** - Build exitoso
6. ✅ **Archivos sincronizados** - Copiados a `public/build-admin`

---

## 🔧 Paso Final Requerido

### Ejecutar la migración de base de datos

La migración agregará las columnas necesarias para soportar parcialidades en facturas:
- `installment_count` - Número de parcialidades
- `installment_period` - Período (mensual, quincenal, etc.)es
- `installment_schedule` - JSON con el calendario de parcialidades

**Comando a ejecutar:**

```bash
cd /Users/dario.mata/Documents/Github/diabe-platform
php artisan migrate
```

Si `php` no está en el PATH, usa la ruta completa de tu instalación PHP, por ejemplo:
```bash
/usr/local/bin/php artisan migrate
# o
/Applications/MAMP/bin/php/php8.1.0/bin/php artisan migrate
```

---

## 📝 Siguiente: Crear Datos de Prueba

Una vez ejecutada la migración, puedes crear facturas de prueba con parcialidades.

### Ejemplo de factura con 12 parcialidades mensuales

```sql
-- Actualizar una factura existente con parcialidades
UPDATE invoices 
SET 
  installment_count = 12,
  installment_period = 'monthly',
  installment_schedule = '[
    {"number": 1, "due_date": "2026-01-15", "amount": 12000.00},
    {"number": 2, "due_date": "2026-02-15", "amount": 12000.00},
    {"number": 3, "due_date": "2026-03-15", "amount": 12000.00},
    {"number": 4, "due_date": "2026-04-15", "amount": 12000.00},
    {"number": 5, "due_date": "2026-05-15", "amount": 12000.00},
    {"number": 6, "due_date": "2026-06-15", "amount": 12000.00},
    {"number": 7, "due_date": "2026-07-15", "amount": 12000.00},
    {"number": 8, "due_date": "2026-08-15", "amount": 12000.00},
    {"number": 9, "due_date": "2026-09-15", "amount": 12000.00},
    {"number": 10, "due_date": "2026-10-15", "amount": 12000.00},
    {"number": 11, "due_date": "2026-11-15", "amount": 12000.00},
    {"number": 12, "due_date": "2026-12-15", "amount": 12000.00}
  ]'
WHERE id = 1; -- Reemplaza con el ID de tu factura
```

---

## 🎯 Verificar la Implementación

1. **Accede al dashboard** de la aplicación
2. **Verifica que aparezca la card** "Pagos pendientes"
3. **Debe mostrar:**
   - Título con ícono de calendario
   - Mes actual
   - Chips de resumen (Vencidos | Pendientes | Pagados)
   - Monto total pendiente (si hay parcialidades)
   - Lista de parcialidades con badges de estado

---

## 🔄 Desarrollo Futuro Recomendado

### 1. UI para Crear Acuerdos de Pago

Crear interfaz en el formulario de facturas para:
- Definir número de parcialidades
- Seleccionar período (mensual, quincenal, semanal)
- Generar automáticamente el calendario
- Editar fechas de vencimiento individuales

**Ubicación sugerida:**
- Componente: `diabe-ui/src/pages/invoices/edit/components/InstallmentSettings.tsx`
- Agregar tab o sección en el formulario de edición de factura

### 2. Mejorar Asignación de Pagos

Actualmente los pagos se asignan secuencialmente. Considerar:
- Agregar campo `installment_number` a la tabla `payments`
- Permitir asignar pagos a parcialidades específicas
- UI para ver qué parcialidad cubre cada pago

### 3. Notificaciones y Recordatorios

- Email automático X días antes del vencimiento
- Notificación en dashboard de parcialidades próximas a vencer
- Integración con el sistema de recordatorios existente

### 4. Reportes

- Reporte de parcialidades por cliente
- Proyección de flujo de efectivo basado en parcialidades
- Análisis de cumplimiento de pagos

---

## 📚 Documentación Completa

Ver `IMPLEMENTACION_PAGOS_PENDIENTES.md` para:
- Detalles técnicos completos
- Estructura de datos del API
- Reglas de negocio implementadas
- Casos de prueba recomendados

---

## 🐛 Troubleshooting

### La card no aparece en el dashboard

1. Verificar que el build se sincronizó correctamente:
   ```bash
   ls -la /Users/dario.mata/Documents/Github/diabe-platform/public/build-admin/react/
   ```
   Debe contener archivos recientes con fecha de hoy

2. Limpiar caché del navegador (Cmd+Shift+R en Chrome/Firefox)

3. Verificar que el módulo de facturas esté habilitado en la configuración

### El endpoint retorna error 500

1. Verificar que la migración se ejecutó correctamente:
   ```bash
   php artisan migrate:status
   ```

2. Revisar logs de Laravel:
   ```bash
   tail -f storage/logs/laravel.log
   ```

### No aparecen parcialidades

1. Verificar que las facturas tengan `installment_count > 0`
2. Verificar que `installment_schedule` sea un JSON válido
3. Verificar que las fechas estén en formato `YYYY-MM-DD`

---

**Fecha:** 23 de marzo de 2026  
**Estado:** ✅ Implementación completa - Solo falta ejecutar migración
