<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OmieReceber;
use App\Models\OmieContaCorrente;

class OmieReceberController extends Controller
{// OmieReceberController.php
protected $empresaNomes = [
    'vs' => 'Verreschi Soluções',
    'gv' => 'Grupo Verreschi',
    'sv' => 'Sociedade Advogados Verreschi',
    'cs' => 'Consultoria Soluções',
];
protected $empresas = [
    'vs' => '30',
    'gv' => '36',
    'sv' => '04',
    'cs' => '10',
];


public function index(Request $request, $empresa)
{
    if (! isset($this->empresas[$empresa])) {
        abort(404);
    }

    $empresaId   = $this->empresas[$empresa];
    $empresaNome = $this->empresaNomes[$empresa];

    $mes = $request->get('mes');
    $ano = $request->get('ano');

    $query = OmieReceber::with([
            'cliente'   => fn ($q) => $q->where('empresa', $empresaId),
            'categoria' => fn ($q) => $q->where('empresa', $empresaId),
        ])
        ->where('empresa', $empresaId)
        ->whereNotNull('data_vencimento');

    // Filtro por mês e ano
    if ($mes && $ano) {
        $query->whereYear('data_vencimento', $ano)
              ->whereMonth('data_vencimento', $mes);
    } elseif ($ano) {
        $query->whereYear('data_vencimento', $ano);
    } elseif ($mes) {
        $query->whereMonth('data_vencimento', $mes);
    }

    // 🔥 Ordenação por proximidade da data atual
    $receber = $query
        ->orderByRaw('ABS(DATEDIFF(data_vencimento, CURDATE()))')
        ->paginate(25)
        ->withQueryString();

    return view('omie.receber.index', compact(
        'receber',
        'empresa',
        'empresaNome'
    ));
}




public function show($empresa, OmieReceber $receber)
{
    if (
        ! isset($this->empresas[$empresa]) ||
        $receber->empresa !== $this->empresas[$empresa]
    ) {
        abort(404);
    }

    $receber->load([
        'cliente' => fn ($q) =>
            $q->where('empresa', $this->empresas[$empresa]),
    ]);

    return view('omie.receber.show', compact('receber', 'empresa'));
}
public function create(Request $request, string $empresa)
{
    if (! isset($this->empresas[$empresa])) {
        abort(404);
    }

    $empresaId = $this->empresas[$empresa];

    $clientes = \App\Models\OmieCliente::where('empresa', $empresaId)
        ->orderBy('razao_social')
        ->get();

    $categorias = \App\Models\OmieCategoria::where('empresa', $empresaId)
        ->orderBy('descricao')
        ->get();

    $contas = \App\Models\OmieContaCorrente::where('empresa_codigo', $empresaId)
        ->where('inativo', 'N')
        ->orderBy('descricao')
        ->get();

    return view('omie.receber.create', compact(
        'empresa',
        'clientes',
        'categorias',
        'contas'
    ));
}


public function store(Request $request, string $empresa)
{
    if (! isset($this->empresas[$empresa])) {
        abort(404);
    }

    $empresaId = $this->empresas[$empresa];

    $request->validate([
        'codigo_cliente_fornecedor' => 'required',
        'codigo_categoria'          => 'required',
        'id_conta_corrente'         => 'required|exists:omie_contas_correntes,id',
        'valor_documento'           => 'required|numeric',
        'data_vencimento'           => 'required|date',
    ]);

    OmieReceber::create([
        'empresa'                   => $empresaId,
        'codigo_cliente_fornecedor' => $request->codigo_cliente_fornecedor,
        'codigo_categoria'          => $request->codigo_categoria,
        'id_conta_corrente'         => $request->id_conta_corrente,
        'valor_documento'           => $request->valor_documento,
        'data_vencimento'           => $request->data_vencimento,
        'status'                    => 'pendente',
    ]);

    return redirect()
        ->route('omie.receber.index', $empresa)
        ->with('success', 'Conta a receber cadastrada com sucesso.');
}

public function edit(string $empresa, OmieReceber $receber)
{
    $empresaId = $this->empresas[$empresa];

    if ($receber->empresa !== $empresaId) {
        abort(403);
    }

    $clientes = \App\Models\OmieCliente::where('empresa', $empresaId)->get();
    $categorias = \App\Models\OmieCategoria::where('empresa', $empresaId)->get();

    return view('omie.receber.edit', compact(
        'receber',
        'empresa',
        'clientes',
        'categorias'
    ));
}
public function update(Request $request, string $empresa, OmieReceber $receber)
{
    $empresaId = $this->empresas[$empresa];

    if ($receber->empresa !== $empresaId) {
        abort(403);
    }

    if (in_array($receber->status, ['recebido', 'cancelado'])) {
        return back()->with('error', 'Este registro não pode mais ser alterado.');
    }

    $request->validate([
        'codigo_categoria' => 'required',
        'valor_documento'  => 'required|numeric',
        'data_vencimento'  => 'required|date',
    ]);

    $receber->update($request->only([
        'codigo_categoria',
        'valor_documento',
        'data_vencimento',
    ]));

    return back()->with('success', 'Conta a receber atualizada.');
}
public function updateStatus(Request $request, string $empresa, OmieReceber $receber)
{
    $empresaId = $this->empresas[$empresa];

    if ($receber->empresa !== $empresaId) {
        abort(403);
    }

    if (! $receber->podeEditarStatus()) {
        return back()->with('error', 'Este status não pode mais ser alterado.');
    }

    $request->validate([
        'status' => ['required', 'in:' . implode(',', array_keys(OmieReceber::STATUS))]
    ]);

    $receber->update([
        'status' => $request->status,
    ]);

    return back()->with('success', 'Status do recebimento atualizado com sucesso.');
}


}
