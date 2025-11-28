<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'numero', 'cliente', 'valor', 'data_emissao', 'data_vencimento', 'status'
    ];

    protected $casts = [
        'data_emissao' => 'date',
        'data_vencimento' => 'date',
    ];
}
