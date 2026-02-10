<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieContaCorrenteLancamento;
use App\Models\OmieContaCorrente;
use Throwable;
use Carbon\Carbon;

class OmieImportContaCorrenteLancamentos extends Command
{
    protected $signature = 'omie:import-conta-corrente-lancamentos {empresa : sv|vs|gv |cs}';
    protected $description = 'Importa lançamentos de conta corrente da Omie (ListarLancCC)';

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

        $this->info("🔄 Importando lançamentos: {$empresaCfg['label']} ({$codigoEmpresa})");

        $omie = new OmieClient(
            $empresaCfg['app_key'],
            $empresaCfg['app_secret']
        );

        $dataInicio = '01/01/2025';
        $dataFim    = '31/01/2025';

        // ✅ FILTRO CORRETO
        $contas = OmieContaCorrente::where('empresa_codigo', $codigoEmpresa)
            ->whereNotNull('omie_cc_id')
            ->where('inativo', 'N')
            ->get();

        if ($contas->isEmpty()) {
            $this->warn('Nenhuma conta com ID Omie encontrada.');
            return Command::SUCCESS;
        }

        $totalImportados = 0;
        $bar = $this->output->createProgressBar($contas->count());
        $bar->start();

        foreach ($contas as $conta) {
            usleep(600000); // 0.6s entre contas

            try {
                $pagina = 1;
                $totalPaginas = 1;

                do {
                    usleep(500000); // 0.5s entre páginas

                    $response = $omie->post(
                        'financas/contacorrentelancamentos/',
                        'ListarLancCC',
                        [
                            'pagina' => $pagina,
                            'registros_por_pagina' => 20,
                            'nCodCC' => (int) $conta->omie_cc_id,
                            'dDtLancIni' => $dataInicio,
                            'dDtLancFim' => $dataFim,
                        ]
                    );

                    $totalPaginas = $response['total_de_paginas'] ?? 1;
                    $lancamentos  = $response['lancamentos'] ?? [];

                    foreach ($lancamentos as $lanc) {
                        OmieContaCorrenteLancamento::updateOrCreate(
                            [
                                'empresa_codigo' => $codigoEmpresa,
                                'nCodLanc' => $lanc['nCodLanc'],
                            ],
                            [
                                'empresa_nome' => $empresaCfg['label'],
                                'omie_cc_id' => $conta->omie_cc_id,
                                'data_lancamento' => Carbon::createFromFormat(
                                    'd/m/Y',
                                    $lanc['dDtLanc']
                                )->format('Y-m-d'),
                                'valor' => $lanc['nValorLanc'],
                                'tipo' => $lanc['cNatureza'] ?? 'C',
                                'descricao' => $lanc['cDescLanc'] ?? null,
                                'payload' => $lanc,
                                'importado_em' => now(),
                            ]
                        );

                        $totalImportados++;
                    }

                    $pagina++;

                } while ($pagina <= $totalPaginas);

            } catch (Throwable $e) {
                $this->newLine();

                if (str_contains($e->getMessage(), '425')) {
                    $this->warn("⏳ Rate limit ({$conta->descricao}). Pulando conta.");
                    sleep(3);
                } else {
                    $this->error("❌ {$conta->descricao}: {$e->getMessage()}");
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Importação finalizada. Total importado: {$totalImportados}");

        return Command::SUCCESS;
    }
}
