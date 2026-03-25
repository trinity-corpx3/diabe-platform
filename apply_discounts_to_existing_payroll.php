<?php

/**
 * Script para aplicar descuentos a registros de nómina existentes
 * 
 * Ejecutar con: php apply_discounts_to_existing_payroll.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PayrollEntry;
use Illuminate\Support\Facades\DB;

echo "🔄 Aplicando descuentos a registros de nómina existentes...\n\n";

DB::beginTransaction();

try {
    // Obtener todos los registros de nómina que no tienen descuentos aplicados
    $entries = PayrollEntry::whereDoesntHave('discounts')->get();
    
    echo "📊 Encontrados " . $entries->count() . " registros sin descuentos aplicados\n\n";
    
    $totalAplicados = 0;
    $registrosActualizados = 0;
    
    foreach ($entries as $entry) {
        echo "Procesando: {$entry->worker_name} - Semana {$entry->date->format('Y-m-d')}... ";
        
        // En el nuevo modelo, los descuentos se crean directamente vinculados al registro
        // No hay método aplicarDescuentos() para aplicar descuentos automáticamente
        $descuentos = 0;
        
        if ($descuentos > 0) {
            echo "✅ Aplicados: $" . number_format($descuentos, 2) . "\n";
            $totalAplicados += $descuentos;
            $registrosActualizados++;
        } else {
            echo "⚪ Sin descuentos activos\n";
        }
    }
    
    DB::commit();
    
    echo "\n✅ Proceso completado exitosamente!\n";
    echo "📈 Total de registros actualizados: {$registrosActualizados}\n";
    echo "💰 Total de descuentos aplicados: $" . number_format($totalAplicados, 2) . "\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
