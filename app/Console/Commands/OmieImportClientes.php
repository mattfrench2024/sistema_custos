<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieCliente;

class OmieImportClientes extends Command
{
    protected $signature = 'omie:import-clientes {empresa? : gv | sv | vs}';
    protected $description = 'Importa clientes da Omie por empresa';

    public function handle()
    {
        $empresa = $this->argument('empresa') ?? 'gv';

        $map = [
            'sv' => ['codigo' => '04'],
            'vs' => ['codigo' => '30'],
            'gv' => ['codigo' => '36'],
        ];

        if (! isset($map[$empresa])) {
            $this->error('Empresa inválida. Use: gv | sv | vs');
            return Command::FAILURE;
        }

        $codigoEmpresa = $map[$empresa]['codigo'];
        $empresaCfg = config("omie.empresas.{$codigoEmpresa}");

        if (! $empresaCfg || empty($empresaCfg['app_key'])) {
            $this->error('Configuração Omie não encontrada para a empresa.');
            return Command::FAILURE;
        }

        $this->info("🚀 Importando clientes — {$empresaCfg['label']}");

        $omie = new OmieClient(
            $empresaCfg['app_key'],
            $empresaCfg['app_secret']
        );

        $pagina = 1;
        $totalPaginas = 1;
        $importados = 0;

        do {
            $this->line("📄 Página {$pagina} de {$totalPaginas}");

            $response = $omie->listarClientes($pagina, 50);
            $totalPaginas = $response['total_de_paginas'] ?? 1;

            foreach ($response['clientes_cadastro'] ?? [] as $cliente) {

                OmieCliente::updateOrCreate(
                    [
                        'empresa' => $codigoEmpresa,
                        'codigo_cliente_omie' => $cliente['codigo_cliente_omie'],
                    ],
                    [
                        'razao_social'  => $cliente['razao_social'] ?? null,
                        'nome_fantasia' => $cliente['nome_fantasia'] ?? null,
                        'cnpj_cpf'      => $cliente['cnpj_cpf'] ?? null,
                        'cidade'        => $cliente['cidade'] ?? null,
                        'estado'        => $cliente['estado'] ?? null,
                        'tags'          => $cliente['tags'] ?? [],
                        'payload'       => $cliente,
                    ]
                );

                $importados++;
            }

            $pagina++;
        } while ($pagina <= $totalPaginas);

        $this->info("✅ {$empresaCfg['label']} — {$importados} clientes importados");

        return Command::SUCCESS;
    }
}
