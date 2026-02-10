<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieOrcamento;
use Carbon\Carbon;
use Throwable;

class OmieImportOrcamentos extends Command
{
    protected $signature = 'omie:import-orcamentos {empresa? : gv | sv | vs} {--ano=} {--mes=}';
    protected $description = 'Importa Orçamento de Caixa (Previsto x Realizado) da Omie por empresa';

    public function handle()
    {
        $empresaArg = $this->argument('empresa') ?? 'gv';
        $ano = $this->option('ano') ?? date('Y');
        $mes = $this->option('mes') ?? date('m');

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

        $omie = new OmieClient($empresaCfg['app_key'], $empresaCfg['app_secret']);

        $this->info("🚀 Importando Orçamento de Caixa — {$empresaCfg['label']} ({$codigoEmpresa}) para {$ano}/{$mes}");

        try {
            $response = $omie->post(
                'financas/caixa/',
                'ListarOrcamentos',
                [
                    'ocprListarRequest' => [
                        'nAno' => (int) $ano,
                        'nMes' => (int) $mes,
                    ]
                ]
            );

            $orcamentos = $response['ListaOrcamentos'] ?? [];

            $importados = 0;
            foreach ($orcamentos as $orc) {
                OmieOrcamento::updateOrCreate(
                    [
                        'empresa' => $codigoEmpresa,
                        'ano' => $ano,
                        'mes' => $mes,
                        'codigo_categoria' => $orc['cCodCateg'] ?? null,
                    ],
                    [
                        'descricao_categoria' => $orc['cDesCateg'] ?? null,
                        'valor_previsto' => $orc['nValorPrevisto'] ?? 0,
                        'valor_realizado' => $orc['nValorRealilzado'] ?? 0,
                        'payload' => $orc,
                    ]
                );
                $importados++;
            }

            $this->info("✅ {$importados} orçamentos importados com sucesso");

        } catch (Throwable $e) {
            $this->error("❌ Erro: {$e->getMessage()}");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
