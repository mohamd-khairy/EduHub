<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'group_id',
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
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
