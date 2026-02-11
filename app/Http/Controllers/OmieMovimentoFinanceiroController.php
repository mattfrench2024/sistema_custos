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
        'codigo_titulo'         => 'required|numeric', // agora sempre tem valor
        'codigo_conta_corrente' => 'required',
        'valor'                 => 'required|numeric',
        'data_movimento'        => 'required|date',
        'origem'                => 'required|in:pagar,receber',
        'observacao'            => 'nullable|string',
    ]);

    $empresaId = $this->empresas[$empresa];
    $isPagar   = $request->origem === 'pagar';

    // Busca o título usando o código enviado (pode ser o código Omie OU o id local)
    $tituloQuery = $isPagar ? OmiePagar::where('empresa', $empresaId)
                                    : OmieReceber::where('empresa', $empresaId);

    $titulo = $tituloQuery->where(function ($q) use ($request, $isPagar) {
        if ($isPagar) {
            $q->where('codigo_lancamento_omie', $request->codigo_titulo)
              ->orWhere('id', $request->codigo_titulo);
        } else {
            $q->where('codigo_lancamento_integracao', $request->codigo_titulo)
              ->orWhere('id', $request->codigo_titulo);
        }
    })->firstOrFail();

    // Usa o mesmo valor enviado como código de lançamento no movimento
    $codigoLancamentoOmie = $request->codigo_titulo;

    $info = [
        'manual'   => true,
        'usuario'  => auth()->id(),
        'detalhes' => [
            'cGrupo'      => $isPagar ? 'CONTA_A_PAGAR' : 'CONTA_A_RECEBER',
            'nCodTitulo'  => $codigoLancamentoOmie,
            'nCodCC'      => $request->codigo_conta_corrente,
            'dDtMov'      => Carbon::parse($request->data_movimento)->format('d/m/Y'),
            'nValorMov'   => abs($request->valor),
            'nCodCliente' => $titulo->codigo_cliente_fornecedor ?? null,
        ],
    ];

    // Gere os UUIDs antes
$codigoMovimento = (string) \Str::uuid();
$omieUid         = 'manual-' . $codigoMovimento; // ou apenas outro UUID se preferir

OmieMovimentoFinanceiro::create([
    'empresa'                => $empresaId,
    'omie_uid'               => $omieUid,                    // ← ADICIONADO
    'codigo_movimento'       => $codigoMovimento,            // ← use a variável
    'codigo_lancamento_omie' => $codigoLancamentoOmie,
    'codigo_titulo'          => $titulo->id,
    'tipo_movimento'         => $isPagar ? 'P' : 'R',
    'origem'                 => strtoupper($request->origem),
    'data_movimento'         => $request->data_movimento,
    'data_competencia'       => $titulo->data_vencimento,
    'valor'                  => abs($request->valor),
    'codigo_conta_corrente'  => $request->codigo_conta_corrente,
    'info'                   => $info,
    'observacao'             => $request->observacao ?? null,
]);
    // Atualiza status do título automaticamente
    $titulo->refresh();
    if ($titulo->saldo_aberto <= 0) {
        $titulo->update(['status' => 'recebido']);
    } elseif ($titulo->total_recebido > 0) {
        $titulo->update(['status' => 'parcial']);
    }

    return redirect()
        ->route('omie.movimentos.index', $empresa)
        ->with('success', 'Movimento financeiro registrado com sucesso.');
}
}
