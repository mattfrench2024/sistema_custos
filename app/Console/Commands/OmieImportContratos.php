<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieContrato;
use Carbon\Carbon;
use Throwable;

class OmieImportContratos extends Command
{
    protected $signature = 'omie:import-contratos {empresa? : sv | vs | gv} {--pagina=1}';
    protected $description = 'Importa contratos de serviço da Omie';

    public function handle()
    {
        $empresaArg = $this->argument('empresa') ?? 'gv';
        $map = [
    'sv' => ['codigo' => '04'],
    'vs' => ['codigo' => '30'],
    'gv' => ['codigo' => '36'],
    'cs' => ['codigo' => '10'], // Sistemas Custos
];


        if (!isset($map[$empresaArg])) {
            $this->error('Empresa inválida.');
            return Command::FAILURE;
        }

        $codigoEmpresa = $map[$empresaArg]['codigo'];
        $empresaCfg = config("omie.empresas.{$codigoEmpresa}");
        $pagina = (int)$this->option('pagina');
        $importados = 0;

        $omie = new OmieClient($empresaCfg['app_key'], $empresaCfg['app_secret']);

        try {
            do {
                $this->info("🚀 Lendo Contratos - Página {$pagina}");

                $response = $omie->post(
                    'servicos/contrato/',
                    'ListarContratos',
                    [
                        'pagina' => $pagina,
                        'registros_por_pagina' => 50,
                        'apenas_importado_api' => 'N'
                    ]
                );

                $totalPaginas = $response['total_de_paginas'] ?? 1;

                foreach ($response['contratoCadastro'] ?? [] as $ctr) {
                    $cabecalho = $ctr['cabecalho'];

                    OmieContrato::updateOrCreate(
                        ['nCodCtr' => $cabecalho['nCodCtr']],
                        [
                            'empresa'     => $codigoEmpresa,
                            'cNumCtr'     => $cabecalho['cNumCtr'],
                            'nCodCli'     => $cabecalho['nCodCli'],
                            'cCodCateg'   => $ctr['infAdic']['cCodCateg'] ?? null,
                            'nValTotMes'  => $cabecalho['nValTotMes'],
                            'dVigInicial' => $this->parseDate($cabecalho['dVigInicial']),
                            'dVigFinal'   => $this->parseDate($cabecalho['dVigFinal']),
                            'cCodSit'     => $cabecalho['cCodSit'],
                            'itens'       => $ctr['itensContrato'] ?? [],
                            'payload'     => $ctr
                        ]
                    );
                    $importados++;
                }

                $pagina++;
                sleep(1); 

            } while ($pagina <= $totalPaginas);

            $this->info("✅ Finalizado: {$importados} contratos importados.");
        } catch (Throwable $e) {
            $this->error("❌ Erro: {$e->getMessage()}");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function parseDate($date) {
        if (empty($date)) return null;
        try {
            return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}