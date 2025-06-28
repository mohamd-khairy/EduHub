<?php

namespace App\Models;

use App\Models\Scopes\RoleAccessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Teacher extends Authenticatable implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use HasApiTokens, HasRoles;

    protected $guard_name = 'teacher';

    public static bool $inPermission = true;

    protected $fillable = ['name', 'phone', 'email', 'specialization', 'salary_amount', 'password'];

    protected static function booted()
    {
        static::addGlobalScope(new RoleAccessScope);

        static::created(function ($teacher) {
            if (! $teacher->password) {
                $teacher->password = Hash::make($teacher->email);
                $teacher->saveQuietly(); // avoid triggering events again
            }
            if (! $teacher->hasRole('teacher')) {
                $teacher->assignRole('teacher');
            }
        });
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }
}
