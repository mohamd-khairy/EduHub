<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Notification extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    public static bool $inPermission = true;

    protected $fillable = [
        'id',
        'type',
        'data',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }
}
