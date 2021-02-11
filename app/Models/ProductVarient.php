<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVarient extends Model
{
    use HasFactory;
    protected $fillable = [
        'sku_id',
        'image',
        'regular_price',
        'offer_price',
        'offer_from',
        'offer_to',
        'stock',
        'low_stock_threshold',
        'back_orders',
        'order_limit_count',
        'order_limit_days',
        'weight',
        'shipping_class'
    ];

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
