<?php

namespace App\Console\Commands;

use App\Models\PayrollDiscountApplication;
use App\Models\EmployeeDiscount;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RevertDiscountApplications extends Command
{
    protected $signature = 'payroll:revert-discount-applications
                            {--all : Revert all discount applications}
                            {--discount= : Revert applications for specific discount ID}';

    protected $description = 'Revert discount applications and restore discount balances';

    public function handle()
    {
        $all = $this->option('all');
        $discountId = $this->option('discount');

        if (!$all && !$discountId) {
            $this->error('Debes especificar --all o --discount=ID');
            return Command::FAILURE;
        }

        if (!$this->confirm('¿Estás seguro de revertir las aplicaciones de descuentos?')) {
            $this->info('Operación cancelada');
            return Command::SUCCESS;
        }

        DB::beginTransaction();

        try {
            $query = PayrollDiscountApplication::query();
            
            if ($discountId) {
                $query->where('discount_id', $discountId);
            }

            $applications = $query->get();
            
            $this->info("Encontradas {$applications->count()} aplicaciones para revertir");
            
            $progressBar = $this->output->createProgressBar($applications->count());
            $progressBar->start();

            foreach ($applications as $application) {
                $discount = $application->discount;
                
                if ($discount) {
                    // Restaurar saldo
                    $discount->saldo_restante += $application->monto_aplicado;
                    $discount->semanas_aplicadas = max(0, $discount->semanas_aplicadas - 1);
                    
                    // Si estaba liquidado, reactivarlo
                    if ($discount->estado === 'liquidado') {
                        $discount->estado = 'activo';
                        $discount->fecha_liquidacion_real = null;
                    }
                    
                    $discount->save();
                }
                
                // Eliminar la aplicación
                $application->delete();
                
                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            DB::commit();
            
            $this->info('✅ Aplicaciones revertidas exitosamente');
            $this->info("Total de aplicaciones eliminadas: {$applications->count()}");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
