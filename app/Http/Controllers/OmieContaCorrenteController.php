<?php

namespace App\Http\Controllers;

use App\Models\OmieContaCorrente;
use Illuminate\Http\Request;

class OmieContaCorrenteController extends Controller
{
    protected array $empresas = [
        'sv' => ['codigo' => '04', 'label' => 'S. Verreschi Advogados'],
        'vs' => ['codigo' => '30', 'label' => 'Verreschi Soluções'],
        'gv' => ['codigo' => '36', 'label' => 'Grupo Verreschi'],
    ];
    /**
     * Listagem das contas correntes
     */
    // app/Http/Controllers/OmieContaCorrenteController.php

public function index(Request $request, string $empresa)
{
    // 1️⃣ Validar slug da empresa
    if (!isset($this->empresas[$empresa])) {
        abort(404, 'Empresa inválida');
    }

    $empresaCodigo = $this->empresas[$empresa]['codigo'];
    $empresaLabel  = $this->empresas[$empresa]['label'];

    // 2️⃣ Buscar contas correntes da empresa, apenas ativas
    $contas = OmieContaCorrente::query()
        ->empresa($empresaCodigo)
        ->ativas()
        ->with(['pagar', 'receber']) // ⬅️ carregar lançamentos
        ->orderBy('descricao')
        ->get();

    // 3️⃣ Calcular saldo atual por conta
    $contas->transform(function ($conta) {

        // Somar valores a receber pendentes
        $totalReceber = $conta->receber
            ->where('status', 'pendente') // ajustar se necessário
            ->sum('valor_documento');

        // Somar valores a pagar pendentes
        $totalPagar = $conta->pagar
            ->where('status_titulo', 'aberto') // ou 'pendente', conforme sua regra
            ->sum('valor_documento');

        // Atualiza saldo atual dinamicamente
        $conta->saldo_atual = $conta->saldo_inicial + $totalReceber - $totalPagar;

        return $conta;
    });

    // 4️⃣ Somar saldo total consolidado
    $saldoTotal = $contas->sum('saldo_atual');

    // 5️⃣ Retornar view com todos os dados
    return view('omie.contas-correntes.index', [
        'contas'       => $contas,
        'saldoTotal'   => $saldoTotal,
        'empresaSlug'  => $empresa,
        'empresaLabel' => $empresaLabel,
    ]);
}




    /**
     * Detalhe da conta corrente (SHOW)
     */
    public function show(string $empresa, int $contaCorrente)
{
    // 1️⃣ Valida se a empresa existe no array de empresas
    if (!isset($this->empresas[$empresa])) {
        abort(404, 'Empresa inválida');
    }

    $empresaCodigo = $this->empresas[$empresa]['codigo'];
    $empresaLabel  = $this->empresas[$empresa]['label'];

    // 2️⃣ Carrega a conta com todos os relacionamentos necessários
    $conta = OmieContaCorrente::with([
        'pagar' => fn($q) => $q->where('empresa', $empresaCodigo)
                                ->orderBy('data_vencimento', 'desc'),
        'receber' => fn($q) => $q->where('empresa', $empresaCodigo)
                                  ->orderBy('data_vencimento', 'desc')
    ])->where('empresa_codigo', $empresaCodigo)
      ->findOrFail($contaCorrente);

    // 3️⃣ Calcula saldos consolidados
    $saldoPagar       = $conta->pagar->sum('valor_documento');    // Total contas a pagar
    $saldoReceber     = $conta->receber->sum('valor_documento');  // Total contas a receber
    $saldoConsolidado = $conta->saldo_inicial + $saldoReceber - $saldoPagar;

    // 4️⃣ Passa dados para a view
    return view('omie.contas-correntes.show', [
        'conta'            => $conta,
        'saldoPagar'       => $saldoPagar,
        'saldoReceber'     => $saldoReceber,
        'saldoConsolidado' => $saldoConsolidado,
        'empresaSlug'      => $empresa,
        'empresaLabel'     => $empresaLabel,
    ]);
}


}
