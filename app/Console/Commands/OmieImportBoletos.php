<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieBoletos;
use Throwable;

class OmieImportBoletos extends Command
{
    protected $signature = 'omie:import-boletos {empresa? : gv | sv | vs | cs}';
    protected $description = 'Importa boletos de Contas a Receber da Omie';

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

        $omie = new OmieClient($empresaCfg['app_key'], $empresaCfg['app_secret']);

        $pagina = 1;
        $registrosPorPagina = 500;
        $totalPaginas = 1;
        $totalImportados = 0;

        $this->info("🚀 Importando boletos de Contas a Receber — {$empresaCfg['label']} ({$codigoEmpresa})");

        do {
            $tentativa = 0;
            $maxTentativas = 3;

            do {
                try {
                    // 1️⃣ Listar todos os títulos de contas a receber
                    $response = $omie->post('financas/contasareceber', 'ListarContasReceber', [
    'contaReceberListarRequest' => [
        'pagina' => $pagina,
        'registros_por_pagina' => $registrosPorPagina,
    ]
]);

$titulos = $response['conta_receber_cadastro'] ?? []; // <- usar o array correto do retorno
$totalPaginas = $response['total_de_paginas'] ?? 1;

foreach ($titulos as $titulo) {
    try {
        // Garantir que passamos pelo menos um dos dois campos
        $nCodTitulo = $titulo['codigo_lancamento_omie'] ?? null;
        $cCodIntTitulo = $titulo['cCodIntTitulo'] ?? null; // se existir

        if (!$nCodTitulo && !$cCodIntTitulo) {
            $this->warn("⚠️ Título sem nCodTitulo ou cCodIntTitulo, ignorado.");
            continue;
        }

        $boleto = $omie->post('financas/contareceberboleto', 'ObterBoleto', [
            'boletoObterRequest' => [
                'nCodTitulo' => $nCodTitulo,
                'cCodIntTitulo' => $cCodIntTitulo,
            ]
        ]);

        if (!empty($boleto['cLinkBoleto'])) {
            OmieBoletos::updateOrCreate(
                ['cCodIntTitulo' => $cCodIntTitulo],
                array_merge($boleto, [
                    'empresa' => $codigoEmpresa,
                    'nCodTitulo' => $nCodTitulo,
                    'cCodIntTitulo' => $cCodIntTitulo,
                    'payload' => $boleto,
                ])
            );
            $this->line("✅ Boleto importado: {$nCodTitulo}");
        } else {
            $this->warn("⚠️ Nenhum boleto disponível para o título {$nCodTitulo}");
        }

    } catch (Throwable $e) {
        $this->warn("⚠️ Falha ao obter boleto do título {$nCodTitulo}: {$e->getMessage()}");
    }
}


                    $this->line("📄 Página {$pagina}/{$totalPaginas} — " . count($titulos) . " títulos processados");
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

        $this->info("🎯 Importação concluída — Total: {$totalImportados} boletos");
        return Command::SUCCESS;
    }
}
