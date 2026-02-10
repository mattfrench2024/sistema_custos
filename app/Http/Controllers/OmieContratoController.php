<?php

namespace App\Http\Controllers;

use App\Models\OmieContrato;
use Illuminate\Http\Request;

class OmieContratoController extends Controller
{
    protected array $empresas = [
        'sv' => ['codigo' => '04', 'label' => 'S. Verreschi Advogados'],
        'vs' => ['codigo' => '30', 'label' => 'Verreschi Soluções'],
        'gv' => ['codigo' => '36', 'label' => 'Grupo Verreschi'],
        'cs' => ['codigo' => '10', 'label' => 'Consultoria Soluções'],
    ];

    /**
     * 📄 LISTAGEM DE CONTRATOS
     * Visão analítica / executiva
     */
    public function index(Request $request, string $empresa)
    {
        // 1️⃣ Validar empresa
        if (!isset($this->empresas[$empresa])) {
            abort(404, 'Empresa inválida');
        }

        $empresaCodigo = $this->empresas[$empresa]['codigo'];
        $empresaLabel  = $this->empresas[$empresa]['label'];

        // 2️⃣ Query base
        $contratos = OmieContrato::query()
            ->where('empresa', $empresaCodigo)

            // 🔎 filtros opcionais
            ->when($request->filled('categoria'), fn ($q) =>
                $q->where('cCodCateg', $request->categoria)
            )

            ->when($request->filled('status'), fn ($q) =>
                $q->where('cCodSit', $request->status)
            )

            ->when($request->boolean('vigentes'), function ($q) {
                $hoje = now()->toDateString();
                $q->whereDate('dVigInicial', '<=', $hoje)
                  ->whereDate('dVigFinal', '>=', $hoje);
            })

            // 3️⃣ Relacionamentos
            ->with([
                'cliente:id,codigo_cliente_omie,razao_social,nome_fantasia',
                'categoria:id,codigo,descricao',
            ])

            ->orderBy('dVigFinal')
            ->paginate(20);

        // 4️⃣ Totais consolidados (para cards / dashboard)
        $totais = [
            'quantidade' => $contratos->total(),
            'valor_mensal' => $contratos->sum('nValTotMes'),
        ];

        // 5️⃣ View
        return view('omie.contratos.index', [
            'contratos'    => $contratos,
            'totais'       => $totais,
            'empresaSlug'  => $empresa,
            'empresaLabel' => $empresaLabel,
        ]);
    }

    /**
     * 🔎 DETALHE DO CONTRATO
     * Visão 360° + financeiro
     */
    public function show(string $empresa, int $contratoId)
    {
        // 1️⃣ Validar empresa
        if (!isset($this->empresas[$empresa])) {
            abort(404, 'Empresa inválida');
        }

        $empresaCodigo = $this->empresas[$empresa]['codigo'];
        $empresaLabel  = $this->empresas[$empresa]['label'];

        // 2️⃣ Carregar contrato + relações
        $contrato = OmieContrato::with([
            'cliente',
            'categoria',
            'receber' => fn ($q) =>
                $q->where('empresa', $empresaCodigo)
                  ->orderBy('data_vencimento'),
        ])
        ->where('empresa', $empresaCodigo)
        ->findOrFail($contratoId);

        // 3️⃣ Financeiro consolidado
        $financeiro = [
            'valor_mensal' => $contrato->nValTotMes,

            'total_recebido' => $contrato->receber
                ->where('status', 'liquidado')
                ->sum('valor_documento'),

            'total_pendente' => $contrato->receber
                ->where('status', 'pendente')
                ->sum('valor_documento'),

            'quantidade_titulos' => $contrato->receber->count(),
        ];

        // 4️⃣ View
        return view('omie.contratos.show', [
            'contrato'      => $contrato,
            'financeiro'    => $financeiro,
            'empresaSlug'   => $empresa,
            'empresaLabel'  => $empresaLabel,
        ]);
    }
}
