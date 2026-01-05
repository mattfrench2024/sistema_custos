<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class VerreschiApiService
{
    protected string $baseUrl;
    protected string $appKey;
    protected string $appSecret;

    public function __construct()
{
    $this->baseUrl = config('services.verreschi.base_url');

    if (!$this->baseUrl) {
        throw new \Exception('VERRESCHI_API_BASE não configurada.');
    }

    $this->appKey    = config('services.verreschi.app_key');
    $this->appSecret = config('services.verreschi.app_secret');
}


    protected function headers(): array
    {
        return [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'X-APP-KEY'     => $this->appKey,
            'X-APP-SECRET'  => $this->appSecret,
        ];
    }

    public function consultarContaReceber(string $documento)
    {
        return Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/contas-receber/{$documento}")
            ->throw()
            ->json();
    }
}
