<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id', 'weekday', 'start_time', 'end_time', 'room_id'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
