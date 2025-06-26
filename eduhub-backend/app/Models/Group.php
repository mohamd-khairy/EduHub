<?php

namespace App\Models;

use App\Models\Scopes\RoleAccessScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Group extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    public static bool $inPermission = true;

    protected $fillable = ['name', 'teacher_id', 'course_id', 'max_students'];

    protected $appends = ['schedule', 'label', 'value'];

    protected static function booted()
    {
        static::addGlobalScope(new RoleAccessScope);
    }

    public function getLabelAttribute()
    {
        return $this->name;
    }

    public function getValueAttribute()
    {
        return $this->id;
    }

    public function getScheduleAttribute()
    {
        // Get related schedules
        $schedules = $this->schedules()->get(); // Or use $this->schedules if it's eager-loaded

        return $schedules->map(fn($s) => "{$s->day} {$s->start_time} ")
            ->implode(', ');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'enrollments', 'group_id', 'student_id')
            ->withPivot('start_date', 'end_date', 'status');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function currentSchedules()
    {
        return $this->schedules()->where('day', get_day_name_by_date(request('date')));
    }
}
