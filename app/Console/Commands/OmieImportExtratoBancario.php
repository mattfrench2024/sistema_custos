<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieContaCorrente;
use App\Models\OmieExtratoBancario;
use Throwable;
use Carbon\Carbon;

class OmieImportExtratoBancario extends Command
{
    protected $signature = 'omie:import-extrato 
    {empresa : sv|vs|gv}
    {--inicio=01/01/2024}
    {--fim=01/02/2024}';


    protected $description = 'Importa extrato bancário (conciliação) da Omie';

    public function handle(): int
    {
        $map = [
            'sv' => ['codigo' => '04'],
            'vs' => ['codigo' => '30'],
            'gv' => ['codigo' => '36'],
        ];

        $empresaArg = $this->argument('empresa');

        if (!isset($map[$empresaArg])) {
            $this->error('Empresa inválida');
            return Command::FAILURE;
        }

        $codigoEmpresa = $map[$empresaArg]['codigo'];
        $empresaCfg = config("omie.empresas.{$codigoEmpresa}");

        $omie = new OmieClient(
            $empresaCfg['app_key'],
            $empresaCfg['app_secret']
        );

        $contas = OmieContaCorrente::where('empresa_codigo', $codigoEmpresa)
            ->whereNotNull('omie_cc_id')
            ->where('inativo', 'N')
            ->get();

        $totalImportados = 0;

        foreach ($contas as $conta) {

            $pagina = 1;
            $porPagina = 50;

            try {

                do {

                    $response = $omie->post(
                        'financas/extrato',
                        'ListarExtrato',
                        [
                            'nCodCC' => $conta->omie_cc_id,
                            'dPeriodoInicial' => $this->option('inicio'),
                            'dPeriodoFinal'   => $this->option('fim'),
                            'pagina' => $pagina,
                            'registros_por_pagina' => $porPagina,
                        ]
                    );


                    $extratos = $response['extrato'] ?? [];
                    $totalPaginas = $response['total_de_paginas'] ?? 1;

                    foreach ($extratos as $item) {

                        OmieExtratoBancario::updateOrCreate(
                            [
                                'empresa_codigo' => $codigoEmpresa,
                                'omie_cc_id'     => $conta->omie_cc_id,
                                'data_movimento' => isset($item['dDataMov'])
                                    ? Carbon::createFromFormat('d/m/Y', $item['dDataMov'])
                                    : null,
                                'valor'          => $item['nValor'] ?? 0,
                                'descricao'      => $item['cHistorico'] ?? null,
                            ],
                            [
                                'empresa_nome'       => $empresaCfg['label'],
                                'codigo_interno_cc'  => $conta->codigo_interno,
                                'data_compensacao'   => isset($item['dDataComp'])
                                    ? Carbon::createFromFormat('d/m/Y', $item['dDataComp'])
                                    : null,
                                'tipo'               => $item['cDebCred'] ?? null,
                                'documento'          => $item['cDocumento'] ?? null,
                                'saldo_pos'          => $item['nSaldo'] ?? null,
                                'payload'            => $item,
                                'importado_em'       => now(),
                            ]
                        );

                        $totalImportados++;
                    }

                    $pagina++;

                } while ($pagina <= $totalPaginas);

            } catch (Throwable $e) {

                $this->warn(
                    "⚠️ Conta {$conta->descricao} ({$conta->omie_cc_id}) ignorada: {$e->getMessage()}"
                );

                continue;
            }
        }

        $this->info("✅ {$totalImportados} registros de extrato importados ({$empresaCfg['label']})");

        return Command::SUCCESS;
    }
}
