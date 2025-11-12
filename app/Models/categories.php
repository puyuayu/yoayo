<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categories extends Model
{
    protected $table = 'categories';
    
    protected $fillable = [
        'name',
        'description'
    ];

    // Relationship dengan products
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}