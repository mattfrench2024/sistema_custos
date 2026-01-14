<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieTarefa;
use Throwable;

class OmieImportTarefas extends Command
{
    protected $signature = 'omie:import-tarefas {empresa? : gv | sv | vs}';
    protected $description = 'Importa Tarefas do CRM da Omie com paginação, retry e backoff';

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

        $this->info("🚀 Importando Tarefas CRM — {$empresaCfg['label']} ({$codigoEmpresa})");

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
    'crm/tarefas/',
    'ListarTarefas',
    [
        'tarefaListarRequest' => [
            'pagina' => $pagina,
            'registros_por_pagina' => $registrosPorPagina,

            // ⚠️ IMPORTANTE: evita erro 500 em contas restritas
            // Comente se quiser testar global
            'nCodUsuario' => $empresaCfg['usuario_padrao'] ?? null,
        ]
    ]
);


                    $cadastros = $response['cadastros'] ?? [];
                    $totalPaginas = $response['total_de_paginas'] ?? 1;

                    foreach ($cadastros as $tarefa) {
                        OmieTarefa::updateOrCreate(
                            [
                                'empresa' => $codigoEmpresa,
                                'codigo_tarefa' => $tarefa['nCodTarefa'] ?? null,
                            ],
                            [
                                'codigo_oportunidade' => $tarefa['nCodOp'] ?? null,
                                'codigo_integracao' => $tarefa['cCodInt'] ?? null,
                                'codigo_usuario' => $tarefa['nCodUsuario'] ?? null,
                                'codigo_atividade' => $tarefa['nCodAtividade'] ?? null,
                                'data_tarefa' => $tarefa['dData'] ?? null,
                                'hora_tarefa' => $tarefa['cHora'] ?? null,
                                'importante' => ($tarefa['cImportante'] ?? 'N') === 'S',
                                'urgente' => ($tarefa['cUrgente'] ?? 'N') === 'S',
                                'em_execucao' => ($tarefa['cEmExecucao'] ?? 'N') === 'S',
                                'realizada' => ($tarefa['cRealizada'] ?? 'N') === 'S',
                                'descricao' => $tarefa['cDescricao'] ?? null,
                                'detalhes_oportunidade' => $tarefa['detalhesOportunidade'] ?? null,
                                'payload' => $tarefa,
                            ]
                        );

                        $totalImportados++;
                    }

                    $this->line("📄 Página {$pagina}/{$totalPaginas} — " . count($cadastros) . " tarefas");
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

        $this->info("🎯 Importação concluída — Total: {$totalImportados} tarefas");
        return Command::SUCCESS;
    }
}
