<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\VerreschiApiService;
use Illuminate\Support\Facades\Log;

class ReceberController extends Controller
{
    private $meses = [
        1 => 'jan', 2 => 'fev', 3 => 'mar', 4 => 'abr',
        5 => 'mai', 6 => 'jun', 7 => 'jul', 8 => 'ago',
        9 => 'set', 10 => 'out', 11 => 'nov', 12 => 'dez'
    ];

    public function index(Request $request, VerreschiApiService $api)
    {
        $mes = intval($request->get('mes', date('m')));
        $ano = $request->get('ano', date('Y'));

        $mes_str = $this->meses[$mes];

        $col_pago   = "pago_{$mes_str}";
        $col_venc   = "venc_{$mes_str}";
        $col_status = "status_{$mes_str}";

        $dados = DB::table('costs_base')
            ->select(
                'Categoria',
                DB::raw("`{$col_pago}` AS valor"),
                DB::raw("{$col_venc} AS vencimento"),
                DB::raw("{$col_status} AS status_flag")
            )
            ->where('Ano', $ano)
            ->orderBy($col_venc)
            ->get()
            ->map(function ($item) use ($api) {

                /* -----------------------------
                 | REGRA BASE (local)
                 |------------------------------
                */
                if ($item->valor == 0 || $item->valor === null) {
                    $item->status = 'Sem Lançamento';
                    return $item;
                }

                $item->status = ($item->status_flag == 1) ? 'Pago' : 'Pendente';

                /* -----------------------------
                 | CONSULTA NA API
                 |------------------------------
                 | Usando Categoria como identificador
                 | (ajuste se for ID, NF, boleto, etc)
                 */
                try {
                    $response = $api->consultarContaReceber($item->Categoria);

                    if (isset($response['pago']) && $response['pago'] === true) {
                        $item->status = 'Pago (API)';
                    }

                } catch (\Throwable $e) {
                    // Nunca quebra a tela
                    Log::warning('Erro API Verreschi', [
                        'categoria' => $item->Categoria,
                        'erro' => $e->getMessage()
                    ]);
                }

                /* -----------------------------
                 | REGRA DE VENCIMENTO
                 |------------------------------
                 */
                if (
                    $item->status !== 'Pago' &&
                    $item->status !== 'Pago (API)' &&
                    $item->vencimento &&
                    now()->gt($item->vencimento)
                ) {
                    $item->status = 'Vencido';
                }

                return $item;
            });

        return view('financeiro.receber.index', compact('dados', 'mes', 'ano'));
    }
}
