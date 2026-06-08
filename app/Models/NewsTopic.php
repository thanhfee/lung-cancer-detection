<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsTopic extends Model
{
    protected $fillable = [
        'label',
        'query',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
