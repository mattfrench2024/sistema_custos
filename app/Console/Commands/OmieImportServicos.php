<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieServico;
use Throwable;

class OmieImportServicos extends Command
{
    protected $signature = 'omie:import-servicos {empresa? : gv | sv | vs}';
    protected $description = 'Importa Cadastro de Serviços da Omie com paginação, retry e backoff';

    public function handle()
    {
        $empresaArg = $this->argument('empresa') ?? 'gv';

        $map = [
            'sv' => ['codigo' => '04'],
            'vs' => ['codigo' => '30'],
            'gv' => ['codigo' => '36'],
        ];

        if (!isset($map[$empresaArg])) {
            $this->error('Empresa inválida. Use: gv | sv | vs');
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

        $this->info("🚀 Importando Serviços — {$empresaCfg['label']} ({$codigoEmpresa})");

        $pagina = 1;
        $registrosPorPagina = 20;
        $totalImportados = 0;
        $totalPaginas = 1;

        do {
            $tentativa = 0;
            $maxTentativas = 3;

            do {
                try {
                    $response = $omie->post(
                        'servicos/servico/',
                        'ListarCadastroServico',
                        [
                            'srvListarRequest' => [
                                'nPagina' => $pagina,
                                'nRegPorPagina' => $registrosPorPagina,
                            ]
                        ]
                    );

                    $cadastros = $response['cadastros'] ?? [];
                    $totalPaginas = $response['nTotPaginas'] ?? 1;

                    foreach ($cadastros as $srv) {
                        $cab = $srv['cabecalho'] ?? [];

                        OmieServico::updateOrCreate(
                            [
                                'empresa' => $codigoEmpresa,
                                'codigo_servico' => $srv['intListar']['nCodServ'] ?? null,
                            ],
                            [
                                'codigo_integracao' => $srv['intListar']['cCodIntServ'] ?? null,
                                'codigo' => $cab['cCodigo'] ?? null,
                                'descricao' => $cab['cDescricao'] ?? null,
                                'preco_unitario' => $cab['nPrecoUnit'] ?? null,
                                'codigo_categoria' => $cab['cCodCateg'] ?? null,
                                'importado_api' => $srv['info']['cImpAPI'] ?? null,
                                'inativo' => $srv['info']['inativo'] ?? null,
                                'cabecalho' => $cab,
                                'descricao_completa' => $srv['descricao'] ?? null,
                                'impostos' => $srv['impostos'] ?? null,
                                'info' => $srv['info'] ?? null,
                                'produtos_utilizados' => $srv['produtosUtilizados'] ?? null,
                                'payload' => $srv,
                            ]
                        );

                        $totalImportados++;
                    }

                    $this->line("📄 Página {$pagina}/{$totalPaginas} — " . count($cadastros) . " serviços");
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

        } while ($pagina <= $totalPaginas);

        $this->info("🎯 Importação concluída — Total: {$totalImportados} serviços");
        return Command::SUCCESS;
    }
}
