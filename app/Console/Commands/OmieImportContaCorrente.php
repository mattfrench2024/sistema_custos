<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieContaCorrente;
use Throwable;

class OmieImportContaCorrente extends Command
{
    protected $signature = 'omie:import-conta-corrente {empresa : sv|vs|gv}';

    protected $description = 'Importa contas correntes da Omie';

    public function handle()
    {
        $map = [
    'sv' => ['codigo' => '04'],
    'vs' => ['codigo' => '30'],
    'gv' => ['codigo' => '36'],
    'cs' => ['codigo' => '10'], // Sistemas Custos
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

        $pagina = 1;
        $porPagina = 50;
        $importados = 0;

        try {

            do {

                $response = $omie->post(
                    'geral/contacorrente/',
                    'ListarContasCorrentes',
                    [
                        'pagina' => $pagina,
                        'registros_por_pagina' => $porPagina,
                    ]
                );

                $totalPaginas = $response['total_de_paginas'] ?? 1;
                $contas = $response['ListarContasCorrentes'] ?? [];

                foreach ($contas as $cc) {

                    OmieContaCorrente::updateOrCreate(
                        [
                            'empresa_codigo' => $codigoEmpresa,
                            'omie_cc_id'     => $cc['nCodCC'],
                        ],
                        [
                            'empresa_nome'          => $empresaCfg['label'],
                            'codigo_interno'        => $cc['cCodCCInt'] ?? null,
                            'tipo_conta'            => $cc['tipo_conta_corrente'] ?? null,
                            'tipo'                  => $cc['tipo'] ?? null,
                            'codigo_banco'          => $cc['codigo_banco'] ?? null,
                            'codigo_agencia'        => $cc['codigo_agencia'] ?? null,
                            'numero_conta_corrente' => $cc['numero_conta_corrente'] ?? null,
                            'descricao'             => $cc['descricao'] ?? null,
                            'saldo_inicial'         => $cc['saldo_inicial'] ?? 0,
                            'saldo_atual'           => 0,
                            'valor_limite'          => $cc['valor_limite'] ?? 0,
                            'inativo'               => $cc['inativo'] ?? 'N',
                            'importado_api'         => $cc['importado_api'] ?? 'N',
                            'data_inc'              => isset($cc['data_inc']) ? \Carbon\Carbon::createFromFormat('d/m/Y', $cc['data_inc']) : null,
                            'data_alt'              => isset($cc['data_alt']) ? \Carbon\Carbon::createFromFormat('d/m/Y', $cc['data_alt']) : null,
                            'importado_em'          => now(),
                        ]
                    );

                    $importados++;
                }

                $pagina++;

            } while ($pagina <= $totalPaginas);

        } catch (Throwable $e) {
            $this->error("❌ {$e->getMessage()}");
            return Command::FAILURE;
        }

        $this->info("✅ {$importados} contas correntes importadas ({$empresaCfg['label']})");

        return Command::SUCCESS;
    }
}
