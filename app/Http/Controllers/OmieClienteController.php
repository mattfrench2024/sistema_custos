<?php

namespace App\Http\Controllers;

use App\Models\OmieCliente;
use App\Services\Omie\OmieClient;
use Illuminate\Http\Request;

class OmieClienteController extends Controller
{
    public function index(string $empresa, Request $request)
    {
        $config = config("omie.empresas.$empresa");

        abort_if(! $config, 404, 'Empresa Omie inválida');

        $clientes = OmieCliente::where('empresa', $empresa)
    ->when($request->search, function ($q) use ($request) {
        $q->where(function ($sub) use ($request) {
            $sub->where('razao_social', 'like', "%{$request->search}%")
                ->orWhere('cnpj_cpf', 'like', "%{$request->search}%");
        });
    })
    ->orderBy('razao_social')
    ->paginate(20);


        return view('omie.clientes.index', [
            'clientes' => $clientes,
            'empresa'  => $empresa,
            'empresaLabel' => $config['label'],
        ]);
    }

    public function show(string $empresa, OmieCliente $cliente)
    {
        abort_if($cliente->empresa !== $empresa, 404);

        return view('omie.clientes.show', compact('cliente', 'empresa'));
    }
}
