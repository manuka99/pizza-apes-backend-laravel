<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;
    protected $fillable = ['type', 'product_id', 'url', 'name'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
