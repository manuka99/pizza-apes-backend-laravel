<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionData extends Model
{
    use HasFactory;

    protected $table = "sessions";
    protected $keyType = 'string';
    public $timestamps = false;

    protected $appends = ['last_online'];

    public function getLastOnlineAttribute()
    {
        $moment = Helper::preetyTime($this->last_activity);
        if ($moment === "Just now")
            return "Online";
        return $moment;
    }
}
