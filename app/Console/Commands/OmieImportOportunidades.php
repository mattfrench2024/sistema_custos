<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieOportunidade;
use Throwable;

class OmieImportOportunidades extends Command
{
    protected $signature = 'omie:import-oportunidades {empresa? : gv | sv | vs}';
    protected $description = 'Importa Oportunidades do CRM Omie (multiempresa)';

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

        $omie = new OmieClient(
            $empresaCfg['app_key'],
            $empresaCfg['app_secret']
        );

        $this->info("🚀 Importando Oportunidades — {$empresaCfg['label']} ({$codigoEmpresa})");

        $pagina = 1;
        $totalPaginas = 1;
        $totalImportados = 0;

        do {
            try {
                $response = $omie->post(
    'crm/oportunidades/',
    'ListarOportunidades',
    [
        'opListarRequest' => [
            'pagina' => $pagina,
            'registros_por_pagina' => 20,

            // 🚨 OBRIGATÓRIO NA PRÁTICA
            'exibir_detalhes' => 'S',
        ]
    ]
);



                // ✅ CAMPO CORRETO
                $cadastros = $response['cadastros'] ?? [];
                $totalPaginas = $response['total_de_paginas'] ?? 1;

                foreach ($cadastros as $item) {
                    $id = $item['identificacao'] ?? [];
                    $fase = $item['fasesStatus'] ?? [];
                    $prev = $item['previsaoTemp'] ?? [];
                    $info = $item['outrasInf'] ?? [];

                    OmieOportunidade::updateOrCreate(
                        [
                            'empresa' => $codigoEmpresa,
                            'codigo_oportunidade' => $id['nCodOp'] ?? null,
                        ],
                        [
                            'titulo' => $id['cDesOp'] ?? null,
                            'codigo_integracao' => $id['cCodIntOp'] ?? null,

                            'etapa' => $fase['nCodFase'] ?? null,
                            'status' => $fase['nCodStatus'] ?? null,

                            'codigo_cliente' => $id['nCodConta'] ?? null,

                            'valor_previsto' => $item['ticket']['nTicket'] ?? null,

                            'data_prevista_fechamento' =>
                                isset($prev['nMesPrev'], $prev['nAnoPrev'])
                                    ? "{$prev['nAnoPrev']}-{$prev['nMesPrev']}-01"
                                    : null,

                            'codigo_usuario_responsavel' => $id['nCodVendedor'] ?? null,

                            'data_criacao' =>
                                isset($info['dInclusao'], $info['hInclusao'])
                                    ? "{$info['dInclusao']} {$info['hInclusao']}"
                                    : null,

                            'data_alteracao' =>
                                isset($info['dAlteracao'], $info['hAlteracao'])
                                    ? "{$info['dAlteracao']} {$info['hAlteracao']}"
                                    : null,

                            'payload' => $item,
                        ]
                    );

                    $totalImportados++;
                }

                $this->line("📄 Página {$pagina}/{$totalPaginas} — " . count($cadastros) . " oportunidades");
                sleep(2);
                $pagina++;

            } catch (Throwable $e) {
                $this->error("❌ Falha na página {$pagina}: {$e->getMessage()}");
                break;
                
            }
            catch (Throwable $e) {
    if (str_contains($e->getMessage(), '500')) {
        $this->warn("⚠️ CRM sem oportunidades ou módulo inativo — ignorado");
        return Command::SUCCESS;
    }

    throw $e;
}


        } while ($pagina <= $totalPaginas);

        $this->info("🎯 Importação concluída — {$totalImportados} oportunidades");
        return Command::SUCCESS;
    }
}
