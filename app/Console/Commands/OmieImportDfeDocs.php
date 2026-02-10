<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieDfeDoc;
use Carbon\Carbon;
use Throwable;

class OmieImportDfeDocs extends Command
{
    protected $signature = 'omie:import-dfe-docs 
        {empresa : sv | vs | gv | cs}
        {tipo : nfe | cte | cupom}';

    protected $description = 'Importa XML/PDF de documentos fiscais via DfeDocs';

    public function handle()
    {
        $mapEmpresa = [
            'sv' => '04',
            'vs' => '30',
            'gv' => '36',
            'cs' => '10',
        ];

        $empresaArg = $this->argument('empresa');
        $tipo = strtolower($this->argument('tipo'));

        if (!isset($mapEmpresa[$empresaArg])) {
            $this->error('Empresa inválida');
            return Command::FAILURE;
        }

        if (!in_array($tipo, ['nfe', 'cte', 'cupom'])) {
            $this->error('Tipo inválido');
            return Command::FAILURE;
        }

        $empresa = $mapEmpresa[$empresaArg];
        $cfg = config("omie.empresas.$empresa");

        $omie = new OmieClient(
            $cfg['app_key'],
            $cfg['app_secret']
        );

        $docs = OmieDfeDoc::where('empresa', $empresa)
            ->where('tipo_documento', strtoupper($tipo))
            ->whereNull('xml')
            ->get();

        if ($docs->isEmpty()) {
            $this->warn('⚠️ Nenhum documento pendente para importação');
            return Command::SUCCESS;
        }

        $this->info("🚀 Importando {$tipo} — Empresa {$empresa}");
        $this->info("📄 Total pendente: {$docs->count()}");

        foreach ($docs as $doc) {
            try {

                match ($tipo) {
                    'nfe'   => $this->importNfe($omie, $doc),
                    'cte'   => $this->importCte($omie, $doc),
                    'cupom' => $this->importCupom($omie, $doc),
                };

                $this->line("✅ Documento {$doc->id_documento_omie} importado");
                sleep(1);

            } catch (Throwable $e) {
                $this->error("❌ Documento {$doc->id_documento_omie}: {$e->getMessage()}");
            }
        }

        $this->info("🎯 Importação finalizada");
        return Command::SUCCESS;
    }

    /**
     * =====================
     * NF-e
     * =====================
     */
    private function importNfe(OmieClient $omie, OmieDfeDoc $doc): void
    {
        $res = $omie->post(
            'produtos/dfedocs',
            'ObterNFe',
            [
                'ObterNFeRequest' => [
                    'nIdNfe' => $doc->id_documento_omie
                ]
            ]
        );

        $this->updateDoc($doc, [
            'numero' => $res['cNumNfe'] ?? null,
            'chave_acesso' => $res['nChaveNfe'] ?? null,
            'data_emissao' => $this->dt($res['dDataEmisNfe'] ?? null),
            'xml' => $res['cXmlNfe'] ?? null,
            'pdf_url' => $res['cPdf'] ?? null,
            'portal_url' => $res['cLinkPortal'] ?? null,
            'status_codigo' => $res['cCodStatus'] ?? null,
            'status_descricao' => $res['cDesStatus'] ?? null,
            'payload' => $res,
        ]);
    }

    /**
     * =====================
     * CTe
     * =====================
     */
    private function importCte(OmieClient $omie, OmieDfeDoc $doc): void
    {
        $res = $omie->post(
            'produtos/dfedocs',
            'ObterCTe',
            [
                'ObterCTeRequest' => [
                    'nIdCTe' => $doc->id_documento_omie
                ]
            ]
        );

        $this->updateDoc($doc, [
            'numero' => $res['cNumCTe'] ?? null,
            'chave_acesso' => $res['nChaveCTe'] ?? null,
            'data_emissao' => $this->dt($res['dDataEmisCTe'] ?? null),
            'xml' => $res['cXmlCTe'] ?? null,
            'pdf_url' => $res['cPdf'] ?? null,
            'portal_url' => $res['cLinkPortal'] ?? null,
            'status_codigo' => $res['cCodStatus'] ?? null,
            'status_descricao' => $res['cDesStatus'] ?? null,
            'payload' => $res,
        ]);
    }

    /**
     * =====================
     * CUPOM FISCAL
     * =====================
     */
    private function importCupom(OmieClient $omie, OmieDfeDoc $doc): void
    {
        $res = $omie->post(
            'produtos/dfedocs',
            'ObterCupom',
            [
                'ObterCupomRequest' => [
                    'nIdCupom' => $doc->id_documento_omie
                ]
            ]
        );

        $this->updateDoc($doc, [
            'numero' => $res['cNumCupom'] ?? null,
            'chave_acesso' => $res['nChaveCupom'] ?? null,
            'data_emissao' => $this->dt($res['dDataEmisCupom'] ?? null),
            'xml' => $res['cXmlCupom'] ?? null,
            'pdf_url' => $res['cPdf'] ?? null,
            'portal_url' => $res['cLinkPortal'] ?? null,
            'status_codigo' => $res['cCodStatus'] ?? null,
            'status_descricao' => $res['cDesStatus'] ?? null,
            'payload' => $res,
        ]);
    }

    /**
     * =====================
     * Helpers
     * =====================
     */
    private function updateDoc(OmieDfeDoc $doc, array $data): void
    {
        $doc->update($data);
    }

    private function dt(?string $date): ?Carbon
    {
        return $date
            ? Carbon::createFromFormat('d/m/Y', $date)
            : null;
    }
}
