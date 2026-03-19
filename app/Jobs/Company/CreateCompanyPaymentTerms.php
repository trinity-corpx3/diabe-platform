<?php

/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2025. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\Jobs\Company;

use App\Models\PaymentTerm;
use App\Utils\PaymentTerms as PaymentTermsHelper;
use App\Utils\Traits\MakesHash;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateCompanyPaymentTerms
{
    use MakesHash;
    use Dispatchable;

    protected $company;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @param $company
     * @param $user
     */
    public function __construct($company, $user)
    {
        $this->company = $company;

        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $paymentTerms = collect(PaymentTermsHelper::defaultTerms())->map(function ($term) {
            return [
                'num_days' => $term,
                'name' => '',
                'company_id' => $this->company->id,
                'user_id' => $this->user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->all();

        PaymentTerm::insert($paymentTerms);
    }
}
