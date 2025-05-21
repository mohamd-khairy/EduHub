<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'submitted_by_id', 'submitted_by_type', 'subject', 'message', 'status', 'assigned_to'
    ];

    public function submittedBy()
    {
        return $this->morphTo();
    }
}
