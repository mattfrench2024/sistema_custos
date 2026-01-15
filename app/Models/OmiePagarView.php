<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmiePagarView extends Model
{
    protected $table = 'omie_pagar_com_status';
    protected $primaryKey = 'id';

    public $incrementing = false;
    public $timestamps  = false;

    protected $guarded = [];

    /*
    |--------------------------------------------------------------------------
    | Casts (OBRIGATÓRIO para VIEW)
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'data_emissao'    => 'date',
        'data_vencimento' => 'date',
        'valor_documento' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relacionamentos
    |--------------------------------------------------------------------------
    */

    public function tipoDocumento()
    {
        return $this->belongsTo(
            OmieTipoDocumento::class,
            'codigo_tipo_documento',
            'codigo'
        );
    }

    public function fornecedor()
    {
        return $this->belongsTo(
            OmieCliente::class,
            'codigo_cliente_fornecedor',
            'codigo_cliente_omie'
        );
    }

    public function categoria()
    {
        return $this->belongsTo(
            OmieCategoria::class,
            'codigo_categoria',
            'codigo'
        );
    }

    public function contaCorrente()
    {
        return $this->belongsTo(
            OmieContaCorrente::class,
            'id_conta_corrente',
            'id'
        );
    }
}
