<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmiePagar;
use Carbon\Carbon;
use Throwable;

class OmieImportContasPagar extends Command
{
    protected $signature = 'omie:import-pagar {empresa? : gv | sv | vs} {--pagina=1}';
    protected $description = 'Importa contas a pagar da Omie por empresa';

    public function handle()
    {
        $empresaArg = $this->argument('empresa') ?? 'gv';

        $map = [
    'sv' => ['codigo' => '04'],
    'vs' => ['codigo' => '30'],
    'gv' => ['codigo' => '36'],
    'cs' => ['codigo' => '10'], // Sistemas Custos
];


        if (! isset($map[$empresaArg])) {
            $this->error('Empresa inválida. Use: gv | sv | vs | cs');

            return Command::FAILURE;
        }

        $codigoEmpresa = $map[$empresaArg]['codigo'];
        $empresaCfg = config("omie.empresas.{$codigoEmpresa}");

        if (! $empresaCfg || empty($empresaCfg['app_key'])) {
            $this->error('Configuração Omie não encontrada para a empresa.');
            return Command::FAILURE;
        }

        $pagina = (int) $this->option('pagina');
        $porPagina = 50;
        $totalPaginas = 1;
        $importados = 0;

        $this->info("🚀 Importando Contas a Pagar — {$empresaCfg['label']} (empresa {$codigoEmpresa})");
        $this->line("📌 Iniciando na página {$pagina}");

        $omie = new OmieClient(
            $empresaCfg['app_key'],
            $empresaCfg['app_secret']
        );

        try {

            do {
                $this->line("📄 Página {$pagina} de {$totalPaginas}");

                $response = $omie->post(
                    'financas/contapagar',
                    'ListarContasPagar',
                    [
                        'pagina' => $pagina,
                        'registros_por_pagina' => $porPagina,
                        'apenas_importado_api' => 'N',
                    ]
                );

                $totalPaginas = $response['total_de_paginas'] ?? 1;

                foreach ($response['conta_pagar_cadastro'] ?? [] as $conta) {

                    $dataEmissao = null;
                    if (!empty($conta['data_emissao'])) {
                        $dataEmissao = Carbon::createFromFormat('d/m/Y', $conta['data_emissao']);
                    }

                    $dataVencimento = null;
                    if (!empty($conta['data_vencimento'])) {
                        $dataVencimento = Carbon::createFromFormat('d/m/Y', $conta['data_vencimento']);
                    }

                    $valorDocumento = null;
                    if (isset($conta['valor_documento'])) {
                        $valorDocumento = (float) $conta['valor_documento'];
                    }

                    OmiePagar::updateOrCreate(
                        [
                            'empresa' => $codigoEmpresa,
                            'codigo_lancamento_omie' => $conta['codigo_lancamento_omie'],
                        ],
                        [
                            'codigo_cliente_fornecedor' => $conta['codigo_cliente_fornecedor'] ?? null,
                            'codigo_categoria' => $conta['codigo_categoria'] ?? null,
                            'codigo_tipo_documento' => $conta['codigo_tipo_documento'] ?? null,
                            'status_titulo' => $conta['status_titulo'] ?? null,
                            'data_emissao' => $dataEmissao,
                            'data_vencimento' => $dataVencimento,
                            'valor_documento' => $valorDocumento,
                            'categorias' => $conta['categorias'] ?? [],
                            'distribuicao' => $conta['distribuicao'] ?? [],
                            'info' => $conta['info'] ?? [],
                            'id_conta_corrente' => $conta['id_conta_corrente'] ?? null,
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
    $this->line("php artisan omie:import-pagar {$empresaArg} --pagina={$pagina}");

    return Command::FAILURE;
}

        $this->info("✅ {$empresaCfg['label']} — {$importados} contas importadas com sucesso");

        return Command::SUCCESS;
    }
}
