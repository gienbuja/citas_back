<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $casts = [
        'fecha' => 'date'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
