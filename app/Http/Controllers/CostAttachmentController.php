<?php

namespace App\Http\Controllers;

use App\Models\CostBase;
use App\Models\CostAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CostAttachmentController extends Controller
{
    /**
     * Retorna dados do anexo do mês
     */
    public function show(CostBase $cost, $mes)
    {
        $attachment = $cost->attachments()
            ->where('mes', $mes)
            ->first();

        return response()->json([
            'categoria' => $cost->Categoria,
            'mes'       => $mes,
            'valor'     => $attachment->valor ?? $cost->{'Pago '.$mes},
            'file_url'  => $attachment && $attachment->arquivo
                ? Storage::url($attachment->arquivo)
                : null,
        ]);
    }


    /**
     * Salva/atualiza anexo
     */
    public function store(Request $request, CostBase $cost, $mes)
    {
        $request->validate([
            'valor'   => 'nullable|numeric',
            'file'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $data = [
            'cost_base_id' => $cost->id,
            'mes'          => $mes,
            'valor'        => $request->valor,
        ];

        // upload
        if ($request->hasFile('file')) {
            $data['arquivo'] = $request->file('file')->store('notas', 'public');
        }

        CostAttachment::updateOrCreate(
            ['cost_base_id' => $cost->id, 'mes' => $mes],
            $data
        );

        return response()->json(['success' => true]);
    }
}
