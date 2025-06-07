<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'teacher_id', 'course_id', 'max_students'];

    protected $appends = ['schedule'];

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
}
