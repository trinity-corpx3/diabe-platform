<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConfigureConstructionCompany extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ninja:configure-construction-company';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configura los Campos Personalizados y Dashboard para la Empresa Constructora';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando configuración para Empresa Constructora...');

        // 3. Configurar Etiquetas Personalizadas (Traducciones) en TODAS las empresas
        $companies = \App\Models\Company::all();

        foreach ($companies as $company) {
            $this->info("Procesando empresa: " . $company->present()->name() . " (ID: {$company->id})");

            $settings = $company->settings;

            // Inicializamos translations si es null
            if (!isset($settings->translations) || is_null($settings->translations)) {
                $settings->translations = new \stdClass();
            }

            // Aplicamos las traducciones deseadas
            $settings->translations->vendors = "Proveedores";
            $settings->translations->vat_number = "RFC";
            $settings->translations->vendor = "Proveedor"; // Aseguramos singular
            $settings->translations->clients = "Clientes"; // Aseguramos clientes

            $company->settings = $settings;
            $company->save();

            $this->info("   - Etiquetas personalizadas (Proveedores, RFC, Clientes) actualizadas.");
        }

        $this->info('Configuración guardada exitosamente para todas las empresas.');

        return 0;
    }
}
