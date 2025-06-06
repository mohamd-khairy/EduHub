<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'phone', 'email', 'specialization', 'salary_amount'];

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function attendances()
    {
        return $this->hasMany(TeacherAttendance::class);
    }
}
