<?php

namespace App\Models;

use App\Models\Scopes\RoleAccessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Exam extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    public static bool $inPermission = true;

    protected $fillable = [
        'group_id',
        'title',
        'date',
        'time',
        'total_marks'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new RoleAccessScope);
    }
    
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }
}
