<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieMovimentoFinanceiro;
use Carbon\Carbon;
use Throwable;
use Illuminate\Support\Facades\Log;

class OmieImportMF extends Command
{
    protected $signature = 'omie:import-mf {empresa? : sv | vs | gv} {--pagina=1}';
    protected $description = 'Importa TODOS os Movimentos Financeiros da Omie (RAW, seguro e completo)';

    public function handle()
    {
        $empresaArg = $this->argument('empresa') ?? 'gv';

        $map = [
            'sv' => '04',
            'vs' => '30',
            'gv' => '36',
        ];

        if (!isset($map[$empresaArg])) {
            $this->error('Empresa inválida. Use: sv, vs ou gv');
            return Command::FAILURE;
        }

        $empresa = $map[$empresaArg];
        $cfg = config("omie.empresas.$empresa");

        $omie = new OmieClient($cfg['app_key'], $cfg['app_secret']);

        $pagina       = (int) $this->option('pagina');
        $porPagina    = 50;
        $totalPaginas = 1;

        $this->info("🚀 Importando Movimentos Financeiros — Empresa {$empresa}");
        $this->line("📌 Página inicial: {$pagina}");

        do {
            try {
                $this->line("📄 Página {$pagina} / {$totalPaginas}");

                // ✅ CHAMADA CORRETA — SEM ORDENAÇÃO (MF NÃO SUPORTA)
                $response = $omie->post(
                    'financas/mf',
                    'ListarMovimentos',
                    [
                        'mfListarRequest' => [
                            'nPagina'       => $pagina,
                            'nRegPorPagina' => $porPagina,
                        ],
                    ]
                );

                $movimentos   = $response['movimentos'] ?? [];
                $totalPaginas = (int) ($response['nTotPaginas'] ?? 1);

                $this->info("📊 Movimentos recebidos da API: " . count($movimentos));

                $inseridos   = 0;
                $atualizados = 0;

                foreach ($movimentos as $idx => $mov) {
                    $resultado = $this->importarMovimento($mov, $empresa, $idx + 1);

                    if ($resultado === true) {
                        $inseridos++;
                    } else {
                        $atualizados++;
                    }
                }

                $this->info("✅ Página {$pagina}: {$inseridos} inseridos | {$atualizados} atualizados");

                $pagina++;
                sleep(1);

            } catch (Throwable $e) {
                $this->error("❌ Erro na página {$pagina}: {$e->getMessage()}");

                Log::error('Erro importação MF', [
                    'empresa' => $empresa,
                    'pagina'  => $pagina,
                    'erro'    => $e->getMessage(),
                ]);

                $this->line("➡️ Para continuar:");
                $this->line("php artisan omie:import-mf {$empresaArg} --pagina={$pagina}");

                return Command::FAILURE;
            }

        } while ($pagina <= $totalPaginas);

        $this->info("🏁 Importação FINALIZADA com sucesso");
        return Command::SUCCESS;
    }

    /**
     * Importa um movimento individual
     * Retorna TRUE se inseriu | FALSE se atualizou
     */
    private function importarMovimento(array $mov, string $empresa, int $linha): bool
    {
        $det = $mov['detalhes'] ?? [];
        $res = $mov['resumo']   ?? [];

        $omieUid = $this->gerarOmieUID($mov, $empresa);

        if (empty($omieUid)) {
            $this->error("❌ UID vazio na linha {$linha}");
            Log::warning('UID vazio', compact('mov'));
            return false;
        }

        $valor = (float) (
            $res['nValLiquido']
            ?? $det['nValorMovCC']
            ?? $det['nValorTitulo']
            ?? 0
        );

        $registroExiste = OmieMovimentoFinanceiro::where([
            'empresa'  => $empresa,
            'omie_uid' => $omieUid,
        ])->exists();

        OmieMovimentoFinanceiro::updateOrCreate(
            [
                'empresa'  => $empresa,
                'omie_uid' => $omieUid,
            ],
            [
                'codigo_movimento'       => $det['nCodLancamento'] ?? $det['nCodTitulo'] ?? 'N/A',
                'codigo_lancamento_omie' => $det['nCodLancamento'] ?? null,
                'codigo_titulo'          => $det['nCodTitulo'] ?? null,
                'codigo_conta_corrente'  => $det['nCodCC'] ?? null,
                'tipo_movimento'         => $det['cNatureza'] ?? $det['cTipo'] ?? null,
                'origem'                 => $det['cOrigem'] ?? null,
                'data_movimento'         => $this->dt(
                    $det['dDtPagamento']
                    ?? $det['dDtEmissao']
                    ?? $det['dDtRegistro']
                    ?? null
                ),
                'data_competencia' => $this->dt($det['dDtVenc'] ?? null),
                'data_inclusao'    => now(),
                'valor'            => $valor,
                'categorias'       => $mov['categorias'] ?? [],
                'departamentos'    => $mov['departamentos'] ?? [],
                'info'             => $mov,
            ]
        );

        $this->line(
            ($registroExiste ? '♻️ Atualizado' : '➕ Inserido')
            . " | UID={$omieUid} | Valor={$valor}"
        );

        return !$registroExiste;
    }

    /**
     * UID IMUNE A COLISÃO
     */
    private function gerarOmieUID(array $mov, string $empresa): string
    {
        $det = $mov['detalhes'] ?? [];

        return implode('|', [
            'EMP:' . $empresa,
            'TIT:' . ($det['nCodTitulo']     ?? 'X'),
            'LAN:' . ($det['nCodLancamento'] ?? 'X'),
            'OS:'  . ($det['nCodOS']         ?? 'X'),
            'CC:'  . ($det['nCodCC']         ?? 'X'),
            'ORG:' . ($det['cOrigem']        ?? 'X'),
            'REG:' . ($det['dDtRegistro']    ?? 'X'),
            'PAR:' . ($det['cNumParcela']    ?? 'X'),
        ]);
    }

    private function dt($date)
    {
        if (!$date) return null;

        try {
            return str_contains($date, '/')
                ? Carbon::createFromFormat('d/m/Y', substr($date, 0, 10))->format('Y-m-d')
                : Carbon::parse(substr($date, 0, 10))->format('Y-m-d');
        } catch (Throwable) {
            return null;
        }
    }
}
