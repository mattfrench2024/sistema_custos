<?php

namespace App\Http\Controllers;

use App\Models\CostBase;
use Illuminate\Http\Request;

class CostNoteController extends Controller
{
    public function show($id, $mes)
    {
        $cost = CostBase::findOrFail($id);

        $column = "Pago " . strtolower($mes);
        $valor = $cost->{$column} ?? 0;

        return response()->json([
            'categoria' => $cost->Categoria,
            'valor' => $valor,
            'vencimento' => $cost->{"venc_" . strtolower($mes)} ?? null,
'file_url' => $cost->{"file_" . strtolower($mes)}
    ? asset('storage/' . $cost->{"file_" . strtolower($mes)})
    : null,
        ]);
    }

    public function save(Request $request, $id, $mes)
{
    $cost = CostBase::findOrFail($id);

    $mes = strtolower($mes);

    $column     = "Pago " . $mes;       // Ex: "Pago abr"
    $fileColumn = "file_" . $mes;       // Ex: file_abr
    $vencColumn = "venc_" . $mes;       // Ex: venc_abr

    // Atualiza valor
    if ($request->valor !== null) {
        $cost->{$column} = $request->valor;
    }

    // Salva arquivo
    if ($request->hasFile('file')) {
        $path = $request->file('file')->store('notas', 'public');
        $cost->{$fileColumn} = $path;
    }

    // Atualiza vencimento (se enviado)
    if ($request->vencimento !== null) {
        $cost->{$vencColumn} = $request->vencimento;
    }

    $cost->save();

    return response()->json(['success' => true]);
}

}
