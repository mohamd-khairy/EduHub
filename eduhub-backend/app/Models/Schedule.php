<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'day',
        'start_time',
        'end_time',
        'room_id'
    ];

    protected $appends = ['label', 'value'];

    public function getLabelAttribute()
    {
        return "{$this->day} {$this->start_time} - {$this->end_time}";
    }

    public function getValueAttribute()
    {
        return $this->id;
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
