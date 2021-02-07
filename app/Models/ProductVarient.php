<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVarient extends Model
{
    use HasFactory;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVarientValues()
    {
        return $this->belongsToMany(OptionValues::class, 'product_variant_values');
    }

    public function extras()
    {
        return $this->belongsToMany(Extras::class, 'product_variant_extras');
    }
}
