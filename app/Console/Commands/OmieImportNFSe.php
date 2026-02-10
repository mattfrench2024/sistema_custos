<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieNotaFiscal;
use Carbon\Carbon;
use Throwable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class OmieImportNFSe extends Command
{
    protected $signature = 'omie:import-nfse {empresa? : sv | vs | gv | cs}
                            {--debug : Mostra detalhes extras de debug}
                            {--dry-run : Apenas simula, não salva nada}';

    protected $description = 'Importa NFSe Omie - versão final com correções para XML/PDF via nfse';

    public function handle()
    {
        $this->newLine(2);
        $this->info('══════════════════════════════════════════════════════════════════════════════');
        $this->info('               IMPORTAÇÃO COMPLETA DE NFSe - VERSÃO FINAL 2025              ');
        $this->info('══════════════════════════════════════════════════════════════════════════════');
        $this->newLine();

        $map = ['sv' => '04', 'vs' => '30', 'gv' => '36', 'cs' => '10'];
        $empresaArg = $this->argument('empresa') ?? 'gv';
        $debug      = $this->option('debug');
        $dryRun     = $this->option('dry-run');

        if (!isset($map[$empresaArg])) {
            $this->error("Empresa inválida");
            return Command::FAILURE;
        }

        $empresa = $map[$empresaArg];
        $cfg = config("omie.empresas.{$empresa}");

        if (empty($cfg['app_key']) || empty($cfg['app_secret'])) {
            $this->error("Credenciais Omie não encontradas");
            return Command::FAILURE;
        }

        $omie = new OmieClient($cfg['app_key'], $cfg['app_secret']);

        $pagina          = 1;
        $porPagina       = 50;
        $totalPaginas    = 999; // valor inicial alto para garantir que rode até acabar
        $totalImportados = 0;
        $totalTentativasFalha = 0;

        $dtInicio = '01/01/2024';
        $dtFim    = Carbon::now()->addYear()->format('d/m/Y');

        $this->info("Empresa ........: {$empresaArg} (código {$empresa})");
        $this->info("Período ........: {$dtInicio} ~ {$dtFim}");
        $this->info("Dry-run ........: " . ($dryRun ? 'SIM' : 'NÃO'));
        $this->info("Debug ..........: " . ($debug ? 'SIM' : 'NÃO'));
        $this->newLine();

        while ($pagina <= $totalPaginas && $totalTentativasFalha < 5) {
            $this->comment(str_repeat('─', 80));
            $this->info("Página {$pagina} em processamento...");

            try {
                $params = [
                    'nPagina'       => $pagina,
                    'nRegPorPagina' => $porPagina,
                    'dEmiInicial'   => $dtInicio,
                    'dEmiFinal'     => $dtFim,
                ];

                $resp = $omie->post('servicos/nfse', 'ListarNFSEs', $params);

                if ($debug) {
                    $this->line("Resposta página {$pagina}:");
                    $this->line(json_encode($resp, JSON_PRETTY_PRINT));
                }

                // Extração robusta
                $lista = $resp['nfseEncontradas'] 
                      ?? $resp['nfseListarResponse']['nfseEncontradas'] 
                      ?? (is_array($resp) && isset($resp[0]['Cabecalho']) ? $resp : []);

                $totalPaginas    = (int) ($resp['nTotPaginas'] ?? $resp['nfseListarResponse']['nTotPaginas'] ?? 1);
                $totalRegistros  = (int) ($resp['nTotRegistros'] ?? $resp['nfseListarResponse']['nTotRegistros'] ?? count($lista));

                $qtd = count($lista);

                $this->info("  Total páginas ......: {$totalPaginas}");
                $this->info("  Total registros ....: {$totalRegistros}");
                $this->info("  Registros nesta pág.: {$qtd}");

                if ($qtd === 0) {
                    $this->info("Página vazia → finalizando busca.");
                    break;
                }

                $bar = $this->output->createProgressBar($qtd);
                $bar->start();

                foreach ($lista as $nfse) {
                    $this->processarNFSe($omie, $empresa, $nfse, $dryRun, $debug);
                    $bar->advance();
                    $totalImportados++;
                }

                $bar->finish();
                $this->newLine();

                $pagina++;
                $totalTentativasFalha = 0; // reset se sucesso
                sleep(1);

            } catch (Throwable $e) {
                $this->error("Erro página {$pagina}: " . $e->getMessage());
                $totalTentativasFalha++;
                sleep(4);
                $pagina++;
            }
        }

        $this->newLine(2);
        $this->line("FINALIZADO!");
        $this->info("Total NFSe processadas: {$totalImportados}");
        $this->info("Empresa: {$empresa} | Período: {$dtInicio} ~ {$dtFim}");
        return Command::SUCCESS;
    }

    private function processarNFSe(OmieClient $omie, string $empresa, array $nfse, bool $dryRun, bool $debug): void
    {
        $cabecalho = $nfse['Cabecalho'] ?? [];
        $idOmie    = $cabecalho['nCodNF'] ?? null;

        if (!$idOmie) return;

        $numero = $cabecalho['nNumeroNFSe'] ?? '(sem nº)';
        $valor  = number_format($cabecalho['nValorNFSe'] ?? 0, 2, ',', '.');

        if ($dryRun) {
            $this->line("  [DRY] NFSe {$idOmie} - {$numero} - R$ {$valor}");
            return;
        }

        $nota = OmieNotaFiscal::updateOrCreate(
            ['empresa' => $empresa, 'id_nota_omie' => $idOmie, 'tipo' => 'NFSE'],
            [
                'numero'            => $cabecalho['nNumeroNFSe'] ?? null,
                'serie'             => $cabecalho['cSerieNFSe'] ?? null,
                'chave_acesso'      => $cabecalho['cCodigoVerifNFSe'] ?? null,
                'valor_total'       => $cabecalho['nValorNFSe'] ?? 0,
                'status'            => $cabecalho['cStatusNFSe'] ?? null,
                'cnpj_emitente'     => $cabecalho['cCNPJEmissor'] ?? null,
                'cnpj_destinatario' => $cabecalho['cCNPJDestinatario'] ?? null,
                'data_emissao'      => $this->dt($nfse['Emissao']['cDataEmissao'] ?? null),
                'payload'           => $nfse,
            ]
        );

        $this->line("  NFSe {$idOmie} - {$numero} - R$ {$valor} (ID banco: {$nota->id})");

        $this->downloadXmlAndPdf($omie, $idOmie, $cabecalho, $empresa, $nota, $debug);
    }

    private function downloadXmlAndPdf(OmieClient $omie, int $idOmie, array $cabecalho, string $empresa, OmieNotaFiscal $nota, bool $debug)
    {
        // Parâmetros comuns
        $params = ['nCodNF' => $idOmie];
        if (isset($cabecalho['nNumeroNFSe']) && isset($cabecalho['cSerieNFSe'])) {
            $params = array_merge($params, [
                'nNumeroNFSe' => $cabecalho['nNumeroNFSe'],
                'cSerieNFSe' => $cabecalho['cSerieNFSe'],
            ]);
        }

        // Tentativas para XML
        $xmlMethods = [
            'ObterXMLNFSe', 'ObterXmlNfse', 'ConsultarNFSe', 'PesquisarNFSe', 'ObterNFSe', 'RetornarXmlNfse', 'ConsultarXmlNfse', 'ConsultarNFSeXml'
        ];
        $xml = null;

        foreach ($xmlMethods as $method) {
            try {
                if ($debug) $this->comment("    Tentando XML: {$method}");
                $resp = $omie->post('servicos/nfse', $method, $params);
                $xml = $resp['cRetorno'] ?? $resp['cXml'] ?? $resp['xml'] ?? $resp['sXml'] ?? $resp['conteudoXml'] ?? $resp['nfseXml'] ?? null;
                if ($xml && strlen($xml) > 200) {
                    if ($debug) $this->info("      Sucesso em {$method}");
                    break;
                }
            } catch (Throwable $e) {
                if ($debug) $this->warn("      Falhou {$method}: " . substr($e->getMessage(), 0, 80));
            }
        }

        if ($xml && strlen($xml) > 200) {
            $path = "omie/nfse/{$empresa}/xml/{$idOmie}.xml";
            Storage::put($path, $xml);
            $nota->possui_xml = true;
            $nota->save();
            $this->info("    → XML salvo (" . number_format(strlen($xml)/1024, 1) . " KB)");
        } else {
            $this->warn("    → Falha XML (nenhum método funcionou)");
        }

        // Tentativas para PDF
        $pdfMethods = [
            'ObterPDFNFSe', 'ObterPdfNfse', 'GerarPDFNFSe', 'GerarPdfNfse', 'ConsultarNFSe', 'PesquisarNFSe', 'ObterNFSe', 'RetornarPdfNfse', 'ConsultarPdfNfse', 'ConsultarNFSePdf', 'GerarNFSePdf'
        ];
        $pdfBase64 = null;

        foreach ($pdfMethods as $method) {
            try {
                if ($debug) $this->comment("    Tentando PDF: {$method}");
                $resp = $omie->post('servicos/nfse', $method, $params);
                $pdfBase64 = $resp['cRetorno'] ?? $resp['cPdf'] ?? $resp['pdf'] ?? $resp['base64'] ?? $resp['base64Pdf'] ?? $resp['conteudoPdf'] ?? $resp['nfsePdf'] ?? null;
                if ($pdfBase64 && strlen($pdfBase64) > 500) {
                    if ($debug) $this->info("      Sucesso em {$method}");
                    break;
                }
            } catch (Throwable $e) {
                if ($debug) $this->warn("      Falhou {$method}: " . substr($e->getMessage(), 0, 80));
            }
        }

        if ($pdfBase64 && strlen($pdfBase64) > 500) {
            $binario = base64_decode($pdfBase64);
            $path = "omie/nfse/{$empresa}/pdf/{$idOmie}.pdf";
            Storage::put($path, $binario);
            $nota->possui_pdf = true;
            $nota->save();
            $this->info("    → PDF salvo (" . number_format(strlen($binario)/1024, 1) . " KB)");
        } else {
            $this->warn("    → Falha PDF (nenhum método funcionou)");
        }
    }

    private function dt($date)
    {
        if (!$date) return null;
        try {
            return Carbon::createFromFormat('d/m/Y', substr($date, 0, 10))->format('Y-m-d');
        } catch (Throwable) {
            return null;
        }
    }
}