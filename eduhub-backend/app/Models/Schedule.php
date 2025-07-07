<?php

namespace App\Models;

use App\Models\Scopes\RoleAccessScope;
use App\Traits\HasStudyYear;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Schedule extends Model implements Auditable
{
    use HasFactory, HasStudyYear;
    use \OwenIt\Auditing\Auditable;
    public static bool $inPermission = true;


    protected $fillable = [
        'group_id',
        'day',
        'start_time',
        'end_time',
        'room_id',
        'study_year_id'
    ];

    protected $appends = ['label', 'value'];

    protected static function booted()
    {
        static::addGlobalScope(new RoleAccessScope);
    }

    public function getLabelAttribute()
    {
        $day = get_arabic_day_name_by_english($this->day);
        $start = Carbon::parse($this->start_time)->format('h:i');
        $end = Carbon::parse($this->end_time)->format('h:i');
        return "{$day} ({$start} - {$end})";
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
