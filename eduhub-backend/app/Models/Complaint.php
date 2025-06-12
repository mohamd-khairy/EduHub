<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Complaint extends Model implements Auditable
{
use HasFactory;
use \OwenIt\Auditing\Auditable;


    protected $fillable = [
        'submitted_by_id', 'submitted_by_type', 'subject', 'message', 'status', 'assigned_to'
    ];

    public function submittedBy()
    {
        return $this->morphTo();
    }
}
