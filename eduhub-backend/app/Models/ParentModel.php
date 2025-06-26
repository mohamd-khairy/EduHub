<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class ParentModel extends Authenticatable implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use HasApiTokens, HasRoles;

    protected $guard_name = 'parent';
    public static bool $inPermission = true;

    protected $table = 'parent_models';

    protected $fillable = ['name', 'phone', 'email', 'address', 'password'];

    protected static function booted()
    {
        static::created(function ($parent) {
            $parent->password = Hash::make($parent->email);
            $parent->saveQuietly(); // avoid triggering events again
            if (! $parent->hasRole('parent')) {
                $parent->assignRole('parent');
            }
        });
    }

    public function parents()
    {
        return $this->hasMany(Student::class, 'parent_id');
    }
}
