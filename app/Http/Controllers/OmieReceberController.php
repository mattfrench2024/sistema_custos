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
    if (!isset($this->empresas[$empresa])) {
        abort(404);
    }

    $empresa_id = $this->empresas[$empresa];

    $receber = OmieReceber::where('empresa', $empresa_id)
        ->orderByDesc('data_vencimento')
        ->paginate(20);

    $empresaNome = $this->empresaNomes[$empresa];

    return view('omie.receber.index', compact('receber', 'empresa', 'empresaNome'));
}


public function show($empresa, OmieReceber $receber)
{
    if (!isset($this->empresas[$empresa]) || $receber->empresa != $this->empresas[$empresa]) {
        abort(404);
    }

    return view('omie.receber.show', compact('receber', 'empresa'));
}


}
