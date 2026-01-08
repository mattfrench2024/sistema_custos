<?php

namespace App\Services\Omie;

class ContaCorrenteService
{
    protected OmieClient $client;

    public function __construct()
    {
        $this->client = new OmieClient();
    }

    public function listar(string $empresaCodigo, int $pagina = 1, int $porPagina = 100): array
    {
        return $this->client->post(
            $empresaCodigo,
            'geral/contacorrente/',
            [
                'pagina' => $pagina,
                'registros_por_pagina' => $porPagina,
                'apenas_importado_api' => 'N',
            ]
        );
    }
}
