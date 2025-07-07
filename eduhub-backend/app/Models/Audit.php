<?php

namespace App\Models;

use App\Models\Scopes\RoleAccessScope;
use App\Traits\HasStudyYear;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model implements \OwenIt\Auditing\Contracts\Audit
{
    use \OwenIt\Auditing\Audit, HasStudyYear;

    public static bool $inPermission = true;

    /**
     * @var string[]
     */
    protected $guarded = [];
    /**
     * Is globally auditing disabled?
     *
     * @var bool
     */
    public static $auditingGloballyDisabled = true;

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        'tags' => 'json',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new RoleAccessScope);

        static::creating(function ($model) {
            $model->user_id = auth()->id() ?? null;
            $model->user_type = auth()->user() ? get_class(auth()->user()) : null;
        });

        static::updating(function ($model) {
            $model->user_id = auth()->id() ?? null;
            $model->user_type = auth()->user() ? get_class(auth()->user()) : null;
        });
    }

    public function getSerializedDate($date): string
    {
        return $this->serializeDate($date);
    }
}
