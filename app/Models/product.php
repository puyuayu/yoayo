<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name', 
        'description',
        'price',
        'stock',
        'image'
    ];

    // Relationship dengan categories - PERBAIKI INI
    public function category(): BelongsTo
    {
        return $this->belongsTo(Categories::class, 'category_id'); // Ganti Category::class → Categories::class
    }
}