<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmieTarefa extends Model
{
    protected $table = 'omie_tarefas';

    protected $fillable = [
        'empresa',
        'codigo_tarefa',
        'codigo_oportunidade',
        'codigo_integracao',
        'codigo_usuario',
        'codigo_atividade',
        'data_tarefa',
        'hora_tarefa',
        'importante',
        'urgente',
        'em_execucao',
        'realizada',
        'descricao',
        'detalhes_oportunidade',
        'payload',
    ];

    protected $casts = [
        'data_tarefa' => 'date',
        'hora_tarefa' => 'datetime:H:i',
        'importante' => 'boolean',
        'urgente' => 'boolean',
        'em_execucao' => 'boolean',
        'realizada' => 'boolean',
        'detalhes_oportunidade' => 'array',
        'payload' => 'array',
    ];
}
