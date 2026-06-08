<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedNews extends Model
{
    protected $fillable = [
        'user_id',
        'news_hash',
        'topic',
        'title',
        'url',
        'source',
        'summary',
        'published_label',
    ];
}
