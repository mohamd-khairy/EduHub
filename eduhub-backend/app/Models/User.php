<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements Auditable
{
    use HasFactory, Notifiable, HasRoles;
    use HasApiTokens;
    use \OwenIt\Auditing\Auditable;

    public static bool $inPermission = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'image'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['display_roles', 'role'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function setImageAttribute($value)
    {
        if ($value && strpos($value, 'http') === false) {
            $image = $value->store('images', 'public');
            $this->attributes['image'] = url('/storage/' . $image);
        } else {
            $this->attributes['image'] = $value;
        }
    }

    public function getRoleAttribute()
    {
        return $this->roles()->first();
    }

    public function getDisplayRolesAttribute()
    {
        return $this->roles->pluck('name')->implode(', ');
    }

    public function isAdmin()
    {
        return in_array('admin', $this->roles->pluck('name')->toArray());
    }
}
