<?php

namespace App\Http\Controllers;

use App\Models\OmiePagar;
use Illuminate\Http\Request;
use App\Models\OmieTipoDocumento;
class OmiePagarController extends Controller
{

public function index(Request $request)
{
    $empresaCodigo = $request->get('empresa_codigo');
    $empresaSlug   = $request->get('empresa_slug');
    $tipoDocumento = $request->get('tipo_documento');

    $query = OmiePagar::with([
        'tipoDocumento',
        'fornecedor' => function ($q) use ($empresaCodigo) {
            $q->where('empresa', $empresaCodigo);
        },
    ])
    ->where('empresa', $empresaCodigo);

    if ($tipoDocumento) {
        $query->where('codigo_tipo_documento', $tipoDocumento);
    }

    $contas = $query
        ->orderByDesc('data_vencimento')
        ->paginate(25)
        ->withQueryString();

    $tiposDocumento = OmieTipoDocumento::whereIn(
            'codigo',
            OmiePagar::where('empresa', $empresaCodigo)
                ->select('codigo_tipo_documento')
                ->distinct()
        )
        ->orderBy('descricao')
        ->get();

    return view('omie.pagar.index', [
        'contas'          => $contas,
        'empresaCodigo'   => $empresaCodigo,
        'empresaSlug'     => $empresaSlug,
        'tiposDocumento'  => $tiposDocumento,
        'tipoSelecionado' => $tipoDocumento,
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
