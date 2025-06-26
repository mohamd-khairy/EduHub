<?php

namespace App\Models;

use App\Models\Scopes\RoleAccessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Payment extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    public static bool $inPermission = true;


    protected $fillable = ['student_id', 'amount', 'payment_date', 'method', 'status', 'note'];

    protected static function booted()
    {
        static::addGlobalScope(new RoleAccessScope);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
