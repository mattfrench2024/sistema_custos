<?php

namespace App\Http\Controllers;

use App\Models\OmiePagar;
use App\Models\OmieReceber;
use App\Models\OmieContaCorrente;
use App\Models\OmieMovimentoFinanceiro;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OmieMovimentoFinanceiroController extends Controller
{
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
    // 🔒 Sanidade de datas (evita 2045+)
    ->whereNotNull('data_movimento')
    ->whereDate('data_movimento', '<=', now()->addDays(5))

    // 💰 Apenas movimentos financeiros reais
    ->where('valor', '!=', 0)

    // 📊 Ordenação profissional
    ->orderByDesc('data_movimento')
    ->orderByDesc('data_inclusao')
    ->orderByDesc('id')

    // 📄 Paginação estável
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
public function create(Request $request, string $empresa)
{
    abort_unless(isset($this->empresas[$empresa]), 404);

    $origem   = $request->query('origem');
    $tituloId = $request->query('id');

    if (! $origem || ! $tituloId) {
        abort(400, 'Origem ou título não informado.');
    }

    abort_unless(in_array($origem, ['pagar', 'receber']), 400);

    $titulo = $origem === 'pagar'
        ? OmiePagar::where('empresa', $this->empresas[$empresa])
            ->findOrFail($tituloId)
        : OmieReceber::where('empresa', $this->empresas[$empresa])
            ->findOrFail($tituloId);

    $contas = OmieContaCorrente::ativas()
        ->empresa($this->empresas[$empresa])
        ->get();

    return view('omie.movimentos-financeiros.create', compact(
        'empresa',
        'origem',
        'titulo',
        'contas'
    ));
}

public function store(Request $request, $empresa)
{
    abort_unless(isset($this->empresas[$empresa]), 404);

    $request->validate([
    'codigo_titulo'           => 'required',
    'codigo_conta_corrente'   => 'required',
    'valor'                   => 'required|numeric',
    'data_movimento'          => 'required|date',
    'origem'                  => 'required|in:pagar,receber',
]);


    $empresaId = $this->empresas[$empresa];
    $isPagar   = $request->origem === 'pagar';

    // 🔎 Busca título correto
    $titulo = $isPagar
        ? OmiePagar::where('empresa', $empresaId)
            ->where('codigo_lancamento_omie', $request->codigo_lancamento_omie)
            ->first()
        : OmieReceber::where('empresa', $empresaId)
            ->where('codigo_lancamento_integracao', $request->codigo_lancamento_omie)
            ->first();

    if (! $titulo) {
        return back()->withErrors([
            'titulo' => 'Título financeiro não encontrado.'
        ]);
    }

    // 🎯 Código REAL do título
    $codigoTitulo = $isPagar
        ? $titulo->codigo_lancamento_omie
        : $titulo->codigo_lancamento_integracao;

    // 📦 Payload correto
    $info = [
        'manual' => true,
        'usuario' => auth()->id(),
        'detalhes' => [
            'cGrupo'      => $isPagar ? 'CONTA_A_PAGAR' : 'CONTA_A_RECEBER',
            'nCodTitulo'  => $codigoTitulo,
            'nCodCC'      => $request->codigo_conta_corrente,
            'dDtMov'      => Carbon::parse($request->data_movimento)->format('d/m/Y'),
            'nValorMov'   => abs($request->valor),
            'nCodCliente' => $titulo->codigo_cliente_fornecedor ?? null,
        ],
    ];

    // 💾 Salva movimento
    OmieMovimentoFinanceiro::create([
        'empresa'               => $empresaId,
        'codigo_movimento'      => (string) \Str::uuid(),
        'codigo_lancamento_omie'=> $codigoTitulo,
        'codigo_titulo'         => $codigoTitulo,
        'tipo_movimento'        => $isPagar ? 'P' : 'R',
        'origem'                => strtoupper($request->origem),
        'data_movimento'        => $request->data_movimento,
        'data_competencia'      => $titulo->data_vencimento,
        'valor'                 => abs($request->valor),
        'codigo_conta_corrente' => $request->codigo_conta_corrente,
        'info'                  => $info,
    ]);

    return redirect()
        ->route('omie.movimentos.index', $empresa)
        ->with('success', 'Movimento financeiro registrado com sucesso.');
}
}
