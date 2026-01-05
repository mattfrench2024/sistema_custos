<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinanceiroAnalitico;

class FinanceiroAnaliticoController extends Controller
{
    public function dashboard(Request $request)
    {
        $ano     = (int) $request->input('ano', date('Y'));
        $mes     = $request->input('mes');
        $empresa = $request->input('empresa');

        return view('financeiro.analitico.dashboard', [
    'ano'     => $ano,
    'mes'     => $mes,
    'empresa' => $empresa,

    // KPIs estratégicos
    'kpis' => FinanceiroAnalitico::kpis($ano, $mes, $empresa),

    // Evolução + volatilidade
    'mensal' => FinanceiroAnalitico::dashboardMensal($ano, $empresa),

    // Top análises (ADICIONAR)
'topRecebimentos' => FinanceiroAnalitico::topRecebimentos($ano, $mes),
'topPagamentos'   => FinanceiroAnalitico::topPagamentos($ano, $mes),


    // Concentração de receita
    'concentracaoReceita' => FinanceiroAnalitico::concentracaoReceita($ano, $mes),

    // % meses negativos (ADICIONAR)
    'percentualMesesNegativos' => collect(
        FinanceiroAnalitico::dashboardMensal($ano, $empresa)
    )->where('negativo', true)->count() / 12 * 100,

    // Alertas automáticos
    'alertas' => FinanceiroAnalitico::alertas($ano, $mes),

    // Score financeiro do mês
    'scoreMes' => $mes
        ? FinanceiroAnalitico::scoreMes($ano, $mes)
        : null,
]);

    }
    public function empresa(string $empresa, Request $request)
{
    $ano = (int) $request->input('ano', date('Y'));
    $mes = $request->input('mes');

    return view('financeiro.analitico.empresa', [
        'empresaCodigo' => $empresa,
        'empresaNome'   => FinanceiroAnalitico::empresasMap()[$empresa] ?? $empresa,
        'ano' => $ano,
        'mes' => $mes,

        'kpis' => FinanceiroAnalitico::kpis($ano, $mes, $empresa),
        'mensal' => FinanceiroAnalitico::dashboardMensal($ano, $empresa),

        'topClientes' => FinanceiroAnalitico::topClientes($ano, $mes, $empresa),
        'topFornecedores' => FinanceiroAnalitico::topFornecedores($ano, $mes, $empresa),

        'concentracaoClientes' =>
            FinanceiroAnalitico::concentracaoClientes($ano, $mes, $empresa),
    ]);
}


}
