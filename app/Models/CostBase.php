<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostBase extends Model
{
    protected $table = 'costs_base';

    protected $fillable = [
        'Categoria',
        'AJUSTES',
        'Pago jan',
        'Pago fev',
        'Pago mar',
        'Pago abr',
        'Pago mai',
        'Pago jun',
        'Pago jul',
        'Pago ago',
        'Pago set',
        'Pago out',
        'Pago nov',
        'Pago dez',
        'Ano',
    ];

    public function getMonthlyValues()
    {
        return [
            'jan' => $this->{'Pago jan'},
            'fev' => $this->{'Pago fev'},
            'mar' => $this->{'Pago mar'},
            'abr' => $this->{'Pago abr'},
            'mai' => $this->{'Pago mai'},
            'jun' => $this->{'Pago jun'},
            'jul' => $this->{'Pago jul'},
            'ago' => $this->{'Pago ago'},
            'set' => $this->{'Pago set'},
            'out' => $this->{'Pago out'},
            'nov' => $this->{'Pago nov'},
            'dez' => $this->{'Pago dez'},
        ];
    }
    public function attachments()
{
    return $this->hasMany(CostAttachment::class, 'cost_base_id');
}

public function attachmentByMonth($m)
{
    return $this->attachments()->where('mes', $m)->first();
}

}
