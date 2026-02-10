<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieNotaFiscal;
use App\Models\OmieDfeDoc;
use Carbon\Carbon;
use Throwable;

class OmieListNfe extends Command
{
    protected $signature = 'omie:list-nfe {empresa : sv | vs | gv | cs}';

    protected $description = 'Lista NF-e na Omie e grava no banco';

    public function handle()
    {
        $map = [
            'sv' => '04',
            'vs' => '30',
            'gv' => '36',
            'cs' => '10',
        ];

        $empresaArg = $this->argument('empresa');

        if (!isset($map[$empresaArg])) {
            $this->error('Empresa inválida');
            return Command::FAILURE;
        }

        $empresa = $map[$empresaArg];
        $cfg = config("omie.empresas.$empresa");

        $omie = new OmieClient($cfg['app_key'], $cfg['app_secret']);

        $pagina = 1;
        $porPagina = 50;

        $this->info("🚀 Listando NF-e — Empresa {$empresa}");

        do {
            try {

                $response = $omie->post(
                    'produtos/nf',
                    'ListarNF',
                    [
                        'ListarNFRequest' => [
                            'pagina' => $pagina,
                            'registros_por_pagina' => $porPagina,
                        ]
                    ]
                );

                $lista = $response['nfCadastro'] ?? [];
                $totalPaginas = $response['total_de_paginas'] ?? 1;

                foreach ($lista as $nf) {

                    $nota = OmieNotaFiscal::updateOrCreate(
                        [
                            'empresa' => $empresa,
                            'tipo' => 'NFE',
                            'id_nota_omie' => $nf['nIdNF'],
                        ],
                        [
                            'numero' => $nf['cNumeroNF'] ?? null,
                            'serie' => $nf['cSerieNF'] ?? null,
                            'chave_acesso' => $nf['cChaveNF'] ?? null,
                            'data_emissao' => $this->dt($nf['dDataEmissao'] ?? null),
                            'valor_total' => $nf['nValorTotalNF'] ?? null,
                            'status' => $nf['cStatusNF'] ?? null,
                            'codigo_status' => $nf['cCodStatus'] ?? null,
                            'cnpj_emitente' => $nf['cCNPJEmitente'] ?? null,
                            'cnpj_destinatario' => $nf['cCNPJDestinatario'] ?? null,
                            'payload' => $nf,
                        ]
                    );

                    OmieDfeDoc::updateOrCreate(
                        [
                            'empresa' => $empresa,
                            'tipo_documento' => 'NFE',
                            'id_documento_omie' => $nota->id_nota_omie,
                        ],
                        [
                            'nota_fiscal_id' => $nota->id,
                        ]
                    );
                }

                $this->line("📄 Página {$pagina} — ".count($lista)." NF-e");

                $pagina++;

                sleep(1);

            } catch (Throwable $e) {
                $this->error("❌ Erro página {$pagina}: {$e->getMessage()}");
                break;
            }

        } while ($pagina <= $totalPaginas);

        $this->info("✅ NF-e listadas com sucesso");
        return Command::SUCCESS;
    }

    private function dt(?string $date): ?Carbon
    {
        return $date
            ? Carbon::createFromFormat('d/m/Y', $date)
            : null;
    }
}
