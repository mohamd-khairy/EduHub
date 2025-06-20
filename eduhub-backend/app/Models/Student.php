<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
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
        'image'
    ];

    protected $appends = ['attendance_status'];

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
}
