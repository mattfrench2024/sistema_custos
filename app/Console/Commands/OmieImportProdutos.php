<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieProduto;
use Throwable;

class OmieImportProdutos extends Command
{
    protected $signature = 'omie:import-produtos {empresa? : gv | sv | vs}';
    protected $description = 'Importa Produtos do Omie (ProdutosCadastro) com paginação, retry e backoff';

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
            $this->error('Empresa inválida. Use: gv | sv | vs | cs');

            return Command::FAILURE;
        }

        $codigoEmpresa = $map[$empresaArg]['codigo'];
        $empresaCfg = config("omie.empresas.{$codigoEmpresa}");

        if (!$empresaCfg) {
            $this->error('Configuração Omie não encontrada.');
            return Command::FAILURE;
        }

        $omie = new OmieClient(
            $empresaCfg['app_key'],
            $empresaCfg['app_secret']
        );

        $this->info("🚀 Importando Produtos — {$empresaCfg['label']} ({$codigoEmpresa})");

        $pagina = 1;
        $registrosPorPagina = 50;
        $totalImportados = 0;

        do {
            $tentativa = 0;
            $maxTentativas = 3;

            do {
                try {
                    $response = $omie->post(
                        'geral/produtos/',
                        'ListarProdutos',
                        [
                            'produto_servico_list_request' => [
                                'pagina' => $pagina,
                                'registros_por_pagina' => $registrosPorPagina,
                                'apenas_importado_api' => 'N',
                                'filtrar_apenas_omiepdv' => 'N',
                            ]
                        ]
                    );

                    $produtos = $response['produto_servico_cadastro'] ?? [];
                    $totalPaginas = $response['total_de_paginas'] ?? 1;

                    foreach ($produtos as $prod) {
                        OmieProduto::updateOrCreate(
                            [
                                'empresa' => $codigoEmpresa,
                                'codigo_produto' => $prod['codigo_produto'] ?? null,
                            ],
                            [
                                'codigo_produto_integracao' => $prod['codigo_produto_integracao'] ?? null,
                                'codigo' => $prod['codigo'] ?? null,
                                'descricao' => $prod['descricao'] ?? null,
                                'unidade' => $prod['unidade'] ?? null,
                                'ncm' => $prod['ncm'] ?? null,
                                'tipo' => $prod['tipo'] ?? null,
                                'importado_api' => $prod['info']['cImpAPI'] ?? null,
                                'caracteristicas' => $prod['caracteristicas'] ?? null,
                                'componentes_kit' => $prod['componentes_kit'] ?? null,
                                'imagens' => $prod['imagens'] ?? null,
                                'dados_ibpt' => $prod['dadosIbpt'] ?? null,
                                'info' => $prod['info'] ?? null,
                                'payload' => $prod,
                            ]
                        );

                        $totalImportados++;
                    }

                    $this->line("📄 Página {$pagina}/{$totalPaginas} — " . count($produtos) . " produtos");
                    sleep(3);
                    break;

                } catch (Throwable $e) {
                    $tentativa++;
                    $this->warn("⚠️ Tentativa {$tentativa}/{$maxTentativas} falhou: {$e->getMessage()}");

                    if ($tentativa < $maxTentativas) {
                        $sleep = 5 * $tentativa;
                        $this->info("⏳ Backoff {$sleep}s");
                        sleep($sleep);
                    } else {
                        $this->error("❌ Página {$pagina} ignorada");
                    }
                }
            } while ($tentativa < $maxTentativas);

            $pagina++;

        } while ($pagina <= ($totalPaginas ?? 1));

        $this->info("🎯 Importação concluída — Total: {$totalImportados} produtos");
        return Command::SUCCESS;
    }
}
