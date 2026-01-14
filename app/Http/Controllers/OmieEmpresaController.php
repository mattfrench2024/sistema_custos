<?php

namespace App\Http\Controllers;

use App\Models\OmieEmpresa;

class OmieEmpresaController extends Controller
{
    protected array $empresaNomes = [
        'vs' => 'Verreschi Soluções',
        'gv' => 'Grupo Verreschi',
        'sv' => 'Sociedade Advogados Verreschi',
    ];

    protected array $empresas = [
        'vs' => '30',
        'gv' => '36',
        'sv' => '04',
    ];

    // ==============================
    // LISTAGEM
    // ==============================
    public function index(string $empresa)
    {
        if (!isset($this->empresas[$empresa])) {
            abort(404);
        }

        $empresaId   = $this->empresas[$empresa];
        $empresaNome = $this->empresaNomes[$empresa];

        $empresas = OmieEmpresa::empresa($empresaId)
            ->orderBy('razao_social')
            ->paginate(5)
            ->withQueryString();

        return view('omie.empresas.index', compact(
            'empresas',
            'empresa',
            'empresaNome'
        ));
    }

    // ==============================
    // DETALHE
    // ==============================
    public function show(string $empresa, OmieEmpresa $empresaModel)
{
    // Verifica se a empresa existe no array de empresas autorizadas
    if (!isset($this->empresas[$empresa]) || $empresaModel->empresa !== $this->empresas[$empresa]) {
        abort(404);
    }

    // Carrega relacionamentos relevantes usando o campo "empresa" compatível
    // Limita cada relacionamento aos top 20 itens
    $empresaModel->load([
        'servicos' => function ($query) {
            $query->orderBy('descricao')   // ordenar por descrição ou outro critério de importância
                  ->take(20);              // limitar a 20 registros
        },
        'clientes' => function ($query) {
            $query->orderBy('razao_social') // ou outro critério de prioridade
                  ->take(20);               // limitar a 20 registros
        },
        'categorias' => function ($query) {
            $query->orderBy('descricao')   // ou outro critério de prioridade
                  ->take(20);              // limitar a 20 registros
        },
    ]);

    // Retorna a view com o modelo carregado
    return view('omie.empresas.show', [
        'empresaModel' => $empresaModel,
        'empresa' => $empresa,
    ]);
}

}
