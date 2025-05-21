<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'teacher_id', 'group_name', 'schedule', 'max_students'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class)->withPivot('start_date', 'end_date', 'status');
    }

    public function studentEnrollments()
    {
        return $this->hasMany(CourseStudent::class);
    }
}
