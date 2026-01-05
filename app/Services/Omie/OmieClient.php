<?php

namespace App\Services\Omie;

use Illuminate\Support\Facades\Http;
use Exception;

class OmieClient
{
    protected string $endpoint = 'https://app.omie.com.br/api/v1';

    public function __construct(
        protected string $appKey,
        protected string $appSecret
    ) {}
    public function post(string $modulo, string $metodo, array $param = []): array
{
    $url = "{$this->endpoint}/{$modulo}/";

    $response = Http::withOptions([
            'verify' => false,
        ])
        ->timeout(60)
        ->post($url, [
            'call' => $metodo,
            'app_key' => $this->appKey,
            'app_secret' => $this->appSecret,
            'param' => [$param],
        ]);

    if (! $response->successful()) {
        throw new Exception('Erro HTTP Omie: ' . $response->status());
    }

    $json = $response->json();

    if (isset($json['faultstring'])) {
        throw new Exception('Erro Omie: ' . $json['faultstring']);
    }

    return $json;
}


    public function listarClientes(int $pagina, int $porPagina = 50): array
    {
        $response = Http::withOptions([
                'verify' => false, // ⚠️ TEMPORÁRIO – apenas ambiente local Windows
            ])
            ->timeout(60)
            ->post(
                "{$this->endpoint}/geral/clientes/",
                [
                    'call' => 'ListarClientes',
                    'app_key' => $this->appKey,
                    'app_secret' => $this->appSecret,
                    'param' => [[
                        'pagina' => $pagina,
                        'registros_por_pagina' => $porPagina,
                    ]]
                ]
            );

        if (! $response->successful()) {
            throw new Exception('Erro HTTP Omie: ' . $response->status());
        }

        $json = $response->json();

        if (isset($json['faultstring'])) {
            throw new Exception('Erro Omie: ' . $json['faultstring']);
        }

        return $json;
    }
}
