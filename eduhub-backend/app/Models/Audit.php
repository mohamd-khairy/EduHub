<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model implements \OwenIt\Auditing\Contracts\Audit
{
    use \OwenIt\Auditing\Audit;

    /**
     * @var string[]
     */

    protected $guarded = ['*'];
    /**
     * Is globally auditing disabled?
     *
     * @var bool
     */
    public static $auditingGloballyDisabled = false;

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        'tags' => 'json',
    ];

    public function getSerializedDate($date): string
    {
        return $this->serializeDate($date);
    }
}
