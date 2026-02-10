<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieMovimentoFinanceiro;
use Carbon\Carbon;
use Throwable;

class OmieImportMFHistorico extends Command
{
    protected $signature = 'omie:import-mf-raw {empresa? : sv | vs | gv | cs}';
    protected $description = 'Importação HISTÓRICA COMPLETA de Movimentos Financeiros Omie (SEM PERDA)';

    public function handle()
    {
        $map = [
            'sv' => '04',
            'vs' => '30',
            'gv' => '36',
            'cs' => '10',
        ];

        $empresaArg = $this->argument('empresa') ?? 'gv';

        if (!isset($map[$empresaArg])) {
            $this->error('Empresa inválida');
            return Command::FAILURE;
        }

        $empresa = $map[$empresaArg];
        $cfg = config("omie.empresas.$empresa");

        // Certifique-se que seu OmieClient aceita array no 3º parametro e o converte para "param" JSON corretamente
        $omie = new OmieClient($cfg['app_key'], $cfg['app_secret']);

        $pagina = 1;
        $porPagina = 50; // Omie geralmente aceita até 500 dependendo do endpoint, mas 50 é seguro
        $totalPaginas = 1;
        $totalImportados = 0;

        $this->info("🚀 Importação HISTÓRICA MF — Empresa {$empresa}");

        do {
            // Correção Principal: Parâmetros diretos, sem 'mfListarRequest'
            $params = [
                'nPagina'       => $pagina,
                'nRegPorPagina' => $porPagina,
            ];

            try {
                $resp = $omie->post(
                    'financas/mf',
                    'ListarMovimentos',
                    $params 
                );
            } catch (Throwable $e) {
                $this->error("Erro na página {$pagina}: " . $e->getMessage());
                sleep(2); // Tenta esperar um pouco antes de continuar ou parar
                continue;
            }

            $movimentos = $resp['movimentos'] ?? [];
            $totalPaginas = (int) ($resp['nTotPaginas'] ?? 0);
            $qtdNaPagina = count($movimentos);
            
            $this->line("📄 Página {$pagina} / {$totalPaginas} | Encontrados: {$qtdNaPagina}");

            if ($qtdNaPagina === 0) {
                break;
            }

            $bar = $this->output->createProgressBar($qtdNaPagina);
            $bar->start();

            foreach ($movimentos as $mov) {
                $this->insertMovimento($empresa, $mov);
                $bar->advance();
                $totalImportados++;
            }

            $bar->finish();
            $this->newLine();

            $pagina++;
            // Pequena pausa para evitar Rate Limit se estiver rodando muito rápido
            // usleep(200000); 

        } while ($pagina <= $totalPaginas);

        $this->success("🏁 IMPORTAÇÃO FINALIZADA. Total processado: {$totalImportados}");
        return Command::SUCCESS;
    }

    private function insertMovimento(string $empresa, array $mov): void
    {
        $det = $mov['detalhes'] ?? [];
        $res = $mov['resumo'] ?? [];

        // 1. Tenta pegar o ID Único Real da Omie
        $codigoMov = $det['nCodMovCC'] ?? $res['nCodMovCC'] ?? null;

        // 2. Se não existir (raro, mas acontece em previsões), gera Hash
        if (!$codigoMov) {
            $codigoMov = md5(json_encode([
                'empresa' => $empresa,
                'data'    => $det['dDtPagamento'] ?? $det['dDtRegistro'] ?? null,
                'valor'   => $det['nValorTitulo'] ?? 0,
                'titulo'  => $det['nCodTitulo'] ?? null,
                'lancto'  => $det['nCodLancamento'] ?? null, // Adicionado para garantir unicidade
                'nat'     => $det['cNatureza'] ?? null,
            ]));
        }

        // Normalização do Valor
        $valor = (float)(
            $res['nValLiquido']
            ?? $det['nValorMovCC']
            ?? $det['nValorTitulo']
            ?? 0
        );

        OmieMovimentoFinanceiro::updateOrCreate(
            [
                'empresa' => $empresa,
                'codigo_movimento' => (string) $codigoMov, // Casting para string para garantir
            ],
            [
                'omie_uid'               => "EMP:$empresa|MOV:$codigoMov",
                'codigo_lancamento_omie' => $det['nCodLancamento'] ?? null,
                'codigo_titulo'          => $det['nCodTitulo'] ?? null,
                'codigo_conta_corrente'  => $det['nCodCC'] ?? null,
                'tipo_movimento'         => $det['cNatureza'] ?? null,
                'origem'                 => $det['cOrigem'] ?? null,
                'data_movimento'         => $this->dt(
                    $det['dDtPagamento']
                    ?? $det['dDtEmissao']
                    ?? $det['dDtRegistro']
                    ?? null
                ),
                'data_competencia'       => $this->dt($det['dDtVenc'] ?? null),
                'data_inclusao'          => now(),
                'valor'                  => $valor,
                'categorias'             => $mov['categorias'] ?? [],
                'departamentos'          => $mov['departamentos'] ?? [],
                'info'                   => $mov,
            ]
        );
    }

    private function dt($date)
    {
        if (!$date) return null;

        try {
            // Limpa string para garantir formato data
            $date = substr($date, 0, 10);
            
            return str_contains($date, '/')
                ? Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d')
                : Carbon::parse($date)->format('Y-m-d');
        } catch (Throwable) {
            return null;
        }
    }
}