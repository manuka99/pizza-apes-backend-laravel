<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extras extends Model
{
    use HasFactory;

    public function extrasValues()
    {
        return $this->hasMany(ExtrasValues::class);
    }
    
    public function productVarients()
    {
        return $this->belongsToMany(ProductVarient::class, 'product_variant_extras');
    }
}
