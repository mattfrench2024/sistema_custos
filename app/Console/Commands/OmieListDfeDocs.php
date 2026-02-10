<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieDfeDoc;
use Throwable;

class OmieListDfeDocs extends Command
{
    protected $signature = 'omie:list-dfe-docs 
        {empresa : sv | vs | gv | cs}
        {tipo : nfe | cte | cupom}';

    protected $description = 'Lista documentos fiscais (DFE) na Omie';

    public function handle()
    {
        $mapEmpresa = [
            'sv' => '04',
            'vs' => '30',
            'gv' => '36',
            'cs' => '10',
        ];

        $empresaArg = $this->argument('empresa');
        $tipo = strtoupper($this->argument('tipo'));

        if (!isset($mapEmpresa[$empresaArg])) {
            $this->error('Empresa inválida');
            return Command::FAILURE;
        }

        $empresa = $mapEmpresa[$empresaArg];
        $cfg = config("omie.empresas.$empresa");

        $omie = new OmieClient($cfg['app_key'], $cfg['app_secret']);

        $pagina = 1;
        $porPagina = 50;

        $this->info("🚀 Listando {$tipo} — Empresa {$empresa}");

        do {
            try {

                $response = $omie->post(
                    'produtos/dfedocs',
                    'ListarDocumentos',
                    [
                        'ListarDocumentosRequest' => [
                            'cTipoDocumento' => $tipo,
                            'nPagina' => $pagina,
                            'nRegPorPagina' => $porPagina,
                        ]
                    ]
                );

                $docs = $response['documentos'] ?? [];
                $totalPaginas = $response['nTotPaginas'] ?? 1;

                foreach ($docs as $doc) {
                    OmieDfeDoc::updateOrCreate(
                        [
                            'empresa' => $empresa,
                            'tipo_documento' => $tipo,
                            'id_documento_omie' => $doc['nIdDocumento'],
                        ],
                        [
                            'numero' => $doc['cNumero'] ?? null,
                            'chave_acesso' => $doc['cChaveAcesso'] ?? null,
                            'data_emissao' => isset($doc['dEmissao'])
                                ? \Carbon\Carbon::createFromFormat('d/m/Y', $doc['dEmissao'])
                                : null,
                            'status_codigo' => $doc['cStatus'] ?? null,
                            'status_descricao' => $doc['cDescricaoStatus'] ?? null,
                            'payload' => $doc,
                        ]
                    );
                }

                $this->line("📄 Página {$pagina} — ".count($docs)." documentos");
                $pagina++;
                sleep(1);

            } catch (Throwable $e) {
                $this->error("❌ Erro página {$pagina}: {$e->getMessage()}");
                break;
            }

        } while ($pagina <= $totalPaginas);

        $this->info("✅ Documentos listados com sucesso");
        return Command::SUCCESS;
    }
}
