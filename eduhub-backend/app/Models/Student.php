<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'gender',
        'grade_level',
        'school_name',
        'parent_id',
        'phone',
        'email',
        'image'
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class)->withPivot('start_date', 'end_date', 'status');
    }

    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function courseEnrollments()
    {
        return $this->hasMany(CourseStudent::class);
    }
}
