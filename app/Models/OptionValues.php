<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionValues extends Model
{
    use HasFactory;

    public function option()
    {
        return $this->belongsTo(Options::class);
    }

    public function productVarientValues()
    {
        return $this->belongsToMany(ProductVarient::class, 'product_variant_values');
    }

}
