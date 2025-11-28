<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'user_id', 'mes', 'ano', 'salario_base', 'beneficios', 'descontos'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

