<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryClass extends Model
{
    use HasFactory;

    public function areas()
    {
        return $this->belongsToMany(DeliveryArea::class, 'area_classes');
    }

    public function options()
    {
        return $this->hasMany(DeliveryClassOptions::class);
    }
}
