<?php

namespace App\Http\Controllers;

use App\Models\OmieCategoria;
use Illuminate\Http\Request;

class OmieCategoriaController extends Controller
{
    protected array $empresas = [
        'sv' => ['codigo' => '04', 'label' => 'S. Verreschi Advogados'],
        'vs' => ['codigo' => '30', 'label' => 'Verreschi Soluções'],
        'gv' => ['codigo' => '36', 'label' => 'Grupo Verreschi'],
    ];

    public function index(Request $request, string $empresa)
{
    if (! isset($this->empresas[$empresa])) {
        abort(404);
    }

    $empresaCfg = $this->empresas[$empresa];

    /** =========================
     * QUERY BASE (SOMENTE ESTRUTURAL)
     ========================= */
    $query = OmieCategoria::where('empresa', $empresaCfg['codigo']);

    // Tipo
    if ($request->filled('tipo')) {
        match ($request->tipo) {
            'receita'        => $query->where('conta_receita', true),
            'despesa'        => $query->where('conta_despesa', true),
            'transferencia' => $query->where('transferencia', true),
            default          => null,
        };
    }

    // Status
    if ($request->filled('status')) {
        $query->where(
            'conta_inativa',
            $request->status === 'inativa'
        );
    }

    // Busca textual
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('codigo', 'like', "%{$search}%")
              ->orWhere('descricao', 'like', "%{$search}%")
              ->orWhere('natureza', 'like', "%{$search}%");
        });
    }

    /** =========================
     * EXECUTA QUERY
     ========================= */
    $categorias = $query
        ->orderBy('codigo')
        ->get(); // 👈 NÃO paginar ainda

    /** =========================
     * FILTRO DE MOVIMENTAÇÃO (NEGÓCIO)
     ========================= */
    if ($request->filled('movimentacao')) {
        $categorias = $categorias->filter(function ($categoria) use ($request) {
            return $request->movimentacao === 'com'
                ? $categoria->possuiMovimentacao()
                : ! $categoria->possuiMovimentacao();
        });
    }

    /** =========================
     * PAGINAÇÃO MANUAL
     ========================= */
    $page     = request()->get('page', 1);
    $perPage  = 25;

    $categorias = new \Illuminate\Pagination\LengthAwarePaginator(
        $categorias->forPage($page, $perPage),
        $categorias->count(),
        $perPage,
        $page,
        ['path' => request()->url(), 'query' => request()->query()]
    );

    return view('omie.categorias.index', [
        'categorias'   => $categorias,
        'empresa'      => $empresa,
        'empresaLabel' => $empresaCfg['label'],
    ]);
}



    public function show(string $empresa, string $codigo)
{
    if (! isset($this->empresas[$empresa])) {
        abort(404);
    }

    $empresaCfg = $this->empresas[$empresa];

    $categoria = OmieCategoria::with([
            'superior',
            'filhas',
        ])
        ->where('empresa', $empresaCfg['codigo'])
        ->where('codigo', $codigo)
        ->firstOrFail();

    // Métricas financeiras agregadas
    $financeiro = [
        'total_receitas' => $categoria->totalReceitas(),
        'total_despesas' => $categoria->totalDespesas(),
        'saldo'          => $categoria->saldoFinanceiro(),
        'movimentada'    => $categoria->possuiMovimentacao(),
    ];

    return view('omie.categorias.show', [
        'categoria'    => $categoria,
        'financeiro'   => $financeiro,
        'empresa'      => $empresa,
        'empresaLabel' => $empresaCfg['label'],
    ]);
}

}
