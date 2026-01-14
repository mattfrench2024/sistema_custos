<?php

namespace App\Http\Controllers;

use App\Models\OmieMovimentoFinanceiro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OmieFinanceiroConsolidadoController extends Controller
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
     * Dashboard financeiro consolidado (Gerencial / Executivo)
     */
    public function index(Request $request, string $empresa)
    {
        if (! isset($this->empresas[$empresa])) {
            abort(404);
        }

        $empresaId   = $this->empresas[$empresa];
        $empresaNome = $this->empresaNomes[$empresa];

        /*
        |--------------------------------------------------------------------------
        | 🔹 BASE QUERY — Movimentos consolidados (PASSADO)
        |--------------------------------------------------------------------------
        */
        $baseQuery = OmieMovimentoFinanceiro::empresa($empresaId)
            ->periodo($request->data_de, $request->data_ate);

        /*
        |--------------------------------------------------------------------------
        | 📊 KPIs CONSOLIDADOS (realizado)
        |--------------------------------------------------------------------------
        */
        $kpis = [
            'total_registros' => (clone $baseQuery)->count(),

            'pagar' => [
    'valor' => (clone $baseQuery)
        ->contaAPagarGerencial()
        ->sum('valor'),

    'qtd' => (clone $baseQuery)
        ->contaAPagarGerencial()
        ->count(),
],

'receber' => [
    'valor' => (clone $baseQuery)
        ->contaAReceberGerencial()
        ->sum('valor'),

    'qtd' => (clone $baseQuery)
        ->contaAReceberGerencial()
        ->count(),
],

        ];

        $kpis['saldo_realizado'] =
            $kpis['receber']['valor'] - $kpis['pagar']['valor'];

       /*
|--------------------------------------------------------------------------
| 🔮 PROJEÇÕES (FUTURO) — BASEADAS EM TÍTULOS
|--------------------------------------------------------------------------
*/
$hoje = now()->startOfDay();
$projecoes = [];

foreach ([30, 60, 90] as $dias) {
    $ate = (clone $hoje)->addDays($dias);

    // 🔻 A PAGAR — usa status_titulo
    $totalPagar = DB::table('omie_pagar')
    ->where('empresa', $empresaId)
    ->whereNotIn('status_titulo', ['CANCELADO'])
    ->whereDate('data_vencimento', '<=', $ate)
    ->sum('valor_documento');


$totalReceber = DB::table('omie_receber')
    ->where('empresa', $empresaId)
    ->whereNotIn('status', ['cancelado', 'Cancelado'])
    ->whereDate('data_vencimento', '<=', $ate)
    ->sum('valor_documento');


    $projecoes[$dias] = [
        'pagar'   => $totalPagar,
        'receber' => $totalReceber,
        'saldo'   => $totalReceber - $totalPagar,
    ];
}



        /*
        |--------------------------------------------------------------------------
        | 📄 LISTAGEM RESUMIDA (rápida para view)
        |--------------------------------------------------------------------------
        */
        $movimentos = (clone $baseQuery)
            ->select([
                'id',
                'data_movimento',
                'tipo_movimento',
                'valor',
                'codigo_conta_corrente',
                'categorias',
                'info',
            ])
            ->orderByDesc('data_movimento')
            ->paginate(30)
            ->withQueryString();

        return view('omie.financeiro-consolidado.index', compact(
            'empresa',
            'empresaNome',
            'kpis',
            'projecoes',
            'movimentos'
        ));
    }
}
