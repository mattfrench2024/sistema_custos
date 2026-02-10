<?php

namespace App\Http\Controllers;

use App\Models\OmieServico;

class OmieServicoController extends Controller
{
    protected array $empresaNomes = [
        'vs' => 'Verreschi Soluções',
        'gv' => 'Grupo Verreschi',
        'sv' => 'Sociedade Advogados Verreschi',
        'cs' => 'Consultoria Soluções',
    ];

    protected array $empresas = [
        'vs' => '30',
        'gv' => '36',
        'sv' => '04',
        'cs' => '10',
    ];

    public function index(string $empresa)
    {
        if (! isset($this->empresas[$empresa])) {
            abort(404);
        }

        $empresaId   = $this->empresas[$empresa];
        $empresaNome = $this->empresaNomes[$empresa];

        $servicos = OmieServico::with([
        'categoria' => function ($query) use ($empresaId) {
            $query->where('empresa', $empresaId);
        }
    ])
    ->empresa($empresaId)
    ->orderBy('descricao')
    ->paginate(5)
    ->withQueryString();


        return view('omie.servicos.index', compact(
            'servicos',
            'empresa',
            'empresaNome'
        ));
    }

    public function show(string $empresa, OmieServico $servico)
{
    // Empresa inválida ou serviço não pertence à empresa
    if (
        ! isset($this->empresas[$empresa]) ||
        $servico->empresa !== $this->empresas[$empresa]
    ) {
        abort(404);
    }

    // Carrega relacionamento com escopo de empresa
    $servico->load([
        'categoria' => function ($query) use ($empresa) {
            $query->where('empresa', $this->empresas[$empresa]);
        }
    ]);

    return view('omie.servicos.show', compact(
        'servico',
        'empresa'
    ));
}

}
