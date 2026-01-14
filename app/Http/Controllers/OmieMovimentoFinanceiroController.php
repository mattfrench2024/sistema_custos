<?php

namespace App\Http\Controllers;

use App\Models\OmieMovimentoFinanceiro;
use Illuminate\Http\Request;

class OmieMovimentoFinanceiroController extends Controller
{
    protected $empresaNomes = [
        'vs' => 'Verreschi Soluções',
        'gv' => 'Grupo Verreschi',
        'sv' => 'Sociedade Advogados Verreschi',
    ];

    protected $empresas = [
        'vs' => '30',
        'gv' => '36',
        'sv' => '04',
    ];

    /**
     * Lista geral de movimentos financeiros
     */
    public function index(Request $request, $empresa)
{
    if (! isset($this->empresas[$empresa])) {
        abort(404);
    }

    $empresaId   = $this->empresas[$empresa];
    $empresaNome = $this->empresaNomes[$empresa];

    $query = OmieMovimentoFinanceiro::where('empresa', $empresaId);

    /*
    |--------------------------------------------------------------------------
    | 📅 Filtro por datas
    |--------------------------------------------------------------------------
    */
    if ($request->filled('data_de')) {
        $query->whereDate('data_movimento', '>=', $request->data_de);
    }

    if ($request->filled('data_ate')) {
        $query->whereDate('data_movimento', '<=', $request->data_ate);
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 Origem do movimento (cGrupo - Omie)
    |--------------------------------------------------------------------------
    */
   if ($request->filled('grupo')) {
    match ($request->grupo) {
        'receber' => $query->where('info->detalhes->cGrupo', 'CONTA_A_RECEBER'),

        'pagar' => $query->where('info->detalhes->cGrupo', 'CONTA_A_PAGAR'),

        'cc' => $query->whereIn(
            'info->detalhes->cGrupo',
            ['CONTA_CORRENTE_REC', 'CONTA_CORRENTE_PAG']
        ),

        default => null,
    };
}


    /*
    |--------------------------------------------------------------------------
    | ⚙️ Tipo técnico (R / P / C / D)
    |--------------------------------------------------------------------------
    */
    if ($request->filled('tipo') && in_array($request->tipo, ['R','P','C','D'])) {
        $query->where('tipo_movimento', $request->tipo);
    }

    /*
    |--------------------------------------------------------------------------
    | 📄 Paginação
    |--------------------------------------------------------------------------
    */
    $movimentos = $query
        ->orderByDesc('data_movimento')
        ->paginate(50)
        ->withQueryString();

    return view('omie.movimentos-financeiros.index', compact(
        'movimentos',
        'empresa',
        'empresaNome'
    ));
}




    /**
     * Detalhe de um movimento financeiro
     */
   public function show($empresa, OmieMovimentoFinanceiro $movimento)
{
    if (
        ! isset($this->empresas[$empresa]) ||
        $movimento->empresa !== $this->empresas[$empresa]
    ) {
        abort(404);
    }

    return view('omie.movimentos-financeiros.show', compact(
        'movimento',
        'empresa'
    ));
}

}
