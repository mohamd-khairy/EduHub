<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'user_type', 'title', 'message', 'is_read'
    ];

    public function notifiable()
    {
        return $this->morphTo(__FUNCTION__, 'user_type', 'user_id');
    }
}
