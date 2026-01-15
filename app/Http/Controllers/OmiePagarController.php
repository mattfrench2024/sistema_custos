<?php

namespace App\Http\Controllers;
use App\Models\OmiePagarView;
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

    // Query base com relacionamentos
    $query = OmiePagarView::with([
    'tipoDocumento',
    'fornecedor',
    'categoria',
    'contaCorrente',
])
->where('empresa', $empresaCodigo)
->whereNotNull('data_vencimento')
->whereBetween('data_vencimento', [
    now()->subYears(5),
    now()->addYears(1),
]);


    // Filtro opcional por tipo de documento
    if ($tipoDocumento) {
        $query->where('codigo_tipo_documento', $tipoDocumento);
    }
// Filtro por mês e ano
$mes = $request->get('mes');
$ano = $request->get('ano');

if($mes && $ano){
    $query->whereYear('data_vencimento', $ano)
          ->whereMonth('data_vencimento', $mes);
} elseif ($ano) {
    $query->whereYear('data_vencimento', $ano);
} elseif ($mes) {
    $query->whereMonth('data_vencimento', $mes);
}

    // Ordenação segura por data
    $contas = $query
        ->orderBy('data_vencimento', 'desc')
        ->paginate(25)
        ->withQueryString();

    // Lista de tipos de documento usados na empresa
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
public function create(Request $request)
{
    $empresaCodigo = $request->get('empresa_codigo'); // obrigatório para filtrar
    $empresaSlug   = $request->get('empresa_slug');

    // Buscar dados relacionados apenas da empresa atual
    $fornecedores = \App\Models\OmieCliente::where('empresa', $empresaCodigo)
                        ->orderBy('razao_social')
                        ->get();

    $tiposDocumento = \App\Models\OmieTipoDocumento::orderBy('descricao')->get();
    $categorias     = \App\Models\OmieCategoria::where('empresa', $empresaCodigo)
                        ->orderBy('descricao')
                        ->get();

    $contasCorrentes = \App\Models\OmieContaCorrente::where('empresa_codigo', $empresaCodigo)
                        ->orderBy('descricao')
                        ->get();

    return view('omie.pagar.create', compact(
        'empresaCodigo', 'empresaSlug',
        'fornecedores', 'tiposDocumento', 'categorias', 'contasCorrentes'
    ));
}

public function store(Request $request)
{
    $request->validate([
        'codigo_cliente_fornecedor' => 'required',
        'codigo_tipo_documento'     => 'required',
        'codigo_categoria'          => 'required',
        'data_emissao'              => 'required|date',
        'data_vencimento'           => 'required|date',
        'valor_documento'           => 'required|numeric',
        'id_conta_corrente'         => 'nullable|exists:omie_contas_correntes,id',
    ]);

    $empresaCodigo = $request->get('empresa_codigo');

    $conta = OmiePagar::create(array_merge($request->all(), [
        'empresa' => $empresaCodigo,
        'status_titulo' => 'A VENCER',
    ]));

    return redirect()->route('omie.pagar.index', [
        'empresa'        => $request->get('empresa_slug'),
        'empresa_codigo' => $empresaCodigo,
    ])->with('success', 'Pagamento cadastrado com sucesso!');
}
public function toggleStatus(
    Request $request,
    string $empresa,
    OmiePagar $pagar
) {
    $empresaCodigo = $request->get('empresa_codigo');

    // Segurança por empresa
    if ($pagar->empresa !== $empresaCodigo) {
        abort(403, 'Acesso não autorizado');
    }

    if ($pagar->status_titulo === 'CANCELADO') {
        return back()->with('error', 'Conta cancelada não pode ser alterada.');
    }

    // Toggle
    $pagar->status_titulo = $pagar->status_titulo === 'PAGO'
        ? 'A VENCER'
        : 'PAGO';

    $pagar->save();

    return back()->with('success', 'Status atualizado com sucesso.');
}


}
