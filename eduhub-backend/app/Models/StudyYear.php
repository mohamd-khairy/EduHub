<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyYear extends Model
{
    /** @use HasFactory<\Database\Factories\StudyYearFactory> */
    use HasFactory;

    public static bool $inPermission = true;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status'
    ];

    protected $appends = ['label', 'value'];

    public function getLabelAttribute()
    {
        return $this->name;
    }

    public function getValueAttribute()
    {
        return $this->id;
    }

    public static function current()
    {
        return self::where('status', 1)->first() ?? null;
    }
}
