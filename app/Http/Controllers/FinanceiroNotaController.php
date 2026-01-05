<?php

namespace App\Http\Controllers;

use App\Models\CostBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FinanceiroNotaController extends Controller
{
    public function show($id, $month)
    {
        $cost = CostBase::findOrFail($id);

        $month = strtolower($month);

        $valorField  = "pago_$month";
        $statusField = "status_$month";
        $fileField   = "file_$month";

        return response()->json([
            'valor'    => $cost->{$valorField},
            'status'   => $cost->{$statusField} ?? 1,
            'file_url' => $cost->{$fileField}
                ? Storage::disk('public')->url($cost->{$fileField})
                : null,
        ]);
    }

    public function store(Request $request, $id, $month)
    {
        $request->validate([
            'valor' => 'nullable|numeric',
            'status' => 'required|in:0,1',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $cost = CostBase::findOrFail($id);

        $month = strtolower($month);

        $valorField  = "pago_$month";
        $statusField = "status_$month";
        $fileField   = "file_$month";

        // VALOR
        if ($request->filled('valor')) {
            $cost->{$valorField} = $request->valor;
        }

        // STATUS
        $cost->{$statusField} = $request->status;

        // ARQUIVO
        if ($request->hasFile('file')) {

            if ($cost->{$fileField}) {
                Storage::disk('public')->delete($cost->{$fileField});
            }

            $path = $request->file('file')->store('notas', 'public');
            $cost->{$fileField} = $path;
        }

        $cost->save();

        return response()->json(['success' => true]);
    }
}
