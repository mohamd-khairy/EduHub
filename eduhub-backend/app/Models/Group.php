<?php

namespace App\Models;

use App\Models\Scopes\RoleAccessScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;


class Group extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    public static bool $inPermission = true;

    protected $fillable = ['name', 'teacher_id', 'course_id', 'status', 'study_year_id'];

    protected $appends = ['schedule', 'label', 'value', 'students_count'];

    protected static function booted()
    {
        static::addGlobalScope(new RoleAccessScope);
    }

    public function getStudentsCountAttribute($value)
    {
        return $this->students()->count();
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

        return $schedules->map(function ($s) {
            $day = get_arabic_day_name_by_english($s->day);
            $start = Carbon::parse($s->start_time)->format('h:i');
            $end = Carbon::parse($s->end_time)->format('h:i');
            return "{$day} ({$start} - {$end})";
        })
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

    public function studyYear()
    {
        return $this->belongsTo(StudyYear::class, 'study_year_id');
    }

    public function activeStudyYear()
    {
        return $this->studyYear()->where('status', 1);
    }
}
