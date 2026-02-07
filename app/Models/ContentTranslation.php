<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Stores translated values for dynamic content (Product, Page, etc.).
 * One row per translatable field per language.
 */
class ContentTranslation extends Model
{
    protected $table = 'content_translations';

    protected $fillable = [
        'translatable_type',
        'translatable_id',
        'language_code',
        'field',
        'value',
    ];

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }
}
