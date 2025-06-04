<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
    use HasFactory;

    protected $table = 'parent_models';

    protected $fillable = ['name', 'phone', 'email', 'address'];

    public function student()
    {
        return $this->hasOne(Student::class, 'parent_id');
    }
}
