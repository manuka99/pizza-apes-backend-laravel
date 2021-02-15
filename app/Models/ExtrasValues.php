<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtrasValues extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'image',
        'price',
        'layer_image',
    ];

    public function extras()
    {
        return $this->belongsTo(Extras::class);
    }
}
