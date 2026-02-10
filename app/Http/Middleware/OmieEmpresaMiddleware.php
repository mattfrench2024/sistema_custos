<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OmieEmpresaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $map = [
            'sv' => '04',
            'vs' => '30',
            'gv' => '36',
            'cs' => '10',
        ];

        $empresaSlug = $request->route('empresa');

        if (! isset($map[$empresaSlug])) {
            abort(404, 'Empresa Omie inválida');
        }

        // Injeta no request
        $request->attributes->set('empresa_codigo', $map[$empresaSlug]);
        $request->attributes->set('empresa_slug', $empresaSlug);

        return $next($request);
    }
}
