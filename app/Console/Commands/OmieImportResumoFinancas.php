<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieResumoFinanca;
use Carbon\Carbon;
use Throwable;

class OmieImportResumoFinancas extends Command
{
    protected $signature = 'omie:import-resumo-financas 
                            {empresa? : gv | sv | vs} 
                            {data? : Data no formato d/m/Y}';

    protected $description = 'Importa o Resumo de Finanças da Omie (snapshot diário)';

    public function handle()
    {
        $empresaArg = $this->argument('empresa') ?? 'gv';
        $dataArg    = $this->argument('data') ?? now()->format('d/m/Y');

        $map = [
            'sv' => ['codigo' => '04'],
            'vs' => ['codigo' => '30'],
            'gv' => ['codigo' => '36'],
        ];

        if (!isset($map[$empresaArg])) {
            $this->error('Empresa inválida. Use: gv | sv | vs');
            return Command::FAILURE;
        }

        try {
            $dataReferencia = Carbon::createFromFormat('d/m/Y', $dataArg)->startOfDay();
        } catch (Throwable $e) {
            $this->error('Data inválida. Use o formato d/m/Y');
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

        $this->info("📊 Importando Resumo Financeiro — {$empresaCfg['label']} ({$codigoEmpresa})");
        $this->info("📅 Data de referência: {$dataReferencia->format('d/m/Y')}");

        $tentativa = 0;
        $maxTentativas = 3;

        do {
            try {
                $response = $omie->post(
                    'financas/resumo/',
                    'ObterResumoFinancas',
                    [
                        'ObterResumoFinRequest' => [
                            'dDia' => $dataReferencia->format('d/m/Y'),
                            'lApenasResumo' => true,
                        ]
                    ]
                );

                if (isset($response['omie_fail'])) {
                    throw new \Exception($response['omie_fail']['faultstring'] ?? 'Erro Omie');
                }

                OmieResumoFinanca::updateOrCreate(
                    [
                        'empresa' => $codigoEmpresa,
                        'data_referencia' => $dataReferencia->toDateString(),
                    ],
                    [
                        // Conta Corrente
                        'saldo_contas'   => data_get($response, 'contaCorrente.vTotal'),
                        'limite_credito' => data_get($response, 'contaCorrente.vLimiteCredito'),

                        // Contas a Pagar
                        'qtd_pagar'          => data_get($response, 'contaPagar.nTotal'),
                        'total_pagar'        => data_get($response, 'contaPagar.vTotal'),
                        'total_pagar_atraso' => data_get($response, 'contaPagar.vAtraso'),

                        // Contas a Receber
                        'qtd_receber'          => data_get($response, 'contaReceber.nTotal'),
                        'total_receber'        => data_get($response, 'contaReceber.vTotal'),
                        'total_receber_atraso' => data_get($response, 'contaReceber.vAtraso'),

                        // Fluxo de Caixa
                        'fluxo_pagar'    => data_get($response, 'fluxoCaixa.vPagar'),
                        'fluxo_receber'  => data_get($response, 'fluxoCaixa.vReceber'),
                        'fluxo_saldo'    => data_get($response, 'fluxoCaixa.vSaldo'),

                        // Visual
                        'icone' => data_get($response, 'contaCorrente.cIcone'),
                        'cor'   => data_get($response, 'contaCorrente.cCor'),

                        // Payload bruto (auditoria / debug)
                        'payload' => $response,
                    ]
                );

                $this->info('✅ Resumo financeiro importado com sucesso');
                return Command::SUCCESS;

            } catch (Throwable $e) {
                $tentativa++;
                $this->warn("⚠️ Tentativa {$tentativa}/{$maxTentativas} falhou: {$e->getMessage()}");

                if ($tentativa < $maxTentativas) {
                    $sleep = 5 * $tentativa;
                    $this->info("⏳ Backoff {$sleep}s");
                    sleep($sleep);
                } else {
                    $this->error('❌ Falha definitiva ao importar resumo financeiro');
                    return Command::FAILURE;
                }
            }

        } while ($tentativa < $maxTentativas);

        return Command::SUCCESS;
    }
}
