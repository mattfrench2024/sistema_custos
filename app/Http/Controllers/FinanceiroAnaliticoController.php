<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinanceiroAnalitico;
use App\Models\OmieMovimentoFinanceiro;

class FinanceiroAnaliticoController extends Controller
{
    public function dashboard(Request $request)
{
    $ano = (int) $request->input('ano', date('Y'));
    $mes = $request->input('mes');

    return view('financeiro.analitico.dashboard', [
        'ano' => $ano,
        'mes' => $mes,

        'kpis' => FinanceiroAnalitico::kpis($ano, $mes),

        'mensal' => FinanceiroAnalitico::dashboardMensal($ano),

        'topRecebimentos' =>
            FinanceiroAnalitico::topRecebimentos($ano, $mes),

        'topPagamentos' =>
            FinanceiroAnalitico::topPagamentos($ano, $mes),

        'concentracaoReceita' =>
            FinanceiroAnalitico::concentracaoReceita($ano, $mes),
    ]);
}

    public function empresa(Request $request, string $empresa)
{
    // 🔹 Map interno (igual ao consolidado)
    $empresaMap = [
        'vs' => ['codigo' => '30', 'nome' => 'Verreschi Soluções'],
        'gv' => ['codigo' => '36', 'nome' => 'Grupo Verreschi'],
        'sv' => ['codigo' => '04', 'nome' => 'Sociedade Advogados Verreschi'],
    ];

    if (!isset($empresaMap[$empresa])) {
        abort(404);
    }

    $empresaCodigo = $empresaMap[$empresa]['codigo'];
    $empresaNome   = $empresaMap[$empresa]['nome'];

    $ano = (int) $request->input('ano', date('Y'));
    $mes = $request->input('mes');

    /*
    |--------------------------------------------------------------------------
    | 🔹 BASE QUERY — movimentos da empresa
    |--------------------------------------------------------------------------
    */
    $baseQuery = OmieMovimentoFinanceiro::empresa($empresaCodigo)
        ->whereYear('data_movimento', $ano);

    if ($mes) {
        $baseQuery->whereMonth('data_movimento', $mes);
    }

    /*
    |--------------------------------------------------------------------------
    | 📊 KPIs EXECUTIVOS
    |--------------------------------------------------------------------------
    */
    $receita = (clone $baseQuery)
        ->contaAReceberGerencial()
        ->sum('valor');

    $custos = (clone $baseQuery)
        ->contaAPagarGerencial()
        ->sum('valor');

    $saldo = $receita - $custos;

    $margem = $receita > 0
        ? round(($saldo / $receita) * 100, 2)
        : 0;

    $kpis = compact('receita', 'custos', 'saldo', 'margem');

    /*
    |--------------------------------------------------------------------------
    | 📈 EVOLUÇÃO MENSAL (para gráfico)
    |--------------------------------------------------------------------------
    */
    $mensal = OmieMovimentoFinanceiro::empresa($empresaCodigo)
        ->whereYear('data_movimento', $ano)
        ->selectRaw("
            MONTH(data_movimento) as mes_num,
            SUM(CASE WHEN info->'$.detalhes.cGrupo' = 'CONTA_A_RECEBER' THEN valor ELSE 0 END) as receita,
            SUM(CASE WHEN info->'$.detalhes.cGrupo' = 'CONTA_A_PAGAR' THEN valor ELSE 0 END) as custos
        ")
        ->groupByRaw('MONTH(data_movimento)')
        ->orderBy('mes_num')
        ->get()
        ->map(fn ($m) => [
            'mes'     => \Carbon\Carbon::create()->month($m->mes_num)->locale('pt_BR')->isoFormat('MMM'),
            'receita' => (float) $m->receita,
            'custos'  => (float) $m->custos,
        ]);

    /*
    |--------------------------------------------------------------------------
    | 🧾 TOP CLIENTES (Receita)
    |--------------------------------------------------------------------------
    */
    $topClientes = (clone $baseQuery)
        ->contaAReceberGerencial()
        ->selectRaw("
            info->'$.detalhes.nCodCli' as codigo,
            info->'$.detalhes.cRazaoSocial' as cliente,
            SUM(valor) as total
        ")
        ->groupBy('codigo', 'cliente')
        ->orderByDesc('total')
        ->limit(5)
        ->get()
        ->toArray();

    /*
    |--------------------------------------------------------------------------
    | 🧾 TOP FORNECEDORES (Custos)
    |--------------------------------------------------------------------------
    */
    $topFornecedores = (clone $baseQuery)
        ->contaAPagarGerencial()
        ->selectRaw("
            info->'$.detalhes.nCodCli' as codigo,
            info->'$.detalhes.cRazaoSocial' as fornecedor,
            SUM(valor) as total
        ")
        ->groupBy('codigo', 'fornecedor')
        ->orderByDesc('total')
        ->limit(5)
        ->get()
        ->toArray();

    /*
    |--------------------------------------------------------------------------
    | 🟠 CONCENTRAÇÃO DE RECEITA
    |--------------------------------------------------------------------------
    */
    $totalReceita = array_sum(array_column($topClientes, 'total'));

    $concentracaoClientes = collect($topClientes)->map(fn ($c) => [
        'cliente'     => $c['cliente'],
        'percentual'  => $totalReceita > 0
            ? round(($c['total'] / $totalReceita) * 100, 2)
            : 0,
    ])->values();

    return view('financeiro.analitico.empresa', compact(
        'empresaCodigo',
        'empresaNome',
        'ano',
        'mes',
        'kpis',
        'mensal',
        'topClientes',
        'topFornecedores',
        'concentracaoClientes'
    ));
}





}