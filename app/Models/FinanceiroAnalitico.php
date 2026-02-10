<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinanceiroAnalitico extends Model
{
    public $timestamps = false;

    /* =========================================================
     * CAMADA SEMÂNTICA (EMPRESAS)
     * ========================================================= */
    public static function empresasMap()
    {
        return [
            '04' => 'S. Verreschi Advogados',
            '30' => 'Verreschi Soluções',
            '36' => 'Grupo Verreschi',
            '10' => 'Consultoria Soluções',
        ];
    }
    public static function empresasSlugMap()
{
    return [
        'sverreschi' => '04',
        'verreschi'  => '30',
        'grupo'      => '36',
        'consultoria'=> '10',
    ];
}


    private static function nomeEmpresa($codigo)
    {
        return self::empresasMap()[$codigo] ?? $codigo;
    }

    /* =========================================================
     * BASES COM FILTROS DINÂMICOS (ÚNICA FONTE DA VERDADE)
     * ========================================================= */
private static function receberBase($ano, $mes = null, $empresa = null)
{
    return DB::table('omie_receber as base')
        ->whereYear('base.data_vencimento', $ano)
        ->when($mes, fn ($q) => $q->whereMonth('base.data_vencimento', $mes))
        ->when($empresa, fn ($q) => $q->where('base.empresa', $empresa))
        ->whereRaw('UPPER(base.status) = "RECEBIDO"');
}


private static function pagarBase($ano, $mes = null, $empresa = null)
{
    return DB::table('omie_pagar as base')
        ->whereYear('base.data_vencimento', $ano)
        ->when($mes, fn ($q) => $q->whereMonth('base.data_vencimento', $mes))
        ->when($empresa, fn ($q) => $q->where('base.empresa', $empresa))
        ->where('base.status_titulo', 'PAGO');
}




    /* =========================================================
     * KPIs EXECUTIVOS (BOARD LEVEL)
     * ========================================================= */
    public static function kpis($ano, $mes = null, $empresa = null)
{
    $receita = self::receberBase($ano, $mes, $empresa)->sum('valor_documento');
    $custos  = self::pagarBase($ano, $mes, $empresa)->sum('valor_documento');

    $saldo  = $receita - $custos;
    $margem = $receita > 0 ? ($saldo / $receita) * 100 : 0;

    return [
        'receita' => round($receita, 2),
        'custos'  => round($custos, 2),
        'saldo'   => round($saldo, 2),
        'margem'  => round($margem, 2),
    ];
}


    /* =========================================================
     * EVOLUÇÃO MENSAL + VOLATILIDADE
     * ========================================================= */
    public static function dashboardMensal($ano, $empresa = null)
    {
        $receber = self::receberBase($ano, null, $empresa)
            ->selectRaw('MONTH(data_vencimento) mes, SUM(valor_documento) total')
            ->groupBy('mes')
            ->pluck('total', 'mes');

        $pagar = self::pagarBase($ano, null, $empresa)
            ->selectRaw('MONTH(data_vencimento) mes, SUM(valor_documento) total')
            ->groupBy('mes')
            ->pluck('total', 'mes');

        $data = [];
        $anterior = null;

        for ($m = 1; $m <= 12; $m++) {
            $r = $receber[$m] ?? 0;
            $p = $pagar[$m] ?? 0;
            $saldo = $r - $p;

            $variacao = $anterior !== null && $anterior != 0
                ? (($saldo - $anterior) / abs($anterior)) * 100
                : null;

            $data[] = [
                'mes'       => Carbon::create()->month($m)->translatedFormat('F'),
                'receita'   => round($r, 2),
                'custos'    => round($p, 2),
                'saldo'     => round($saldo, 2),
                'variacao'  => $variacao !== null ? round($variacao, 2) : null,
                'negativo'  => $saldo < 0,
            ];

            $anterior = $saldo;
        }

        return $data;
    }

    /* =========================================================
     * CONCENTRAÇÃO DE RECEITA (RISCO)
     * ========================================================= */
    public static function concentracaoReceita($ano, $mes = null, $empresa = null)
{
    $total = self::receberBase($ano, $mes, $empresa)->sum('valor_documento');

    return self::receberBase($ano, $mes, $empresa)
        ->selectRaw('empresa, SUM(valor_documento) total')
        ->groupBy('empresa')
        ->orderByDesc('total')
        ->get()
        ->map(fn ($r) => [
            'empresa'    => self::nomeEmpresa($r->empresa),
            'percentual' => $total > 0
                ? round(($r->total / $total) * 100, 2)
                : 0,
        ]);
}


    /* =========================================================
     * ALERTAS AUTOMÁTICOS (INTELIGÊNCIA FINANCEIRA)
     * ========================================================= */
    public static function alertas($ano, $mes)
    {
        if (!$mes || $mes == 1) return [];

        $atual  = self::kpis($ano, $mes);
        $anterior = self::kpis($ano, $mes - 1);

        $alertas = [];

        if ($anterior['receita'] > 0) {
            $queda = (($atual['receita'] - $anterior['receita']) / $anterior['receita']) * 100;

            if ($queda < -15) {
                $alertas[] = "⚠️ Receita caiu " . round(abs($queda), 1) . "% em relação ao mês anterior.";
            }
        }

        if ($atual['custos'] > $atual['receita']) {
            $alertas[] = "🔥 Custos superaram a receita no período.";
        }

        if ($atual['margem'] < 10) {
            $alertas[] = "🚨 Margem operacional abaixo do nível seguro (10%).";
        }

        return $alertas;
    }

    /* =========================================================
     * CLASSIFICAÇÃO DE MÊS (SCORE FINANCEIRO)
     * ========================================================= */
    public static function scoreMes($ano, $mes)
    {
        $kpi = self::kpis($ano, $mes);

        $score = 100;

        if ($kpi['saldo'] < 0) $score -= 40;
        if ($kpi['margem'] < 10) $score -= 30;
        if ($kpi['margem'] < 0)  $score -= 30;

        return [
            'score' => max($score, 0),
            'status' => match (true) {
                $score >= 80 => 'Saudável',
                $score >= 60 => 'Atenção',
                default      => 'Crítico',
            }
        ];
    }
    private static function nomePessoaSql(): string
{
    return '
        COALESCE(
            c.nome_fantasia,
            c.razao_social,
            CONCAT("Cliente #", base.codigo_cliente_fornecedor)
        )
    ';
}
    /* =========================================================
 * TOP RECEBIMENTOS (CONCENTRAÇÃO POSITIVA)
 * ========================================================= */
public static function topRecebimentos($ano, $mes = null, $empresa = null)
{
    return self::receberBase($ano, $mes, $empresa)
        ->selectRaw('empresa, SUM(valor_documento) total')
        ->groupBy('empresa')
        ->orderByDesc('total')
        ->limit(5)
        ->get()
        ->map(fn ($r) => [
            'empresa' => self::nomeEmpresa($r->empresa),
            'total'   => round($r->total, 2),
        ]);
}

public static function topPagamentos($ano, $mes = null, $empresa = null)
{
    return self::pagarBase($ano, $mes, $empresa)
        ->selectRaw('empresa, SUM(valor_documento) total')
        ->groupBy('empresa')
        ->orderByDesc('total')
        ->limit(5)
        ->get()
        ->map(fn ($p) => [
            'empresa' => self::nomeEmpresa($p->empresa),
            'total'   => round($p->total, 2),
        ]);
}

public static function topClientes($ano, $mes = null, $empresa = null, $limit = 5)
{
    $query = self::receberBase($ano, $mes, $empresa)
        ->leftJoin('omie_clientes as c', function ($join) {
            $join->on(
                'c.codigo_cliente_omie',
                '=',
                'base.codigo_cliente_fornecedor'
            );
        })
        ->selectRaw('
            base.codigo_cliente_fornecedor,
            COALESCE(c.nome_fantasia, c.razao_social, CONCAT("Cliente #", base.codigo_cliente_fornecedor)) as cliente,
            SUM(base.valor_documento) as total
        ')
        ->groupBy('base.codigo_cliente_fornecedor', 'cliente')
        ->orderByDesc('total')
        ->limit($limit);

    return $query->get()->map(fn($c) => [
        'cliente' => $c->cliente,
        'codigo'  => $c->codigo_cliente_fornecedor,
        'total'   => round($c->total, 2),
    ]);
}

public static function topFornecedores($ano, $mes = null, $empresa = null, $limit = 5)
{
    $query = self::pagarBase($ano, $mes, $empresa)
        ->leftJoin('omie_clientes as c', function ($join) {
            $join->on(
                'c.codigo_cliente_omie',
                '=',
                'base.codigo_cliente_fornecedor'
            );
        })
        ->selectRaw('
            base.codigo_cliente_fornecedor,
            COALESCE(c.nome_fantasia, c.razao_social, CONCAT("Fornecedor #", base.codigo_cliente_fornecedor)) as fornecedor,
            SUM(base.valor_documento) as total
        ')
        ->groupBy('base.codigo_cliente_fornecedor', 'fornecedor')
        ->orderByDesc('total')
        ->limit($limit);

    return $query->get()->map(fn($f) => [
        'fornecedor' => $f->fornecedor,
        'codigo'     => $f->codigo_cliente_fornecedor,
        'total'      => round($f->total, 2),
    ]);
}






public static function concentracaoClientes($ano, $mes = null, $empresa = null)
{
    $total = self::receberBase($ano, $mes, $empresa)->sum('valor_documento');

    return self::receberBase($ano, $mes, $empresa)
        ->from('omie_receber as base')
        ->leftJoin('omie_clientes as c', function ($join) {
            $join->on(
                'c.codigo_cliente_omie',
                '=',
                'base.codigo_cliente_fornecedor'
            );
            $join->whereRaw(
                'c.empresa COLLATE utf8mb4_unicode_ci
                 = base.empresa COLLATE utf8mb4_unicode_ci'
            );
        })
        ->selectRaw('
            base.codigo_cliente_fornecedor,
            ' . self::nomePessoaSql() . ' as cliente,
            SUM(base.valor_documento) as total
        ')
        ->groupBy('base.codigo_cliente_fornecedor', 'cliente')
        ->orderByDesc('total')
        ->limit(5)
        ->get()
        ->map(fn ($c) => [
            'cliente' => $c->cliente,
            'percentual' => $total > 0
                ? round(($c->total / $total) * 100, 2)
                : 0,
        ]);
}


}