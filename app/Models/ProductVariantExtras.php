<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantExtras extends Model
{
    protected $fillable = ['display_name', 'select_count', 'extras_id'];
    use HasFactory;
}
