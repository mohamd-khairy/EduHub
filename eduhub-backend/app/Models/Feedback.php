<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'submitted_by_id', 'submitted_by_type', 'target_type', 'target_id', 'rating', 'comment', 'submitted_at'
    ];

    public function submittedBy()
    {
        return $this->morphTo();
    }

    public function target()
    {
        return $this->morphTo();
    }
}
