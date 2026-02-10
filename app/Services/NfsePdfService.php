<?php

namespace App\Services;

use App\Models\OmieNotaFiscal;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class NfsePdfService
{
    public function gerar(OmieNotaFiscal $nota): string
{
    return DB::transaction(function () use ($nota) {

        $payload = $nota->payload;

        $pdf = Pdf::loadView('pdf.nfse', [
            'nota'    => $nota,
            'payload' => $payload,
        ])->setPaper('A4');

        $empresa = $nota->empresa;
        $tipo    = strtoupper($nota->tipo ?? 'NFSE');
        $ano     = optional($nota->data_emissao)->year ?? now()->year;
        $numero  = $nota->numero;

        $path = "notas/{$empresa}/{$tipo}/{$ano}/{$tipo}-{$numero}.pdf";

        Storage::disk('public')->put($path, $pdf->output());

        $nota->update([
            'possui_pdf' => true,
            'pdf_path'   => $path,
        ]);

        return $path;
    });
}
}
