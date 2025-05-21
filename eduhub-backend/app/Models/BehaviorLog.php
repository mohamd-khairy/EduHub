<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BehaviorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'type', 'description', 'logged_by', 'created_at'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
