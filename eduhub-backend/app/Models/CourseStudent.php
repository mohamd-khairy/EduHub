<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseStudent extends Model
{
    use HasFactory;

    protected $table = 'course_student'; // اسم الجدول الوسيط

    protected $fillable = [
        'student_id',
        'course_id',
        'start_date',
        'end_date',
        'status',
    ];

    // العلاقة مع الطالب
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // العلاقة مع الدورة
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
