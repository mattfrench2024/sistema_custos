<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieDocumentoFiscal;
use Throwable;

class OmieImportDocumentosFiscais extends Command
{
    protected $signature = 'omie:import-documentos-fiscais {empresa? : gv | sv | vs}';
    protected $description = 'Importa XMLs de Documentos Fiscais (NF-e, NFS-e, CT-e, NFC-e) via Painel do Contador';

    public function handle()
    {
        $empresaArg = $this->argument('empresa') ?? 'gv';

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

        $omie = new OmieClient(
            $empresaCfg['app_key'],
            $empresaCfg['app_secret']
        );

        $this->info("🚀 Importando Documentos Fiscais — {$empresaCfg['label']} ({$codigoEmpresa})");

        $pagina = 1;
        $registrosPorPagina = 20;
        $totalPaginas = 1;
        $totalImportados = 0;

        do {
            $tentativa = 0;
            $maxTentativas = 3;

            do {
                try {
                    $response = $omie->post(
    'contador/xml/',
    'ListarDocumentos',
    [
        'xmlListarDocumentosRequest' => [
            'nPagina' => $pagina,
            'nRegPorPagina' => $registrosPorPagina,

            // 🔴 OBRIGATÓRIO
            'cModelo' => '55',

            // 🔴 OBRIGATÓRIO (pode ser dinâmico depois)
            'dEmiInicial' => '2025-01-01',
            'dEmiFinal'   => '2025-12-31',

            // opcional, mas recomendado
            'cAmbiente' => 'P', // P = Produção
        ]
    ]
);


                    $docs = $response['documentosEncontrados'] ?? [];
                    $totalPaginas = $response['nTotPaginas'] ?? 1;

                    foreach ($docs as $doc) {
                        OmieDocumentoFiscal::updateOrCreate(
                            [
                                'empresa' => $codigoEmpresa,
                                'chave' => $doc['nChave'] ?? null,
                            ],
                            [
                                'modelo' => $doc['cModelo'] ?? null,
                                'numero' => $doc['nNumero'] ?? null,
                                'serie' => $doc['cSerie'] ?? null,
                                'data_emissao' => $doc['dEmissao'] ?? null,
                                'hora_emissao' => $doc['hEmissao'] ?? null,
                                'valor' => $doc['nValor'] ?? null,
                                'status' => $doc['cStatus'] ?? null,
                                'omie_id_nf' => $doc['nIdNF'] ?? null,
                                'omie_id_pedido' => $doc['nIdPedido'] ?? null,
                                'omie_id_os' => $doc['nIdOS'] ?? null,
                                'omie_id_ct' => $doc['nIdCT'] ?? null,
                                'omie_id_receb' => $doc['nIdReceb'] ?? null,
                                'omie_id_cupom' => $doc['nIdCupom'] ?? null,
                                'xml' => $doc['cXml'] ?? null,
                                'payload' => $doc,
                            ]
                        );

                        $totalImportados++;
                    }

                    $this->line("📄 Página {$pagina}/{$totalPaginas} — " . count($docs) . " documentos");
                    sleep(3);
                    break;

                } catch (Throwable $e) {
                    $tentativa++;
                    $this->warn("⚠️ Tentativa {$tentativa}/{$maxTentativas} falhou: {$e->getMessage()}");

                    if ($tentativa < $maxTentativas) {
                        $sleep = 5 * $tentativa;
                        $this->info("⏳ Backoff {$sleep}s");
                        sleep($sleep);
                    } else {
                        $this->error("❌ Página {$pagina} ignorada");
                    }
                }
            } while ($tentativa < $maxTentativas);

            $pagina++;

        } while ($pagina <= $totalPaginas);

        $this->info("🎯 Importação concluída — Total: {$totalImportados} documentos");
        return Command::SUCCESS;
    }
}
