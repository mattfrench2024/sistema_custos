<?php
namespace App\Services\Omie;

use Illuminate\Support\Facades\Http;

class ContaReceberService
{
    public function exportar(array $payload, array $credenciais)
    {
        return Http::timeout(30)->post(
            'https://app.omie.com.br/api/v1/financas/contareceber/',
            [
                'call'       => 'IncluirContaReceber',
                'app_key'    => $credenciais['app_key'],
                'app_secret' => $credenciais['app_secret'],
                'param'      => [$payload],
            ]
        )->json();
    }
}
