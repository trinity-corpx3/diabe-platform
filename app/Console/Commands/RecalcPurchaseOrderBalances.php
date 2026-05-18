<?php

namespace App\Console\Commands;

use App\Models\PurchaseOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecalcPurchaseOrderBalances extends Command
{
    protected $signature = 'diabe:recalc-po-balances
                            {--dry-run : Show what would change without writing}
                            {--po= : Only recalc a single PO id (numeric)}';

    protected $description = 'Recalculates paid_to_date and balance for all Purchase Orders from their actually linked, non-deleted, paid expenses. Fixes drift from expenses that were unlinked or reassigned.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $singlePoId = $this->option('po');

        $query = PurchaseOrder::query()->where('is_deleted', 0);
        if ($singlePoId) {
            $query->where('id', (int) $singlePoId);
        }

        $total = (clone $query)->count();
        $this->info(($dryRun ? '[DRY-RUN] ' : '') . "Procesando {$total} órdenes de compra...");

        $changed = 0;
        $unchanged = 0;
        $diffs = [];

        $query->chunkById(200, function ($pos) use ($dryRun, &$changed, &$unchanged, &$diffs) {
            foreach ($pos as $po) {
                $actualPaid = (float) DB::table('expenses')
                    ->where('purchase_order_id', $po->id)
                    ->where('payment_date', '!=', '')
                    ->whereNotNull('payment_date')
                    ->whereNull('deleted_at')
                    ->where('is_deleted', 0)
                    ->sum('amount');

                $actualBalance = round((float) $po->amount - $actualPaid, 2);
                $storedPaid = round((float) $po->paid_to_date, 2);
                $storedBalance = round((float) $po->balance, 2);

                if (abs($actualPaid - $storedPaid) < 0.01 && abs($actualBalance - $storedBalance) < 0.01) {
                    $unchanged++;
                    continue;
                }

                $diffs[] = [
                    'po_id' => $po->id,
                    'number' => $po->number,
                    'amount' => $po->amount,
                    'paid_was' => $storedPaid,
                    'paid_now' => round($actualPaid, 2),
                    'balance_was' => $storedBalance,
                    'balance_now' => $actualBalance,
                ];

                if (!$dryRun) {
                    $po->paid_to_date = $actualPaid;
                    $po->balance = $actualBalance;
                    $po->saveQuietly();
                }

                $changed++;
            }
        });

        $this->newLine();
        $this->info("Sin cambios: {$unchanged}");
        $this->info(($dryRun ? 'Cambiaria' : 'Cambiados') . ": {$changed}");

        if (!empty($diffs)) {
            $this->newLine();
            $this->table(
                ['PO ID', 'Número', 'Monto', 'Pagado (antes)', 'Pagado (real)', 'Saldo (antes)', 'Saldo (real)'],
                array_map(fn ($d) => [
                    $d['po_id'],
                    $d['number'],
                    number_format($d['amount'], 2),
                    number_format($d['paid_was'], 2),
                    number_format($d['paid_now'], 2),
                    number_format($d['balance_was'], 2),
                    number_format($d['balance_now'], 2),
                ], $diffs)
            );
        }

        if ($dryRun) {
            $this->warn('Dry-run: ningún cambio fue persistido. Correlo sin --dry-run para aplicar.');
        }

        return self::SUCCESS;
    }
}
