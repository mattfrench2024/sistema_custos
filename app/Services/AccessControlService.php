<?php

namespace App\Services;

use App\Models\User;

class AccessControlService
{
    /**
     * Retorna o redirecionamento após login baseado no papel (role)
     */
    public function redirectAfterLogin(User $user): string
    {
        return match ($user->role->nome) {
            'rh'         => route('rh.dashboard'),
            'ti_admin'   => route('ti.dashboard'),
            'auditoria'  => route('auditoria.dashboard'),
            'financeiro' => route('financeiro.dashboard'),
            default      => route('dashboard'),
        };
    }

    /**
     * Retorna o menu correto para cada role
     */
    public function getMenuForRole(?User $user): array
    {
        if (!$user) {
            return [];
        }

        return match ($user->role->nome) {
            'rh' => [
                ['label' => 'Dashboard', 'route' => 'rh.dashboard'],
                ['label' => 'Funcionários', 'route' => 'rh.funcionarios.index'],
                ['label' => 'Benefícios', 'route' => 'rh.beneficios.index'],
            ],

            'ti_admin' => [
                ['label' => 'Dashboard TI', 'route' => 'ti.dashboard'],
                ['label' => 'Usuários', 'route' => 'ti.users.index'],
                ['label' => 'Logs', 'route' => 'ti.logs.index'],
                ['label' => 'Categorias', 'route' => 'categories.index'],
            ],

            'auditoria' => [
                ['label' => 'Dashboard Auditoria', 'route' => 'auditoria.dashboard'],
                ['label' => 'Auditar Usuários', 'route' => 'auditoria.usuarios.index'],
                ['label' => 'Auditar Movimentos', 'route' => 'auditoria.mov.index'],
            ],

            'financeiro' => [
                ['label' => 'Dashboard Financeiro', 'route' => 'financeiro.dashboard'],
                ['label' => 'Centro de Custos', 'route' => 'financeiro.cc.index'],
                ['label' => 'Pagamentos', 'route' => 'financeiro.pagamentos.index'],
            ],

            default => [
                ['label' => 'Dashboard', 'route' => 'dashboard'],
            ]
        };
    }

    /**
     * Verifica se um usuário pode acessar determinado módulo
     */
    public function canAccess(User $user, string $module): bool
    {
        $map = [
            'usuarios'     => ['ti_admin'],
            'logs'         => ['ti_admin', 'auditoria'],
            'financeiro'   => ['financeiro', 'ti_admin'],
            'rh'           => ['rh', 'ti_admin'],
        ];

        return in_array($user->role->nome, $map[$module] ?? []);
    }
}
