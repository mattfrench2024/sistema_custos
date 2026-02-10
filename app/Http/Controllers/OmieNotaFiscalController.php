<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OmieNotaFiscal;
use App\Services\NfsePdfService;

class OmieNotaFiscalController extends Controller
{
    protected $empresaNomes = [
        'vs' => 'Verreschi Soluções',
        'gv' => 'Grupo Verreschi',
        'sv' => 'Sociedade Advogados Verreschi',
        'cs' => 'Consultoria Soluções',
    ];

    protected $empresas = [
        'vs' => '30',
        'gv' => '36',
        'sv' => '04',
        'cs' => '10',
    ];

    public function index(Request $request, $empresa)
    {
        if (!isset($this->empresas[$empresa])) {
            abort(404);
        }

        $empresaId   = $this->empresas[$empresa];
        $empresaNome = $this->empresaNomes[$empresa];

        $query = OmieNotaFiscal::where('empresa', $empresaId);

        if ($request->filled('ano')) {
            $query->whereYear('data_emissao', $request->ano);
        }

        if ($request->filled('mes')) {
            $query->whereMonth('data_emissao', $request->mes);
        }

        $notas = $query
            ->orderByDesc('data_emissao')
            ->paginate(25)
            ->withQueryString();

        return view('omie.notas-fiscais.index', compact(
            'notas',
            'empresa',
            'empresaNome'
        ));
    }

    public function gerarPdf(
    string $empresa,
    OmieNotaFiscal $nota,
    NfsePdfService $service
) {
    // Segurança ERP: valida se a nota pertence à empresa
    if ($nota->empresa !== $this->empresas[$empresa]) {
        abort(403, 'Nota fiscal não pertence à empresa selecionada.');
    }

    // Evita gerar duas vezes
    if ($nota->possui_pdf && $nota->pdf_path) {
        return back()->with('info', 'PDF já foi gerado.');
    }

    $service->gerar($nota);

    return back()->with('success', 'PDF gerado com sucesso.');
}
public function verPdf(string $empresa, OmieNotaFiscal $nota)
{
    if ($nota->empresa !== $this->empresas[$empresa]) {
        abort(403);
    }

    if (!$nota->possui_pdf || !$nota->pdf_path) {
        abort(404, 'PDF ainda não foi gerado.');
    }

    return response()->file(
        storage_path('app/public/' . $nota->pdf_path)
    );
}

}
