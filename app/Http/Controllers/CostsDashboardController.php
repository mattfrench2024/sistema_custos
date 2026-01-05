<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CostsDashboardController extends Controller
{
    public function index()
    {
        // Total de categorias
        $totalCategorias = DB::table('categories')->count();

        // Total anual (soma de todos os produtos)
        $totalAnual = DB::table('products')->sum('valor');

        // Média mensal (total / 12)
        $mediaMensal = $totalAnual / 12;

        // Meses (labels)
        $meses = [
            'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun',
            'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'
        ];

        // Valores mensais mock (substituir quando quiser)
        $mensalValores = DB::table('products')
            ->select(DB::raw('MONTH(created_at) as mes'), DB::raw('SUM(valor) as total'))
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes')
            ->toArray();

        // Garante 12 valores
        $mensalValores = array_replace(array_fill(1, 12, 0), $mensalValores);
        $mensalValores = array_values($mensalValores);

        // Mês com maior gasto
        $mesMaior = $meses[array_search(max($mensalValores), $mensalValores)];

        // Top 10 categorias
       $topCategorias = DB::table('costs_base')
    ->select('Categoria', DB::raw('
        ( `Pago jan` + `Pago fev` + `Pago mar` + `Pago abr` + `Pago mai` + `Pago jun` + 
          `Pago jul` + `Pago ago` + `Pago set` + `Pago out` + `Pago nov` + `Pago dez` + `AJUSTES` ) as total
    '))
    ->orderBy('total', 'desc')
    ->limit(10)
    ->get();


        // Donut categorias
        $donutLabels = $topCategorias->pluck('Categoria');
        $donutValores = $topCategorias->pluck('total');

        // Heatmap — por simplicidade, números aleatórios por agora
        $heatmap = [];
        foreach ($meses as $i => $m) {
            $heatmap[] = [
                'x' => $m,
                'y' => rand(5, 20)
            ];
        }

        // Últimos registros
        $ultimos = DB::table('products')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('dashboards.auditoria', [
            'totalCategorias' => $totalCategorias,
            'totalAnual'      => $totalAnual,
            'mediaMensal'     => $mediaMensal,
            'mesMaior'        => $mesMaior,
            'meses'           => $meses,
            'mensalValores'   => $mensalValores,
            'topCategorias'   => $topCategorias,
            'donutLabels'     => $donutLabels,
            'donutValores'    => $donutValores,
            'heatmap'         => $heatmap,
            'ultimos'         => $ultimos,
        ]);
    }
}
