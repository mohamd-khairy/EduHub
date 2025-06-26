<?php

namespace App\Models;

use App\Models\Scopes\RoleAccessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class ExamResult extends Model implements Auditable
{
use HasFactory;
 use \OwenIt\Auditing\Auditable;   
 public static bool $inPermission = true;


    protected $fillable = [
        'student_id', 'exam_id', 'score'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new RoleAccessScope);
    }
    
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
