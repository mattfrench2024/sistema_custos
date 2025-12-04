<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostEntry extends Model
{
    protected $table = 'cost_entries';

    protected $fillable = [
    'cost_base_id',
    'value',
    'description',
    'status_pago',
    'date',
];



    public function category()
    {
        return $this->belongsTo(CostBase::class, 'cost_base_id');
    }
}
