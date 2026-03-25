<?php

namespace App\Console\Commands;

use App\Models\PayrollEntry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ApplyDiscountsToExistingPayroll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payroll:apply-discounts
                            {--dry-run : Run without making changes}
                            {--week= : Apply only to specific week (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apply active employee discounts to existing payroll entries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $week = $this->option('week');

        $this->info('🔄 Aplicando descuentos a registros de nómina existentes...');
        $this->newLine();

        if ($dryRun) {
            $this->warn('⚠️  Modo DRY-RUN: No se realizarán cambios en la base de datos');
            $this->newLine();
        }

        DB::beginTransaction();

        try {
            // Obtener registros de nómina sin descuentos aplicados
            $query = PayrollEntry::whereDoesntHave('discountApplications');

            if ($week) {
                $query->whereDate('date', $week);
                $this->info("📅 Filtrando por semana: {$week}");
            }

            $entries = $query->get();

            $this->info("📊 Encontrados {$entries->count()} registros sin descuentos aplicados");
            $this->newLine();

            $totalAplicados = 0;
            $registrosActualizados = 0;

            $progressBar = $this->output->createProgressBar($entries->count());
            $progressBar->start();

            foreach ($entries as $entry) {
                if (!$dryRun) {
                    $descuentos = $entry->aplicarDescuentos();
                } else {
                    // En dry-run, solo simular
                    $descuentos = 0;
                    // Aquí podrías calcular sin guardar
                }

                if ($descuentos > 0) {
                    $totalAplicados += $descuentos;
                    $registrosActualizados++;
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            if ($dryRun) {
                DB::rollBack();
                $this->warn('⚠️  Cambios revertidos (dry-run)');
            } else {
                DB::commit();
                $this->info('✅ Proceso completado exitosamente!');
            }

            $this->newLine();
            $this->table(
                ['Métrica', 'Valor'],
                [
                    ['Registros procesados', $entries->count()],
                    ['Registros actualizados', $registrosActualizados],
                    ['Total descuentos aplicados', '$' . number_format($totalAplicados, 2)],
                ]
            );

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('Stack trace:');
            $this->error($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
