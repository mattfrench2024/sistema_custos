<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieOSDocs;
use Throwable;

class OmieImportOSDocs extends Command
{
    protected $signature = 'omie:import-osdocs 
        {empresa : gv | sv | vs}
        {metodo  : ObterOS | ObterNFSe | ObterRecibo | ObterViaUnica}
        {id      : ID do documento na Omie}';

    protected $description = 'Importa documentos específicos de OS da Omie por ID';

    public function handle()
    {
        $empresaArg = $this->argument('empresa');
        $metodo     = $this->argument('metodo');
        $id         = (int) $this->argument('id');

        $empresas = [
            'sv' => '04',
            'vs' => '30',
            'gv' => '36',
        ];

        if (!isset($empresas[$empresaArg])) {
            $this->error('Empresa inválida.');
            return Command::FAILURE;
        }

        $codigoEmpresa = $empresas[$empresaArg];
        $empresaCfg = config("omie.empresas.{$codigoEmpresa}");

        if (!$empresaCfg) {
            $this->error('Configuração Omie não encontrada.');
            return Command::FAILURE;
        }

        $requestMap = [
            'ObterOS'       => ['nIdOs'  => $id],
            'ObterNFSe'     => ['nIdNf'  => $id],
            'ObterRecibo'   => ['nIdRec' => $id],
            'ObterViaUnica' => ['nIdNf'  => $id],
        ];

        if (!isset($requestMap[$metodo])) {
            $this->error('Método inválido.');
            return Command::FAILURE;
        }

        $omie = new OmieClient(
            $empresaCfg['app_key'],
            $empresaCfg['app_secret']
        );

        try {
            $this->info("🚀 Importando {$metodo} — {$empresaCfg['label']} ({$codigoEmpresa})");

            $response = $omie->post(
                'servicos/osdocs',
                $metodo,
                [
                    "{$metodo}Request" => $requestMap[$metodo]
                ]
            );

            OmieOSDocs::updateOrCreate(
                [
                    'empresa'  => $codigoEmpresa,
                    'tipo_doc' => $metodo,
                    'numero'   =>
                        $response['cNumOs']
                        ?? $response['cNumNFSe']
                        ?? $response['cNumRecibo']
                        ?? $response['cNumViaUnica']
                        ?? null,
                ],
                [
                    'serie'       => $response['cSerieNFSe'] ?? $response['cSerieViaUnica'] ?? null,
                    'url_pdf'     =>
                        $response['cPdfOs']
                        ?? $response['cPdfNFSe']
                        ?? $response['cPdfRecibo']
                        ?? $response['cPdfViaUnica']
                        ?? null,
                    'url_xml'     => $response['cXmlNFSe'] ?? null,
                    'link_portal' => $response['cLinkPortal'] ?? null,
                    'cod_status'  => $response['cCodStatus'] ?? null,
                    'des_status'  => $response['cDesStatus'] ?? null,
                    'payload'     => $response,
                ]
            );

            $this->info('✅ Documento importado com sucesso.');
            return Command::SUCCESS;

        } catch (Throwable $e) {
            $this->error("❌ Erro Omie: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
