<?php

namespace App\Models;

use App\Models\Scopes\RoleAccessScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Student extends Authenticatable implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use HasApiTokens, HasRoles;

    protected $guard_name = 'student';
    public static bool $inPermission = true;

    public static array $customPermissions = [
        'read-student-parent',
        'read-student-group',
        'create-student-group',
        'delete-student-group',
        'update-student-group-status',
        'read-student-exam',
        'read-student-attendance',
        'read-student-payment',
    ];

    protected $fillable = [
        'name',
        'gender',
        'grade_level',
        'school_name',
        'parent_id',
        'phone',
        'email',
        'image',
        'password'
    ];

    protected $appends = ['attendance_status'];

    protected static function booted()
    {
        static::addGlobalScope(new RoleAccessScope);

        static::created(function ($student) {
            if (! $student->password) {
                $student->password = Hash::make($student->email);
                $student->saveQuietly(); // avoid triggering events again
            }
            if (! $student->hasRole('student')) {
                $student->assignRole('student');
            }
        });
    }


    public function todayAttendance()
    {
        return $this->attendances()->whereDate('date', Carbon::parse(request('date', Carbon::today()))); //->exists();
    }

    public function getAttendanceStatusAttribute()
    {
        $scheduleId = request('schedule_id'); // Be cautious — assumes request context
        if (!$scheduleId) return null;

        return $this->todayAttendance()
            ->firstWhere('schedule_id', $scheduleId)
            ->status ?? '-';
    }

    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'enrollments',  'student_id', 'group_id')
            ->withPivot('start_date', 'end_date', 'status');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }
}
