<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'file_path', 'type', 'uploaded_by_id', 'uploaded_by_type'
    ];

    public function uploader()
    {
        return $this->morphTo();
    }
}
