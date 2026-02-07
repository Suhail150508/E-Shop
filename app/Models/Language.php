<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'direction',
        'is_default',
        'status',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'status' => 'boolean',
    ];

    /**
     * Scope: active languages only.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Get the default language (for storing main column values and fallback).
     */
    public static function getDefault(): ?self
    {
        return static::where('is_default', true)->where('status', true)->first()
            ?? static::where('status', true)->first();
    }

    /**
     * Get default language code.
     */
    public static function getDefaultCode(): string
    {
        $lang = static::getDefault();

        return $lang ? $lang->code : config('app.locale', 'en');
    }

    /**
     * Get active languages for admin translation UI (default first).
     */
    public static function getActiveForAdmin(): \Illuminate\Database\Eloquent\Collection
    {
        return static::active()->orderByRaw('is_default DESC')->orderBy('name')->get();
    }
}
