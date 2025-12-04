<?php

namespace App\Http\Controllers;

use App\Models\CostBase;
use Illuminate\Http\Request;
use Carbon\Carbon;


class FinancialDashboardController extends Controller
{
   public function index(Request $request)
{
    $month = $request->get('month', 'all');
    $months = ['jan','fev','mar','abr','mai','jun','jul','ago','set','out','nov','dez'];

    // Mapeamento numérico para identificar o mês atual
    $monthMapNum = [
        1 => 'Pago jan', 2 => 'Pago fev', 3 => 'Pago mar', 4 => 'Pago abr',
        5 => 'Pago mai', 6 => 'Pago jun', 7 => 'Pago jul', 8 => 'Pago ago',
        9 => 'Pago set', 10 => 'Pago out', 11 => 'Pago nov', 12 => 'Pago dez',
    ];

    // Identifica a coluna do mês atual (ex: Pago dez)
    $currentMonthCol = $monthMapNum[Carbon::now()->month] ?? 'Pago dez';

    $costsRaw = CostBase::where('Categoria', '!=', 'Total Geral')->get();

    // 1. Calcular Totais, Mês Atual e Média
   $costs = $costsRaw->map(function($c) use ($months, $currentMonthCol) {

    $total = 0;
    $valuesPaid = [];

    foreach ($months as $m) {
        $val = (float) $c->{"Pago $m"};
        $total += $val;

        if ($val > 0) {
            $valuesPaid[] = $val;
        }
    }

    // média REAL dos 12 meses
    $c->Media = $total / 12;

    // média somente dos meses pagos
    $c->MediaPagos = count($valuesPaid) > 0
        ? array_sum($valuesPaid) / count($valuesPaid)
        : 0;

    // total acumulado
    $c->TotalPago = $total;

    // mês atual
    $c->ValorAtual = (float) $c->{$currentMonthCol};

    return $c;
});
$empresas = CostBase::whereNotNull('cnpj')
    ->where('Categoria', '!=', 'Total Geral')
    ->select('cnpj')
    ->distinct()
    ->pluck('cnpj');




    // 2. Calcular Percentual em relação ao Total Geral do Ano
    $totalAnoGeral = $costs->sum('TotalPago');

    $costs = $costs->map(function($c) use ($totalAnoGeral) {
        $c->Percentual = $totalAnoGeral > 0
            ? round(($c->TotalPago / $totalAnoGeral) * 100, 2)
            : 0;
        return $c;
    });

    // Ordenar: Maior custo anual primeiro
    $costs = $costs->sortByDesc('TotalPago')->values();

    // Totais do rodapé (opcional se for usar charts depois)
    $totalsPorMes = [];
    foreach ($months as $m) {
        $totalsPorMes[$m] = $costsRaw->sum("Pago $m");
    }

    $totals = [
        'por_mes' => $totalsPorMes,
        'total_ano' => $totalAnoGeral,
    ];

    if ($request->ajax()) {
        return response()->json(['costs' => $costs, 'totals' => $totals]);
    }

return view('dashboards.financeiro', compact('costs', 'totals', 'months', 'empresas'));
}
public function show($id)
{
    $cost = CostBase::findOrFail($id);

    $months = [
        'Jan' => (float) $cost->{'Pago jan'},
        'Fev' => (float) $cost->{'Pago fev'},
        'Mar' => (float) $cost->{'Pago mar'},
        'Abr' => (float) $cost->{'Pago abr'},
        'Mai' => (float) $cost->{'Pago mai'},
        'Jun' => (float) $cost->{'Pago jun'},
        'Jul' => (float) $cost->{'Pago jul'},
        'Ago' => (float) $cost->{'Pago ago'},
        'Set' => (float) $cost->{'Pago set'},
        'Out' => (float) $cost->{'Pago out'},
        'Nov' => (float) $cost->{'Pago nov'},
        'Dez' => (float) $cost->{'Pago dez'},
    ];

    $monthsFiltered = collect($months)->filter(fn($v) => $v > 0);

    $average = array_sum($months) / 12;

    $currentMonthNum = Carbon::now()->month;
    $monthKeys = array_keys($months);

    $currentMonthKey = $monthKeys[$currentMonthNum - 1];
    $prevMonthKey = $currentMonthNum > 1 ? $monthKeys[$currentMonthNum - 2] : null;

    $currentValue = (float) $months[$currentMonthKey];
    $prevValue = $prevMonthKey ? (float) $months[$prevMonthKey] : null;

    if ($prevValue !== null && $prevValue > 0) {
        $percentChange = round((($currentValue - $prevValue) / $prevValue) * 100, 2);
    } else {
        $percentChange = 0;
    }

    // Ajuste REAL — SEM zerar quando prevValue = 0
    $realAdjustment = ($prevValue !== null)
        ? ($currentValue - $prevValue)
        : 0;

    return response()->json([
        'id' => $cost->id,
        'categoria' => $cost->Categoria,
        'ajustes' => $realAdjustment,
        'months' => $monthsFiltered->toArray(),
        'average' => $average,
        'currentMonth' => $currentMonthKey,
        'prevMonth' => $prevMonthKey,
        'percentChange' => $percentChange,
    ]);
    $monthsPaid = array_filter($months, fn($v) => $v > 0);

$averagePaid = count($monthsPaid) > 0
    ? array_sum($monthsPaid) / count($monthsPaid)
    : 0;

}
}
