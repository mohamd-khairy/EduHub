<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


// Invoice Model
class Invoice extends Model implements Auditable
{
use HasFactory;
 use \OwenIt\Auditing\Auditable;   
 public static bool $inPermission = true;


    protected $fillable = [
        'student_id',
        'invoice_number',
        'total',
        'due_date',
        'status',
        'note',
        'payment_id'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
