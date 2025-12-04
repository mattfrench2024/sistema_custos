<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CostEntry;
use App\Models\CostBase;
use Carbon\Carbon;

class CostEntryController extends Controller
{
    /**
     * Formulário de cadastro
     */
    public function create()
    {
        // nome correto da coluna: Categoria
        $categories = CostBase::orderBy('Categoria')->get();

        return view('financeiro.costs.create', compact('categories'));
    }

    /**
     * Armazena um novo lançamento
     */
    public function store(Request $request)
    {
        $request->validate([
            'cost_base_id' => ['required', 'exists:costs_base,id'],
            'month'        => ['required', 'date_format:Y-m'],
            'value'        => ['required', 'numeric', 'min:0'],
            'status_pago'  => ['required', 'boolean'],
            'description'  => ['nullable', 'string', 'max:255'],
            'vencimento'   => ['required', 'date'],   // ← novo campo validado
        ]);

        // Mapeamento dos meses → colunas do banco
        $monthColumnMap = [
            '01' => 'Pago jan',
            '02' => 'Pago fev',
            '03' => 'Pago mar',
            '04' => 'Pago abr',
            '05' => 'Pago mai',
            '06' => 'Pago jun',
            '07' => 'Pago jul',
            '08' => 'Pago ago',
            '09' => 'Pago set',
            '10' => 'Pago out',
            '11' => 'Pago nov',
            '12' => 'Pago dez',
        ];

        // Convertendo mês
        $month = Carbon::createFromFormat('Y-m', $request->month);
        $monthNumber = $month->format('m');

        $columnToUpdate = $monthColumnMap[$monthNumber];

        /**
         * 1) SALVA NA cost_entries
         */
        CostEntry::create([
            'cost_base_id' => $request->cost_base_id,
            'value'        => $request->value,
            'description'  => $request->description,
            'status_pago'  => $request->status_pago,
            'date'         => $month->format('Y-m-d'),
            'vencimento'   => $request->vencimento,  // ← novo campo salvo
        ]);

        /**
         * 2) ATUALIZA A TABELA cost_base NO MÊS CORRETO
         */
        CostBase::where('id', $request->cost_base_id)
            ->update([
                $columnToUpdate => $request->value,
                'vencimento'    => $request->vencimento,  // ← mantém sincronizado
            ]);

        return redirect()
            ->route('dashboard.financeiro')
            ->with('success', 'Custo registrado com sucesso!');
    }
}
