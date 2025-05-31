<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'title',
        'date',
        'time',
        'total_marks'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }
}
