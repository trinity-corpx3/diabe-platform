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

        $company = \App\Models\Company::first();

        if (!$company) {
            $this->error('No se encontró ninguna empresa. Por favor completa el setup inicial primero.');
            return 1;
        }

        // 1. Configurar Campos Personalizados
        // Estructura JSON para custom_fields en v5
        $customFields = $company->custom_fields ?? (object) [];

        // Projects (Obras)
        // Campo Fecha 1: Fecha Inicio Obra
        $customFields->project1 = "Fecha Inicio Obra|date";
        // Campo Fecha 2: Fecha Entrega Pactada
        $customFields->project2 = "Fecha Entrega Pactada|date";
        // Campo Dropdown: Estatus Financiero
        $customFields->project3 = "Estatus Financiero|dropdown|En Presupuesto,Desviación,Crítico";

        // Invoices (Facturas)
        // Campo Texto 1: Referencia Bancaria
        $customFields->invoice1 = "Referencia Bancaria";

        $company->custom_fields = $customFields;
        $this->info('Campos Personalizados actualizados.');

        // 3. Configurar Etiquetas Personalizadas (Traducciones)
        // Esto corrige los nombres de "Vendedores" -> "Proveedores" y "CIF/NIF" -> "RFC"
        $settings = $company->settings;

        // Inicializamos translations si es null
        if (!isset($settings->translations) || is_null($settings->translations)) {
            $settings->translations = new \stdClass();
        }

        $settings->translations->vendors = "Proveedores";
        $settings->translations->vat_number = "RFC";

        $company->settings = $settings;
        $this->info('Etiquetas personalizadas (Proveedores y RFC) actualizadas.');

        $company->save();

        $this->info('Configuración guardada exitosamente para la empresa: ' . $company->present()->name());

        return 0;
    }
}
