<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryClassOptions extends Model
{
    use HasFactory;

    public function classes()
    {
        return $this->belongsTo(DeliveryClass::class);
    }
}
