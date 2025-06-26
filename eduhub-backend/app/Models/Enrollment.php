<?php

namespace App\Models;

use App\Models\Scopes\RoleAccessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Enrollment extends Model implements Auditable
{
use HasFactory;
 use \OwenIt\Auditing\Auditable;   
 public static bool $inPermission = true;


    protected $fillable = [
        'student_id',
        'group_id',
        'start_date',
        'end_date',
        'status',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new RoleAccessScope);
    }
    
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
