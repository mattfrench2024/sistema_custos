<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * 📌 Middleware global (sempre executado)
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];


    protected $commands = [
    \App\Console\Commands\OmieImportContasReceber::class,
];

    /**
     * 📌 Grupos de middleware
     */
    protected $middlewareGroups = [

        // 🔐 Web (Sessão, CSRF, Cookies, Blade, Auth, etc)
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,

            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,

            // ⚡ Importante para login persistente
            \Illuminate\Auth\Middleware\AuthenticateSession::class,

            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        // ⚙️ API — sem sessão, sem CSRF
        'api' => [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

            // Throttle para API (opcional)
            // 'throttle:api',
        ],
    ];

    /**
     * 📌 Middlewares individuais aplicáveis por rota
     */
    protected $routeMiddleware = [

        // 🔐 Autenticação e controle de acesso
        'auth' => \App\Http\Middleware\Authenticate::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'role' => \App\Http\Middleware\RoleMiddleware::class,

        // 🔑 Segurança extra
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // 🛡️ Assinatura, cache e proteção
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
    ];
}
