<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryStore extends Model
{
    use HasFactory;

    public function areas()
    {
        return $this->belongsToMany(DeliveryArea::class, 'store_area');
    }
}
