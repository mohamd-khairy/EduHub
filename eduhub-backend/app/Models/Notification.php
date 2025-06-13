<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Notification extends Model implements Auditable
{
use HasFactory;
 use \OwenIt\Auditing\Auditable;   
 public static bool $inPermission = true;


    protected $fillable = [
        'user_id', 'user_type', 'title', 'message', 'is_read'
    ];

    public function notifiable()
    {
        return $this->morphTo(__FUNCTION__, 'user_type', 'user_id');
    }
}
