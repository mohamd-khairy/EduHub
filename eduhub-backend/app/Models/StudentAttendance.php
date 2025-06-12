<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class StudentAttendance extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\StudentAttendanceFactory> */
use HasFactory;
use \OwenIt\Auditing\Auditable;


    protected $fillable = ['student_id', 'group_id', 'date', 'status', 'note'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
