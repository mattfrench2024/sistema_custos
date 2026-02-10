<?php

namespace App\Http\Controllers;

use App\Models\OmieCliente;
use App\Services\Omie\OmieClient;
use Illuminate\Http\Request;

class OmieClienteController extends Controller
{
    protected $empresas = [
    'sv' => '04',
    'vs' => '30',
    'gv' => '36',
    'cs' => '10',
];

protected $empresaNomes = [
    'sv' => 'Sociedade Advogados Verreschi',
    'vs' => 'Verreschi Soluções',
    'gv' => 'Grupo Verreschi',
    'cs' => 'Consultoria Soluções',
];

public function index(string $empresa, Request $request)
{
    if (! isset($this->empresas[$empresa])) {
        abort(404);
    }

    $empresaId = $this->empresas[$empresa];

    $clientes = OmieCliente::where('empresa', $empresaId)
        ->when($request->search, function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('razao_social', 'like', "%{$request->search}%")
                    ->orWhere('cnpj_cpf', 'like', "%{$request->search}%");
            });
        })
        ->orderBy('razao_social')
        ->paginate(20)
        ->withQueryString();

    return view('omie.clientes.index', [
        'clientes' => $clientes,
        'empresa'  => $empresa,
        'empresaLabel' => $this->empresaNomes[$empresa],
    ]);
}


    public function show(string $empresa, OmieCliente $cliente)
{
    if (
        ! isset($this->empresas[$empresa]) ||
        $cliente->empresa !== $this->empresas[$empresa]
    ) {
        abort(404);
    }

    return view('omie.clientes.show', compact('cliente', 'empresa'));
}

}
