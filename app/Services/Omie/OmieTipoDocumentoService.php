<?php

namespace App\Services\Omie;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class OmieTipoDocumentoService
{
    public function listar(string $appKey, string $appSecret): array
    {
        try {
            $response = Http::timeout(30)
                ->when(
                    app()->environment('local'),
                    fn ($http) => $http->withoutVerifying()
                )
                ->post(
                    'https://app.omie.com.br/api/v1/geral/tiposdoc/',
                    [
                        'call'       => 'PesquisarTipoDocumento',
                        'app_key'    => $appKey,
                        'app_secret' => $appSecret,
                        'param'      => [
                            ['codigo' => '']
                        ],
                    ]
                );

            if (! $response->successful()) {
                throw new \Exception(
                    'Erro Omie Tipos Documento: ' . $response->body()
                );
            }

            return $response->json('tipo_documento_cadastro') ?? [];

        } catch (RequestException $e) {
            throw new \Exception(
                'Falha de conexão com a API da Omie: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }
}
