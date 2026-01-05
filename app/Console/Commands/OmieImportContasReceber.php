<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieReceber;
use Carbon\Carbon;
use Throwable;

class OmieImportContasReceber extends Command
{
    protected $signature = 'omie:import-receber {empresa? : sv | vs | gv} {--pagina=1}';
    protected $description = 'Importa contas a receber da Omie por empresa';

    public function handle()
    {
        $empresaArg = $this->argument('empresa') ?? 'gv';

        $map = [
            'sv' => ['codigo' => '04'],
            'vs' => ['codigo' => '30'],
            'gv' => ['codigo' => '36'],
        ];

        if (!isset($map[$empresaArg])) {
            $this->error('Empresa inválida. Use: sv | vs | gv');
            return Command::FAILURE;
        }

        $codigoEmpresa = $map[$empresaArg]['codigo'];
        $empresaCfg = config("omie.empresas.{$codigoEmpresa}");

        if (!$empresaCfg || empty($empresaCfg['app_key'])) {
            $this->error('Configuração Omie não encontrada para a empresa.');
            return Command::FAILURE;
        }

        $pagina = (int)$this->option('pagina');
        $porPagina = 50;
        $totalPaginas = 1;
        $importados = 0;

        $this->info("🚀 Importando Contas a Receber — {$empresaCfg['label']} (empresa {$codigoEmpresa})");
        $this->line("📌 Iniciando na página {$pagina}");

        $omie = new OmieClient(
            $empresaCfg['app_key'],
            $empresaCfg['app_secret']
        );

        try {
            do {
                $this->line("📄 Página {$pagina} de {$totalPaginas}");

                $response = $omie->post(
                    'financas/contareceber',
                    'ListarContasReceber',
                    [
                        'pagina' => $pagina,
                        'registros_por_pagina' => $porPagina,
                        'apenas_importado_api' => 'N',
                    ]
                );

                $totalPaginas = $response['total_de_paginas'] ?? 1;

                foreach ($response['conta_receber_cadastro'] ?? [] as $conta) {

                    $dataVencimento = !empty($conta['data_vencimento'])
                        ? Carbon::createFromFormat('d/m/Y', $conta['data_vencimento'])
                        : null;

                    $dataPrevisao = !empty($conta['data_previsao'])
                        ? Carbon::createFromFormat('d/m/Y', $conta['data_previsao'])
                        : $dataVencimento;

                    // Se o código de integração estiver vazio, geramos um identificador único
                    $codigoIntegracao = !empty($conta['codigo_lancamento_integracao'])
                        ? $conta['codigo_lancamento_integracao']
                        : 'TMP_' . uniqid() . '_' . $codigoEmpresa;

                    OmieReceber::updateOrCreate(
                        [
                            'codigo_lancamento_integracao' => $codigoIntegracao
                        ],
                        [
                            'empresa' => $codigoEmpresa,
                            'codigo_cliente_fornecedor' => $conta['codigo_cliente_fornecedor'],
                            'data_vencimento' => $dataVencimento,
                            'data_previsao' => $dataPrevisao,
                            'valor_documento' => $conta['valor_documento'],
                            'codigo_categoria' => $conta['codigo_categoria'] ?? $conta['categorias'][0]['codigo_categoria'] ?? null,
                            'id_conta_corrente' => $conta['id_conta_corrente'],
                            'status' => strtolower($conta['status_titulo'] ?? 'pendente'),
                            'payload' => $conta,
                        ]
                    );

                    $importados++;
                }

                $pagina++;

                sleep(1); // evita rate limit / erro 500

            } while ($pagina <= $totalPaginas);

        } catch (Throwable $e) {

            $this->error("❌ Erro na página {$pagina}: {$e->getMessage()}");
            $this->warn("➡️ Para retomar, execute:");
            $this->line("php artisan omie:import-receber {$empresaArg} --pagina={$pagina}");

            return Command::FAILURE;
        }

        $this->info("✅ {$empresaCfg['label']} — {$importados} contas importadas com sucesso");

        return Command::SUCCESS;
    }
}
