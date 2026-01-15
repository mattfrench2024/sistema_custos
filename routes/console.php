<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| IMPORTAÇÕES AUTOMÁTICAS — A CADA HORA
|--------------------------------------------------------------------------
|
| Todos os comandos listados serão executados a cada hora para os três
| ambientes/sucursais: gv, sv, vs. O log será salvo em storage/logs/omie.log
|
*/

$comandos = [
    'omie:import-categorias',
    'omie:import-clientes',
    'omie:import-conta-corrente',
    'omie:import-conta-corrente-lancamentos',
    'omie:import-contas-pagar',
    'omie:import-contas-receber',
    'omie:import-contratos',
    'omie:import-documentos-fiscais',
    'omie:import-empresas',
    'omie:import-extrato-bancario',
    'omie:import-mf',
    'omie:import-movimentos-financeiros',
    'omie:import-oportunidades',
    'omie:import-orcamentos',
    'omie:import-produtos',
    'omie:import-resumo-financas',
    'omie:import-servicos',
    'omie:import-tarefas',
    'omie:import-tipos-documento',
];

// Sucursais
$sucursais = ['gv', 'sv', 'vs'];

// Agendamento
foreach ($comandos as $index => $comando) {
    foreach ($sucursais as $sucursal) {
        Schedule::command("$comando $sucursal")
            ->hourlyAt($index * 3) // espaçamento de 3 min entre comandos
            ->withoutOverlapping()
            ->appendOutputTo(storage_path("logs/omie.log"));
    }
}
