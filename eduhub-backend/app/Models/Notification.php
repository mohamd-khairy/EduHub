<?php

namespace App\Models;

use App\Models\Scopes\RoleAccessScope;
use App\Traits\HasStudyYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Notification extends Model implements Auditable
{
    use HasFactory, HasStudyYear;
    use \OwenIt\Auditing\Auditable;
    public static bool $inPermission = true;

    protected $fillable = [
        'id',
        'type',
        'data',
        'read_at',
        'study_year_id'
    ];

    protected $casts = [
        'data' => 'array',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new RoleAccessScope);
    }

    public function notifiable()
    {
        return $this->morphTo();
    }
}
