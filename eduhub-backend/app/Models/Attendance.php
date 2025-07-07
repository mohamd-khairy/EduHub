<?php

namespace App\Models;

use App\Models\Scopes\RoleAccessScope;
use App\Traits\HasStudyYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Attendance extends Model implements Auditable
{
    use HasFactory, HasStudyYear;
    use \OwenIt\Auditing\Auditable;
    public static bool $inPermission = true;

    protected $fillable = ['student_id', 'group_id', 'schedule_id', 'date', 'status', 'note', 'study_year_id'];

    protected static function booted()
    {
        static::addGlobalScope(new RoleAccessScope);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
