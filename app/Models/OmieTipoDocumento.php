<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmieTipoDocumento extends Model
{
    protected $table = 'omie_tipos_documento';

    protected $fillable = [
        'codigo',
        'descricao',
    ];
}
