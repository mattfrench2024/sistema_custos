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

        // 👉 agora SEM "Pago "
        $monthMapNum = [
            1 => 'pago_jan', 2 => 'pago_fev', 3 => 'pago_mar', 4 => 'pago_abr',
            5 => 'pago_mai', 6 => 'pago_jun', 7 => 'pago_jul', 8 => 'pago_ago',
            9 => 'pago_set', 10 => 'pago_out', 11 => 'pago_nov', 12 => 'pago_dez',
        ];

        $currentMonthCol = $monthMapNum[Carbon::now()->month] ?? 'pago_dez';

        $costsRaw = CostBase::where('Categoria', '!=', 'Total Geral')->get();

        // ====== Processamento ======
        $costs = $costsRaw->map(function($c) use ($months, $currentMonthCol) {

            $total = 0;
            $valuesPaid = [];

            foreach ($months as $m) {
                $field = "pago_$m";
                $val = (float) ($c->{$field} ?? 0);

                $total += $val;

                if ($val > 0) {
                    $valuesPaid[] = $val;
                }
            }

            $c->Media = round($total / 12, 2);
            $c->MediaPagos = count($valuesPaid)
                ? round(array_sum($valuesPaid) / count($valuesPaid), 2)
                : 0;

            $c->TotalPago = $total;
            $c->ValorAtual = (float) ($c->{$currentMonthCol} ?? 0);

            return $c;
        });

        // Empresas
        $empresas = CostBase::whereNotNull('cnpj')
            ->where('Categoria', '!=', 'Total Geral')
            ->select('cnpj')
            ->distinct()
            ->pluck('cnpj');

        $totalAnoGeral = $costs->sum('TotalPago');

        $costs = $costs->map(function($c) use ($totalAnoGeral) {
            $c->Percentual = $totalAnoGeral > 0
                ? round(($c->TotalPago / $totalAnoGeral) * 100, 2)
                : 0;
            return $c;
        })->sortByDesc('TotalPago')->values();

        // Totais por mês
        $totalsPorMes = [];
        foreach ($months as $m) {
            $totalsPorMes[$m] = $costsRaw->sum("pago_$m");
        }

        $totals = [
            'por_mes' => $totalsPorMes,
            'total_ano' => $totalAnoGeral,
        ];

        if ($request->ajax()) {
            return response()->json(['costs' => $costs, 'totals' => $totals]);
        }

        $route = $request->route()->getName();

        return match ($route) {
            'financeiro.pagar.index'   => view('financeiro.pagar.index', compact('costs','totals','months','empresas')),
            'financeiro.receber.index' => view('financeiro.receber.index', compact('costs','totals','months','empresas')),
            'dashboard.financeiro'     => view('dashboards.financeiro', compact('costs','totals','months','empresas')),
            default => abort(404),
        };
    }

    public function show($id)
    {
        $cost = CostBase::findOrFail($id);

        $months = [
            'Jan' => (float) $cost->pago_jan,
            'Fev' => (float) $cost->pago_fev,
            'Mar' => (float) $cost->pago_mar,
            'Abr' => (float) $cost->pago_abr,
            'Mai' => (float) $cost->pago_mai,
            'Jun' => (float) $cost->pago_jun,
            'Jul' => (float) $cost->pago_jul,
            'Ago' => (float) $cost->pago_ago,
            'Set' => (float) $cost->pago_set,
            'Out' => (float) $cost->pago_out,
            'Nov' => (float) $cost->pago_nov,
            'Dez' => (float) $cost->pago_dez,
        ];

        $monthsFiltered = collect($months)->filter(fn($v) => $v > 0);
        $average = array_sum($months) / 12;

        $currentMonthNum = Carbon::now()->month;
        $monthKeys = array_keys($months);

        $currentMonthKey = $monthKeys[$currentMonthNum - 1];
        $prevMonthKey = $currentMonthNum > 1 ? $monthKeys[$currentMonthNum - 2] : null;

        $currentValue = (float) $months[$currentMonthKey];
        $prevValue = $prevMonthKey ? (float) $months[$prevMonthKey] : null;

        $percentChange = ($prevValue && $prevValue > 0)
            ? round((($currentValue - $prevValue) / $prevValue) * 100, 2)
            : 0;

        $realAdjustment = ($prevValue !== null)
            ? ($currentValue - $prevValue)
            : 0;

        return response()->json([
            'id' => $cost->id,
            'categoria' => $cost->Categoria,
            'ajustes' => $realAdjustment,
            'months' => $monthsFiltered->toArray(),
            'average' => round($average, 2),
            'currentMonth' => $currentMonthKey,
            'prevMonth' => $prevMonthKey,
            'percentChange' => $percentChange,
        ]);
    }
}
