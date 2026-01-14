<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Omie\OmieClient;
use App\Models\OmieEmpresa;
use Throwable;

class OmieImportEmpresas extends Command
{
    protected $signature = 'omie:import-empresas {empresa? : gv | sv | vs}';
    protected $description = 'Importa Cadastro de Empresas da Omie com paginação, retry e backoff';

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

        $omie = new OmieClient($empresaCfg['app_key'], $empresaCfg['app_secret']);
        $this->info("🚀 Importando Empresas — {$empresaCfg['label']} ({$codigoEmpresa})");

        $pagina = 1;
        $registrosPorPagina = 20;
        $totalImportados = 0;
        $totalPaginas = 1;

        do {
            $tentativa = 0;
            $maxTentativas = 3;

            do {
                try {
                    $response = $omie->post(
                        'geral/empresas/',
                        'ListarEmpresas',
                        [
                            'empresas_list_request' => [
                                'pagina' => $pagina,
                                'registros_por_pagina' => $registrosPorPagina,
                            ]
                        ]
                    );

                    $empresas = $response['empresas_cadastro'] ?? [];
                    $totalPaginas = $response['total_de_paginas'] ?? 1;

                    foreach ($empresas as $empresaData) {
                        OmieEmpresa::updateOrCreate(
                            [
                                'empresa' => $codigoEmpresa,
                                'codigo_empresa' => $empresaData['codigo_empresa'] ?? null,
                            ],
                            [
                                'codigo_empresa_integracao' => $empresaData['codigo_empresa_integracao'] ?? null,
                                'cnpj' => $empresaData['cnpj'] ?? null,
                                'razao_social' => $empresaData['razao_social'] ?? null,
                                'nome_fantasia' => $empresaData['nome_fantasia'] ?? null,
                                'logradouro' => $empresaData['logradouro'] ?? null,
                                'endereco_numero' => $empresaData['endereco_numero'] ?? null,
                                'complemento' => $empresaData['complemento'] ?? null,
                                'bairro' => $empresaData['bairro'] ?? null,
                                'cidade' => $empresaData['cidade'] ?? null,
                                'estado' => $empresaData['estado'] ?? null,
                                'cep' => $empresaData['cep'] ?? null,
                                'codigo_pais' => $empresaData['codigo_pais'] ?? null,
                                'telefone1' => $empresaData['telefone1_numero'] ?? null,
                                'telefone2' => $empresaData['telefone2_numero'] ?? null,
                                'email' => $empresaData['email'] ?? null,
                                'website' => $empresaData['website'] ?? null,
                                'regime_tributario' => $empresaData['regime_tributario'] ?? null,
                                'optante_simples_nacional' => $empresaData['optante_simples_nacional'] ?? null,
                                'gera_nfe' => $empresaData['gera_nfe'] ?? null,
                                'gera_nfse' => $empresaData['gera_nfse'] ?? null,
                                'inativa' => $empresaData['inativa'] ?? null,
                                'payload' => $empresaData,
                            ]
                        );

                        $totalImportados++;
                    }

                    $this->line("📄 Página {$pagina}/{$totalPaginas} — " . count($empresas) . " empresas");
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

        $this->info("🎯 Importação concluída — Total: {$totalImportados} empresas");
        return Command::SUCCESS;
    }
}
