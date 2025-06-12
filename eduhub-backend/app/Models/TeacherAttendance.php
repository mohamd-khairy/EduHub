<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class TeacherAttendance extends Model implements Auditable
{
use HasFactory;
use \OwenIt\Auditing\Auditable;


    protected $fillable = ['teacher_id', 'group_id', 'date', 'status', 'note'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
