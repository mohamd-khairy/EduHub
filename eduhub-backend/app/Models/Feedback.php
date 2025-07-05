<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Feedback extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    public static bool $inPermission = true;


    protected $fillable = [
        'submitted_by_id',
        'submitted_by_type',
        'target_type',
        'target_id',
        'rating',
        'comment',
        'submitted_at'
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
