<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryArea extends Model
{
    use HasFactory;

    public function stores()
    {
        return $this->belongsToMany(DeliveryStore::class, 'store_area');
    }

    public function classes()
    {
        return $this->belongsToMany(DeliveryClass::class, 'area_classes');
    }
}
