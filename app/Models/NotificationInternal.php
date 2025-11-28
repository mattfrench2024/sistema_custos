<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationInternal extends Model
{
    protected $table = 'notifications_internal';

    protected $fillable = [
        'user_id', 'titulo', 'mensagem', 'visualizado'
    ];

    protected $casts = [
        'visualizado' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
