<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        return Expense::with(['category', 'user'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'data' => 'required|date',
            'categoria_id' => 'required|exists:categories,id',
        ]);

        $data['user_id'] = auth()->id();

        return Expense::create($data);
    }

    public function show(Expense $expense)
    {
        return $expense->load(['category', 'user']);
    }

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'data' => 'required|date',
            'categoria_id' => 'required|exists:categories,id'
        ]);

        $expense->update($data);

        return $expense;
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return response()->json(['message' => 'Despesa removida']);
    }
}
