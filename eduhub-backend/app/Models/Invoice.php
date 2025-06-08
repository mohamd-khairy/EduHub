<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Invoice Model
class Invoice extends Model
{
    use HasFactory;

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
