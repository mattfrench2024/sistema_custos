<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class DashboardController extends Controller
{
    public function default()
    {
        return $this->buildDashboard('default');
    }

    public function admin()
    {
        return $this->buildDashboard('admin');
    }

    public function financeiro()
    {
        return $this->buildDashboard('financeiro');
    }

    public function auditoria()
    {
        return $this->buildDashboard('auditoria');
    }
    public function rh()
{
    return $this->buildDashboard('rh');
}


  private function buildDashboard($view)
{
    $products = Product::orderBy('valor', 'desc')->take(10)->get();

    $totals = [
        'produtos_count'   => Product::count(),
        'custos_totais'    => Product::sum('valor'),
        'categorias_count' => Category::count(),
        'usuarios_count'   => \App\Models\User::count(),
        'invoices_count'   => 0
    ];

    $chartLabels = Product::pluck('nome')->take(10);
    $chartValues = Product::pluck('valor')->take(10);

    // Adicione isso:
    $audit_logs = []; // array vazio por padrão

    return view("dashboards.$view", compact('products', 'totals', 'chartLabels', 'chartValues', 'audit_logs'));
}

}
