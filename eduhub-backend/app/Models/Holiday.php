<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Holiday extends Model implements Auditable
{
use HasFactory;
 use \OwenIt\Auditing\Auditable;   
 public static bool $inPermission = true;


    protected $fillable = [
        'title', 'start_date', 'end_date', 'description'
    ];
}
