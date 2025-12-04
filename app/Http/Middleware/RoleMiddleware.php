<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Uso: ->middleware('role:ti_superadmin,financeiro')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user) {
            abort(403, 'Não autorizado.');
        }

        $userRole = $user->role->nome ?? null;

        if (!$userRole || !in_array($userRole, $roles)) {
            abort(403, 'Acesso negado para seu perfil.');
        }

        return $next($request);
    }
}
