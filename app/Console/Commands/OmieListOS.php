<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use Illuminate\Support\Facades\DB;
use Throwable;

class OmieListOS extends Command
{
    protected $signature = 'omie:list-os {empresa : gv | sv | vs}';

    protected $description = 'Lista Ordens de Serviço da Omie e armazena os IDs';

    public function handle()
    {
        $empresaArg = $this->argument('empresa');

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

        $omie = new OmieClient(
            $empresaCfg['app_key'],
            $empresaCfg['app_secret']
        );

        $pagina = 1;
        $totalImportados = 0;

        try {
            do {
                $this->info("📄 Listando OS — Página {$pagina}");

                $response = $omie->post(
                    'servicos/os',
                    'ListarOS',
                    [
                        'ListarOSRequest' => [
                            'cEtapa' => '10', // 🔴 OBRIGATÓRIO
                            'pagina' => $pagina,
                            'registros_por_pagina' => 50,
                        ]
                    ]
                );

                $lista = $response['osCadastro'] ?? [];
                $totalPaginas = $response['total_de_paginas'] ?? 1;

                foreach ($lista as $os) {
                    DB::table('omie_os')->updateOrInsert(
                        [
                            'empresa' => $codigoEmpresa,
                            'nIdOs'   => $os['nIdOs'],
                        ],
                        [
                            'cNumOs'  => $os['cNumOS'] ?? null,
                            'payload' => json_encode($os, JSON_UNESCAPED_UNICODE),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );

                    $totalImportados++;
                }

                $pagina++;
            } while ($pagina <= $totalPaginas);

            $this->info("✅ OS importadas: {$totalImportados}");
            return Command::SUCCESS;

        } catch (Throwable $e) {
            $this->error("❌ Erro Omie: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
