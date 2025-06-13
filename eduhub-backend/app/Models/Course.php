<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Course extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\GroupFactory> */
    use HasFactory;
     use \OwenIt\Auditing\Auditable;   
 public static bool $inPermission = true;

    protected $fillable = ['name', 'description'];

    public function groups()
    {
        return $this->hasMany(Group::class, 'course_id');
    }
}
