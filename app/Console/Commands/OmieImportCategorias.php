<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieCategoria;
use Throwable;

class OmieImportCategorias extends Command
{
    protected $signature = 'omie:import-categorias {empresa? : sv | vs | gv} {--pagina=1}';
    protected $description = 'Importa categorias da Omie';

    public function handle()
    {
        $empresaArg = $this->argument('empresa') ?? 'gv';

        $map = [
            'sv' => ['codigo' => '04'],
            'vs' => ['codigo' => '30'],
            'gv' => ['codigo' => '36'],
        ];

        if (!isset($map[$empresaArg])) {
            $this->error('Empresa inválida.');
            return Command::FAILURE;
        }

        $codigoEmpresa = $map[$empresaArg]['codigo'];
        $empresaCfg = config("omie.empresas.{$codigoEmpresa}");

        $omie = new OmieClient(
            $empresaCfg['app_key'],
            $empresaCfg['app_secret']
        );

        $pagina = (int)$this->option('pagina');
        $porPagina = 50;
        $totalPaginas = 1;
        $importados = 0;

        try {
            do {
                $response = $omie->post(
                    'geral/categorias',
                    'ListarCategorias',
                    [
                        'pagina' => $pagina,
                        'registros_por_pagina' => $porPagina,
                    ]
                );

                $totalPaginas = $response['total_de_paginas'] ?? 1;

                foreach ($response['categoria_cadastro'] ?? [] as $cat) {

                    $dre = $cat['dadosDRE'] ?? [];

                    OmieCategoria::updateOrCreate(
                        [
                            'empresa' => $codigoEmpresa,
                            'codigo'  => $cat['codigo'],
                        ],
                        [
                            'descricao' => html_entity_decode($cat['descricao']),
                            'descricao_padrao' => html_entity_decode($cat['descricao_padrao'] ?? null),
                            'categoria_superior' => $cat['categoria_superior'] ?? null,
                            'codigo_dre' => $cat['codigo_dre'] ?? null,

                            'conta_receita' => ($cat['conta_receita'] ?? 'N') === 'S',
                            'conta_despesa' => ($cat['conta_despesa'] ?? 'N') === 'S',
                            'totalizadora' => ($cat['totalizadora'] ?? 'N') === 'S',
                            'transferencia' => ($cat['transferencia'] ?? 'N') === 'S',
                            'conta_inativa' => ($cat['conta_inativa'] ?? 'N') === 'S',
                            'nao_exibir' => ($cat['nao_exibir'] ?? 'N') === 'S',
                            'definida_pelo_usuario' => ($cat['definida_pelo_usuario'] ?? 'N') === 'S',

                            'natureza' => $cat['natureza'] ?? null,

                            'dre_codigo' => $dre['codigoDRE'] ?? null,
                            'dre_descricao' => $dre['descricaoDRE'] ?? null,
                            'dre_nivel' => $dre['nivelDRE'] ?? null,
                            'dre_sinal' => $dre['sinalDRE'] ?? null,
                            'dre_totaliza' => ($dre['totalizaDRE'] ?? 'N') === 'S',
                            'dre_nao_exibir' => ($dre['naoExibirDRE'] ?? 'N') === 'S',

                            'payload' => $cat,
                        ]
                    );

                    $importados++;
                }

                $pagina++;
                sleep(1);

            } while ($pagina <= $totalPaginas);

        } catch (Throwable $e) {
            $this->error($e->getMessage());
            return Command::FAILURE;
        }

        $this->info("✅ {$importados} categorias importadas ({$empresaCfg['label']})");

        return Command::SUCCESS;
    }
}
