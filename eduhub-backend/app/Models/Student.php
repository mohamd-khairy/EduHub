<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

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

    protected $with = [
        'parent',
        'attendance',
        'courses',
        'payments',
        'evaluations',
        'courseEnrollments'
    ];


    public function getModelRelations($model)
    {
        $relations = collect(get_class_methods($model))
            ->filter(function ($method) use ($model) {
                if ((new \ReflectionMethod($model, $method))->getNumberOfParameters() > 0) {
                    return false;
                }

                try {
                    $return = $model->$method();
                    return $return instanceof \Illuminate\Database\Eloquent\Relations\Relation;
                } catch (\Throwable $e) {
                    return false;
                }
            })
            ->values()
            ->toArray();

        // $relations = implode(',', $relations);

        return $relations;
    }

    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    public function attendance()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_students')->withPivot('start_date', 'end_date', 'status');
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function courseEnrollments()
    {
        return $this->hasMany(CourseStudent::class);
    }
}
