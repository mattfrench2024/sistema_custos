<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieMF;
use Carbon\Carbon;
use Throwable;

class OmieImportMF extends Command
{
    protected $signature = 'omie:import-mf {empresa? : gv | sv | vs}';
    protected $description = 'Importa Movimentos Financeiros da Omie com retry e paginação';

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

        $omie = new OmieClient($empresaCfg['app_key'], $empresaCfg['app_secret']);

        $pagina = 1;
        $registrosPorPagina = 500;
        $totalPaginas = 1;
        $totalImportados = 0;

        $this->info("🚀 Importando Movimentos Financeiros — {$empresaCfg['label']} ({$codigoEmpresa})");

        do {
            $tentativa = 0;
            $maxTentativas = 3;

            do {
                try {
                    $response = $omie->post(
                        'financas/mf',
                        'ListarMovimentos',
                        [
                            'mfListarRequest' => [
                                'nPagina' => $pagina,
                                'nRegPorPagina' => $registrosPorPagina,
                            ]
                        ]
                    );

                    $movimentos = $response['movimentos'] ?? [];
                    $totalPaginas = $response['nTotPaginas'] ?? 1;

                    foreach ($movimentos as $mov) {
                        OmieMF::updateOrCreate(
                            ['cCodIntTitulo' => $mov['cCodIntTitulo'] ?? null],
                            array_merge($mov, [
                                'empresa' => $codigoEmpresa,
                                'dDtEmissao' => !empty($mov['dDtEmissao']) ? Carbon::createFromFormat('d/m/Y', $mov['dDtEmissao']) : null,
                                'dDtVenc' => !empty($mov['dDtVenc']) ? Carbon::createFromFormat('d/m/Y', $mov['dDtVenc']) : null,
                                'dDtPrevisao' => !empty($mov['dDtPrevisao']) ? Carbon::createFromFormat('d/m/Y', $mov['dDtPrevisao']) : null,
                                'dDtPagamento' => !empty($mov['dDtPagamento']) ? Carbon::createFromFormat('d/m/Y', $mov['dDtPagamento']) : null,
                                'dDtRegistro' => !empty($mov['dDtRegistro']) ? Carbon::createFromFormat('d/m/Y', $mov['dDtRegistro']) : null,
                                'dDtCredito' => !empty($mov['dDtCredito']) ? Carbon::createFromFormat('d/m/Y', $mov['dDtCredito']) : null,
                                'dDtConcilia' => !empty($mov['dDtConcilia']) ? Carbon::createFromFormat('d/m/Y', $mov['dDtConcilia']) : null,
                                'dDtInc' => !empty($mov['dDtInc']) ? Carbon::createFromFormat('d/m/Y', $mov['dDtInc']) : null,
                                'dDtAlt' => !empty($mov['dDtAlt']) ? Carbon::createFromFormat('d/m/Y', $mov['dDtAlt']) : null,
                                'payload' => $mov,
                            ])
                        );

                        $totalImportados++;
                    }

                    $this->line("📄 Página {$pagina}/{$totalPaginas} — " . count($movimentos) . " movimentos");
                    sleep(2);
                    break;

                } catch (Throwable $e) {
                    $tentativa++;
                    $this->warn("⚠️ Tentativa {$tentativa}/{$maxTentativas} falhou: {$e->getMessage()}");
                    if ($tentativa < $maxTentativas) sleep(5 * $tentativa);
                    else $this->error("❌ Página {$pagina} ignorada");
                }

            } while ($tentativa < $maxTentativas);

            $pagina++;

        } while ($pagina <= $totalPaginas);

        $this->info("🎯 Importação concluída — Total: {$totalImportados} movimentos");
        return Command::SUCCESS;
    }
}
