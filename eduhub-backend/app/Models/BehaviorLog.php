<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class BehaviorLog extends Model implements Auditable
{
use HasFactory;
 use \OwenIt\Auditing\Auditable;   
 public static bool $inPermission = true;


    protected $fillable = [
        'student_id', 'type', 'description', 'logged_by', 'created_at'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
