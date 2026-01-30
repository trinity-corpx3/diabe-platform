# Guía de Configuración Invoice Ninja - C. Constructora

Esta guía detalla los cambios realizados y los pasos necesarios para finalizar la configuración de la instancia Self-Hosted para la empresa constructora.

## 1. Comandos de Optimización (PHP Artisan)

Ejecute los siguientes comandos en la raíz del proyecto para aplicar los cambios de idioma y limpiar la caché:

```bash
# Limpiar caché de aplicación y configuración
php artisan optimize:clear

# Importar traducciones (CRÍTICO para que los cambios en texts.php/json se reflejen)
php artisan ninja:import-translations

# Re-cachear configuración (Opcional, para producción)
php artisan config:cache
```

## 2. Variables de Entorno (.env)

Para optimizar la generación de PDFs con **Snappdf** (generación local rápida sin depender de servicios externos), agregue o modifique estas variables en su archivo `.env`:

```ini
# Configuración PDF - Snappdf
PDF_GENERATOR=snappdf
SNAPPDF_CHROMIUM_PATH=/usr/bin/google-chrome-stable  # Ajustar según la ruta de su sistema (ej. /usr/bin/chromium)
# Argumentos para entornos sin interfaz gráfica (Servidores Linux/Docker)
SNAPPDF_CHROMIUM_ARGUMENTS="--no-sandbox --disable-setuid-sandbox --disable-gpu --disable-dev-shm-usage"

# Desactivar módulos no deseados (Opcional/Visual)
# Esto oculta funcionalidad en ciertas vistas, pero lo ideal es hacerlo desde Ajustes > Módulos
PHANTOMJS_PDF_GENERATION=false
```

## 3. Snippet JSON de Traducción

Se ha creado el archivo `lang/es_ES.json` con las siguientes definiciones. Este snippet puede ser utilizado también si necesita inyectar traducciones en el frontend de React o en archivos de configuración externos.

```json
{
    "project": "Obra",
    "projects": "Obras",
    "vendor": "Proveedor",
    "vendors": "Base de Datos Proveedores",
    "quote": "Presupuesto / Estimación",
    "quotes": "Presupuestos",
    "quote_number": "Número de Presupuesto",
    "product": "Insumo",
    "products": "Catálogo de Insumos",
    "product_library": "Catálogo de Insumos"
}
```

## 4. Configuración Manual (UI)

Debido a que los **Campos Personalizados** y la configuración del **Dashboard** se almacenan en la base de datos, deben configurarse manualmente desde el panel de administración:

### A. Campos Personalizados (Settings > Custom Fields)
1.  **Projects (Obras)**:
    *   **Campo Fecha 1**: Etiqueta `Fecha Inicio Obra`
    *   **Campo Fecha 2**: Etiqueta `Fecha Entrega Pactada`
    *   **Campo Selección (Dropdown)**: Etiqueta `Estatus Financiero`
        *   Opciones: `En Presupuesto,Desviación,Crítico`
2.  **Invoices (Facturas)**:
    *   **Campo Texto 1**: Etiqueta `Referencia Bancaria`

### B. Dashboard
1.  Ir a **Settings > Account Management > Overview / Dashboard**.
2.  Desactivar "Task Scrubber" o métricas de tiempo.
3.  Activar **Profit & Loss** (Ingresos vs Egresos).
4.  Asegurar que el gráfico muestre desglose por Proyecto (Obra) si la versión lo permite.

---
**Nota**: Los archivos de idioma `lang/es_ES/texts.php` han sido modificados directamente para asegurar compatibilidad con el backend de generación de PDFs.
