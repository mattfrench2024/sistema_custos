<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        return Invoice::with(['category', 'user'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'numero' => 'required|string|max:255',
            'fornecedor' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'data' => 'required|date',
            'categoria_id' => 'required|exists:categories,id',
        ]);

        $data['user_id'] = auth()->id();

        return Invoice::create($data);
    }

    public function show(Invoice $invoice)
    {
        return $invoice->load(['category', 'user']);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'numero' => 'required|string|max:255',
            'fornecedor' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'data' => 'required|date',
            'categoria_id' => 'required|exists:categories,id',
        ]);

        $invoice->update($data);

        return $invoice;
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response()->json(['message' => 'Nota fiscal removida']);
    }
}
