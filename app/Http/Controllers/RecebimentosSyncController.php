<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecebimentosSyncController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $rows = DB::table('tb_pagamentos_processados')
            ->where('data_processamento', $date)
            ->orderBy('carteira')
            ->get();

        return view('financeiro.recebimentos.index', [
            'dados' => $rows,
            'process_date' => $date,
        ]);
    }
}
