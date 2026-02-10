<?php

namespace App\Http\Controllers;

use App\Models\OmieResumoFinanca;

class OmieResumoFinancasController extends Controller
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
    

    public function index(string $empresa)
    {
        if (! isset($this->empresas[$empresa])) {
            abort(404);
        }

        $empresaId   = $this->empresas[$empresa];
        $empresaNome = $this->empresaNomes[$empresa];

        $resumos = OmieResumoFinanca::where('empresa', $empresaId)
            ->orderByDesc('data_referencia')
            ->paginate(15)
            ->withQueryString();

        return view('omie.resumo-financas.index', compact(
            'resumos',
            'empresa',
            'empresaNome'
        ));
    }

    public function show(string $empresa, OmieResumoFinanca $resumo)
    {
        if (
            ! isset($this->empresas[$empresa]) ||
            $resumo->empresa !== $this->empresas[$empresa]
        ) {
            abort(404);
        }

        return view('omie.resumo-financas.show', compact(
            'resumo',
            'empresa'
        ));
    }
}
