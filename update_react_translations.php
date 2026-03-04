<?php
/**
 * Script to update React translation JSON files.
 * Uses string replacement to ensure "Vendedores" -> "Proveedores" and "CIF/NIF" -> "RFC".
 */

// Define the directory containing the React translation JSON files
$dir = __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;

// Find all Spanish translation files (check both root public/ and public/react/)
$files = array_merge(
    glob($dir . 'es*.json'),
    glob($dir . 'react' . DIRECTORY_SEPARATOR . 'es*.json')
);

echo "Found " . count($files) . " files to process.\n";

$replacements = [
    'Vendedores' => 'Proveedores',
    'Vendedor' => 'Proveedor',
    'vendedores' => 'proveedores',
    'vendedor' => 'proveedor',
    'CIF\\/NIF' => 'RFC', // Escaped slash in JSON
    'CIF/NIF' => 'RFC',   // Literal slash
    'CIF\\\\/NIF' => 'RFC', // Triple escaped
    'NIF' => 'RFC',
    'CIF' => 'RFC',
    'VAT Number' => 'RFC',
    // NOTE: Do NOT replace 'vat_number' key, only its value
    '"vat_number":"VAT Number"' => '"vat_number":"RFC"',
    '"vat_number":"CIF\\/NIF"' => '"vat_number":"RFC"',
    '"vat_number":"CIF/NIF"' => '"vat_number":"RFC"',
];

foreach ($files as $file) {
    if (!file_exists($file))
        continue;

    echo "Processing " . basename($file) . "...\n";
    $content = file_get_contents($file);
    if ($content === false) {
        echo "Error reading file: $file\n";
        continue;
    }

    $new_content = $content;
    foreach ($replacements as $search => $replace) {
        $count = 0;
        $new_content = str_replace($search, $replace, $new_content, $count);
        if ($count > 0) {
            echo "  Replaced '$search' with '$replace' ($count times)\n";
        }
    }

    // Decode and inject missing translation keys dynamically
    $json = json_decode($new_content, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
        $json['total_paid'] = 'Total Pagado';
        $json['total_expenses'] = 'Total Gastos';

        // Add exact match literal overwrites for hardcoded displays
        $json['TOTAL_PAID'] = 'Total Pagado';
        $json['TOTAL EXPENSES'] = 'Total Gastos';

        // Cuentas por Pagar (AP Aging)
        $json['cuentas_por_pagar'] = 'Cuentas por Pagar';
        $json['al_corriente'] = 'Al Corriente';
        $json['proximo_a_vencer'] = 'Próximo a Vencer';
        $json['vencido'] = 'Vencido';
        $json['pending'] = 'Pendiente';
        $json['days'] = 'Días';

        // Nómina (Payroll)
        $json['nomina'] = 'Nómina';
        $json['salarios'] = 'Salarios';
        $json['salario_diario'] = 'Salario Diario';
        $json['salario'] = 'Salario';
        $json['impuesto_estatal'] = 'Imp. Estatal';
        $json['neto_estimado'] = 'Neto Estimado';
        $json['nuevo_registro'] = 'Nuevo Registro';
        $json['worker_name'] = 'Nombre del Trabajador';
        $json['sin_proyecto'] = 'Sin Proyecto';
        $json['asistio'] = 'Asistió';
        $json['asistencia'] = 'Asistencia';
        $json['semana'] = 'Semana';
        $json['trabajadores'] = 'Trabajadores';
        $json['bruto'] = 'Bruto';
        $json['impuestos'] = 'Impuestos';
        $json['neto'] = 'Neto';
        $json['day'] = 'Día';
        $json['nomina_empty_hint'] = 'Haz clic en "Nuevo Registro" para capturar la primera entrada de nómina.';

        // Office Expenses (Gastos Oficina)
        $json['office_expenses'] = 'Gastos Oficina';
        $json['office_expense'] = 'Gasto Oficina';
        $json['new_office_expense'] = 'Nuevo Gasto Oficina';
        $json['category'] = 'Categoría';

        $new_content = json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    if ($new_content !== $content) {
        if (file_put_contents($file, $new_content) !== false) {
            echo "Successfully updated " . basename($file) . "\n";
        } else {
            echo "Error writing to file: $file\n";
        }
    } else {
        echo "  No changes needed for " . basename($file) . "\n";
    }
}

echo "Done!\n";
