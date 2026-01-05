<?php

namespace App\Http\Controllers;

use App\Models\OmiePagar;
use Illuminate\Http\Request;

class OmiePagarController extends Controller
{
    public function index(Request $request)
{
    $empresaCodigo = $request->get('empresa_codigo');
    $empresaSlug   = $request->get('empresa_slug');

    $contas = OmiePagar::where('empresa', $empresaCodigo)
        ->orderBy('data_vencimento')
        ->paginate(50);

    return view('omie.pagar.index', [
        'contas'        => $contas,
        'empresaCodigo' => $empresaCodigo,
        'empresaSlug'   => $empresaSlug,
    ]);
}


    public function show(Request $request, string $empresa, OmiePagar $pagar)
{
    $empresaCodigo = $request->get('empresa_codigo');

    if ($pagar->empresa !== $empresaCodigo) {
        abort(403, 'Acesso não autorizado a esta conta');
    }

    return view('omie.pagar.show', [
        'conta'         => $pagar,
        'empresaCodigo' => $empresaCodigo,
        'empresaSlug'   => $empresa,
    ]);
}


}
