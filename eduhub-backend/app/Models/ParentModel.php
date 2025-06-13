<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class ParentModel extends Model implements Auditable
{
use HasFactory;
 use \OwenIt\Auditing\Auditable;   
 public static bool $inPermission = true;


    protected $table = 'parent_models';

    protected $fillable = ['name', 'phone', 'email', 'address'];

    public function students()
    {
        return $this->hasMany(Student::class, 'parent_id');
    }
}
