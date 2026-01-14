<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    $user = Auth::user();
    $role = $user->role->nome ?? null; // <-- Correção aqui

    return redirect()->to(match ($role) {
        'ti', 'ti_superadmin', 'admin', 'ti_admin' => '/dashboard/admin',
        'financeiro' => '/financeiro/analitico',
        'rh', 'dp' => '/dashboard/rh',
        'auditoria', 'diretoria' => '/dashboard/auditoria',
        default => '/dashboard',
    });
}


    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
