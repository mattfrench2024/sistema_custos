<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieOrcamento;
use Carbon\Carbon;
use Throwable;

class OmieImportOrcamentos extends Command
{
    protected $signature = 'omie:import-orcamentos {empresa? : gv | sv | vs} {--ano-inicio=} {--ano-fim=}';
    protected $description = 'Importa Orçamento de Caixa (Previsto x Realizado) da Omie por empresa para todo o período com retry e backoff';

    public function handle()
    {
        $empresaArg = $this->argument('empresa') ?? 'gv';
        $anoInicio = $this->option('ano-inicio') ?? 2020;
        $anoFim = $this->option('ano-fim') ?? date('Y');

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

        $omie = new OmieClient($empresaCfg['app_key'], $empresaCfg['app_secret']);
        $this->info("🚀 Iniciando importação de Orçamento de Caixa — {$empresaCfg['label']} ({$codigoEmpresa})");

        $totalImportados = 0;

        for ($ano = $anoInicio; $ano <= $anoFim; $ano++) {
            for ($mes = 1; $mes <= 12; $mes++) {

                $this->info("📅 Importando {$ano}/{$mes}");

                $tentativa = 0;
                $maxTentativas = 3;

                do {
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
                        $importadosMes = 0;

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

                            $importadosMes++;
                        }

                        $totalImportados += $importadosMes;
                        $this->line("✅ {$importadosMes} orçamentos importados em {$ano}/{$mes}");

                        // Pausa entre meses para evitar bloqueio
                        sleep(5);

                        // sucesso, sai do loop de retry
                        break;

                    } catch (Throwable $e) {
                        $tentativa++;
                        $this->warn("⚠️ Tentativa {$tentativa}/{$maxTentativas} falhou para {$ano}/{$mes}: {$e->getMessage()}");

                        if ($tentativa < $maxTentativas) {
                            // backoff exponencial
                            $sleepTime = 5 * $tentativa; 
                            $this->info("⏳ Aguardando {$sleepTime}s antes de tentar novamente...");
                            sleep($sleepTime);
                        } else {
                            $this->error("❌ Falha persistente em {$ano}/{$mes}, pulando para o próximo mês.");
                        }
                    }
                } while ($tentativa < $maxTentativas);
            }
        }

        $this->info("🎯 Importação concluída — Total: {$totalImportados} registros");
        return Command::SUCCESS;
    }
}
