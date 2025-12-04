<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Services\AccessControlService;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar service para injeção automática
        $this->app->singleton(AccessControlService::class, function ($app) {
            return new AccessControlService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Compartilhar info de controle de acesso com todas as views
        View::composer('*', function ($view) {
            $user = Auth::user();
            $acs  = app(AccessControlService::class);

            $view->with([
                'authUser'  => $user,
                'userRole'  => $user?->role?->nome,
                'menuItems' => $acs->getMenuForRole($user),
            ]);
        });
    }
}
