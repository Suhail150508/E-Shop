<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'position', 'is_active'];

    public function items()
    {
        return $this->hasMany(MenuItem::class)->orderBy('order');
    }

    // Helper to get root items
    public function rootItems()
    {
        return $this->hasMany(MenuItem::class)->whereNull('parent_id')->orderBy('order');
    }
}
