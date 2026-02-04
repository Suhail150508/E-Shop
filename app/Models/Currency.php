<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'symbol',
        'rate',
        'is_default',
        'status',
    ];

    protected $casts = [
        'rate' => 'decimal:6',
        'is_default' => 'boolean',
        'status' => 'boolean',
    ];
}
