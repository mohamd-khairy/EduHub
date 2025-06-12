<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Payment extends Model implements Auditable
{
use HasFactory;
use \OwenIt\Auditing\Auditable;


    protected $fillable = ['student_id', 'amount', 'payment_date', 'method', 'status', 'note'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
