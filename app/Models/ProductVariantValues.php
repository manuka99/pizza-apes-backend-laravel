<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantValues extends Model
{
    protected $fillable = [
        'product_varient_id',
        'product_id',
        'option_values_id',
    ];
    use HasFactory;
}
