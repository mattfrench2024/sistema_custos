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
        'cs' => ['codigo' => '10', 'nome' => 'Consultoria Soluções'],
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


   
    // 1. Iniciamos uma nova query para qualificar as colunas explicitamente
    $concentracaoClientes = OmieMovimentoFinanceiro::query()
        ->where('omie_movimentos_financeiros.empresa', $empresaCodigo) // <--- Correção da ambiguidade
        ->whereYear('omie_movimentos_financeiros.data_movimento', $ano);

    if ($mes) {
        $concentracaoClientes->whereMonth('omie_movimentos_financeiros.data_movimento', $mes);
    }

    $concentracaoClientes = OmieMovimentoFinanceiro::query()
    ->from('omie_movimentos_financeiros as mf')

    // 🔹 filtro por empresa (map: 04, 30, 36, 10)
    ->where('mf.empresa', $empresaCodigo)

    // 🔹 filtro por ano
    ->whereYear('mf.data_movimento', $ano);

if ($mes) {
    $concentracaoClientes->whereMonth('mf.data_movimento', $mes);
}

$concentracaoClientes = $concentracaoClientes

    // 🔹 somente CONTA_A_RECEBER
    ->whereRaw("
        JSON_CONTAINS(
            mf.info->'$.detalhes.cGrupo',
            '\"CONTA_A_RECEBER\"'
        )
    ")

    // 🔹 somente valores positivos
    ->where('mf.valor', '>', 0)

    // 🔹 join com clientes (igual ao SQL)
    ->join('omie_clientes as c', function ($join) {
        $join->on(
            'c.codigo_cliente_omie',
            '=',
            \DB::raw("
                CAST(
                    JSON_UNQUOTE(
                        JSON_EXTRACT(mf.info, '$.detalhes.nCodCliente')
                    ) AS UNSIGNED
                )
            ")
        )
->whereRaw("
    c.empresa COLLATE utf8mb4_unicode_ci =
    mf.empresa COLLATE utf8mb4_unicode_ci
");
    })

    // 🔹 select final
    ->selectRaw("
        c.razao_social as cliente,
        SUM(mf.valor) as total
    ")

    // 🔹 agrupamento correto
    ->groupBy('c.codigo_cliente_omie', 'c.razao_social')

    ->orderByDesc('total')
    ->limit(5)

    ->get()
    ->map(fn ($c) => [
        'cliente' => mb_strimwidth($c->cliente, 0, 35, '…'),
        'total'   => (float) $c->total,
    ])
    ->values();




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