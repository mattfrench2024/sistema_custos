<?php

namespace App\Http\Controllers;

use App\Models\CostBase;
use Illuminate\Http\Request;

class CostBaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:financeiro');
    }

    public function index()
    {
        $costs = CostBase::orderBy('Categoria')->get();
        $categories = CostBase::orderByRaw('`Categoria` ASC')->get();


        $totals = [
            'total_ano' => $costs->sum(function ($c) {
                return array_sum($c->getMonthlyValues());
            }),
            'por_mes' => [
                'jan' => $costs->sum('Pago jan'),
                'fev' => $costs->sum('Pago fev'),
                'mar' => $costs->sum('Pago mar'),
                'abr' => $costs->sum('Pago abr'),
                'mai' => $costs->sum('Pago mai'),
                'jun' => $costs->sum('Pago jun'),
                'jul' => $costs->sum('Pago jul'),
                'ago' => $costs->sum('Pago ago'),
                'set' => $costs->sum('Pago set'),
                'out' => $costs->sum('Pago out'),
                'nov' => $costs->sum('Pago nov'),
                'dez' => $costs->sum('Pago dez'),
            ]
        ];

        return view('financeiro.costs.index', compact('costs', 'totals'));
    }

    public function edit(CostBase $cost)
    {
        return view('financeiro.costs.edit', compact('cost'));
    }

    public function update(Request $request, CostBase $cost)
    {
        $cost->update($request->all());

        return redirect()->route('financeiro.costs.index')
            ->with('success', 'Registro atualizado com sucesso!');
    }
}
