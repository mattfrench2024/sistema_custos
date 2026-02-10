<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmieDocumentosFiscais extends Model
{
    protected $table = 'omie_documentos_fiscais';

    protected $fillable = [
        'empresa',
        'tipo_documento',
        'omie_id',
        'numero',
        'chave',
        'data_emissao',
        'xml',
        'pdf_url',
        'portal_url',
        'status',
        'erro',
    ];

    protected $casts = [
        'data_emissao' => 'date',
    ];

    /**
     * =====================
     * SCOPES
     * =====================
     */

    /**
     * Documentos ainda não baixados ou pendentes
     */
    public function scopePendentes($query)
{
    return $query->where(function ($q) {
        $q->whereNull('xml')
          ->orWhere('xml', '')
          ->orWhereNull('status')
          ->orWhere('status', '!=', 'ok');
    });
}

}
