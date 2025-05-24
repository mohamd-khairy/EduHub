<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'teacher_id', 'group_id', 'schedule', 'max_students'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'course_students', 'course_id', 'student_id')
            ->withPivot('start_date', 'end_date', 'status');
    }

    public function studentEnrollments()
    {
        return $this->hasMany(CourseStudent::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
