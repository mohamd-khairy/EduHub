<?php

namespace App\Models;

use App\Models\Scopes\RoleAccessScope;
use App\Traits\HasStudyYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class ParentModel extends Authenticatable implements Auditable
{
    use HasFactory, HasStudyYear, HasApiTokens, HasRoles;
    use \OwenIt\Auditing\Auditable;

    protected $guard_name = 'parent';
    public static bool $inPermission = true;

    protected $table = 'parent_models';

    protected $fillable = ['name', 'phone', 'email', 'address', 'password', 'study_year_id'];

    protected static function booted()
    {
        static::addGlobalScope(new RoleAccessScope);

        static::created(function ($parent) {
            if (!$parent->password) {
                $parent->password = Hash::make($parent->email);
                $parent->saveQuietly(); // avoid triggering events again
            }
            if (!$parent->hasRole('parent')) {
                $parent->assignRole('parent');
            }
        });
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'parent_id');
    }
}
