<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'url_name',
        'product_name',
        'status',
        'visibility',
        'published_on',
        'is_featured',
        'short_description',
        'description',
        'type',
        'is_trashed',
        'default_variation',
        'image',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function options()
    {
        return $this->hasMany(Options::class);
    }

    public function productVarients()
    {
        return $this->hasMany(ProductVarient::class);
    }
}
