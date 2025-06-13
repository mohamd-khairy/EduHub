<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class File extends Model implements Auditable
{
use HasFactory;
 use \OwenIt\Auditing\Auditable;   
 public static bool $inPermission = true;


    protected $fillable = [
        'title', 'file_path', 'type', 'uploaded_by_id', 'uploaded_by_type'
    ];

    public function uploader()
    {
        return $this->morphTo();
    }
}
