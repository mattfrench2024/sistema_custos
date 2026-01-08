<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OmieReceber;

class OmieReceberController extends Controller
{// OmieReceberController.php
protected $empresaNomes = [
    'vs' => 'Verreschi Soluções',
    'gv' => 'Grupo Verreschi',
    'sv' => 'Sociedade Advogados Verreschi',
];
protected $empresas = [
    'vs' => '30',
    'gv' => '36',
    'sv' => '04',
];


public function index($empresa)
{
    if (! isset($this->empresas[$empresa])) {
        abort(404);
    }

    $empresaId   = $this->empresas[$empresa];
    $empresaNome = $this->empresaNomes[$empresa];

    $receber = OmieReceber::with([
            'cliente' => function ($q) use ($empresaId) {
                $q->where('empresa', $empresaId);
            },
        ])
        ->where('empresa', $empresaId)
        ->orderByDesc('data_vencimento')
        ->paginate(20)
        ->withQueryString();

    return view('omie.receber.index', compact(
        'receber',
        'empresa',
        'empresaNome'
    ));
}


public function show($empresa, OmieReceber $receber)
{
    if (
        ! isset($this->empresas[$empresa]) ||
        $receber->empresa !== $this->empresas[$empresa]
    ) {
        abort(404);
    }

    $receber->load([
        'cliente' => fn ($q) =>
            $q->where('empresa', $this->empresas[$empresa]),
    ]);

    return view('omie.receber.show', compact('receber', 'empresa'));
}



}
