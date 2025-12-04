<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostAttachment extends Model
{
    protected $fillable = [
        'cost_base_id',
        'mes',
        'valor',
        'arquivo',
    ];

    public function cost()
    {
        return $this->belongsTo(CostBase::class, 'cost_base_id');
    }
}
