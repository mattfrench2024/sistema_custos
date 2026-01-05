<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index()
    {
        $notas = DB::table('costs_base')
            ->select(
                'id',
                'Categoria',
                'Ano',
                'cnpj',
                'file_jan',
                'file_fev',
                'file_mar',
                'file_abr',
                'file_mai',
                'file_jun',
                'file_jul',
                'file_ago',
                'file_set',
                'file_out',
                'file_nov',
                'file_dez'
            )
            ->where(function($query) {
                $query->whereNotNull('file_jan')
                      ->orWhereNotNull('file_fev')
                      ->orWhereNotNull('file_mar')
                      ->orWhereNotNull('file_abr')
                      ->orWhereNotNull('file_mai')
                      ->orWhereNotNull('file_jun')
                      ->orWhereNotNull('file_jul')
                      ->orWhereNotNull('file_ago')
                      ->orWhereNotNull('file_set')
                      ->orWhereNotNull('file_out')
                      ->orWhereNotNull('file_nov')
                      ->orWhereNotNull('file_dez');
            })
            ->orderBy('Ano')
            ->orderBy('Categoria')
            ->get();

        return view('invoices.audit', compact('notas'));
    }
}
