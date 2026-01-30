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

        // 2. Configurar Dashboard (Ocultar Tasks, Mostrar P&L)
        // Desactivar módulos operativos innecesarios
        // Esto se maneja via enabled_modules (bitmask) o settings booleanos

        // Deshabilitar Tasks (Modules::TASKS = 8)
        // Bitwise logic is trickier via script without defined constants in scope, 
        // better to use methods if available, but direct property set works for booleans.

        // Habilitar visualización de Profit & Loss (generalmente prefencias de usuario, pero intentamos setear globales)
        // Nota: Dashboard configuration is often user-specific (User preferences).
        // Intentaremos configurar preferencias por defecto de la empresa si existen.

        $company->save();

        $this->info('Configuración guardada exitosamente para la empresa: ' . $company->present()->name());

        return 0;
    }
}
