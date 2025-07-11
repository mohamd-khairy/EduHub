<?php

namespace App\Models\Scopes;

use App\Models\StudyYear;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class RoleAccessScope implements Scope
{
    public $studyYear;

    public function apply(Builder $builder, Model $model) //: void
    {
        $user = Auth::user();

        // if (!$user || $user && $user->hasRole('admin')) {
        //     return;
        // }

        $modelName = class_basename($model);

        match ($modelName) {
            'Student' => $this->applyStudentFilter($builder, $user),
            'Group' => $this->applyGroupFilter($builder, $user),
            'Payment' => $this->applyPaymentFilter($builder, $user),
            'Enrollment' => $this->applyEnrollmentFilter($builder, $user),
            'Exam' => $this->applyExamFilter($builder, $user),
            'ExamResult' => $this->applyExamResultFilter($builder, $user),
            'ParentModel' => $this->applyParentModelFilter($builder, $user),
            'Teacher' => $this->applyTeacherFilter($builder, $user),
            'Attendance' => $this->applyAttendanceFilter($builder, $user),
            'Course' => $this->applyCourseFilter($builder, $user),
            'chat' => $this->applyChatFilter($builder, $user),
            default => null
        };

        $this->applyStudyYearFilter($builder, $model);
    }

    private function applyStudyYearFilter(Builder $builder, Model $model)
    {
        $table = $model->getTable();

        // Check if the model has study_year_id column (optional for safety)
        if (Schema::hasColumn($table, 'study_year_id')) {
            $activeStudyYearId = cache()->remember('active_study_year_id', 60, function () {
                return StudyYear::where('status', 1)->value('id');
            });

            $builder->where("{$table}.study_year_id", $activeStudyYearId);
        }
    }

    private function applyGroupFilter(Builder $builder, $user)
    {
        if ($user && $user->hasRole('teacher')) {
            $builder->where('teacher_id', $user->id);
        }

        if ($user && $user->hasRole('student')) {
            $builder->whereHas('enrollments', fn($q) => $q->where('student_id', $user->id));
        }

        if ($user && $user->hasRole('parent')) {
            $builder->whereHas('enrollments.student', fn($q) => $q->where('parent_id', $user->id));
        }
    }

    private function applyEnrollmentFilter(Builder $builder, $user)
    {
        if ($user && $user->hasRole('teacher')) {
            $builder->whereHas('group', fn($q) => $q->where('teacher_id', $user->id));
        }

        if ($user && $user->hasRole('student')) {
            $builder->where('student_id', $user->id);
        }

        if ($user && $user->hasRole('parent')) {
            $builder->whereHas('student', fn($q) => $q->where('parent_id', $user->id));
        }
    }

    private function applyStudentFilter(Builder $builder, $user)
    {
        if ($user && $user->hasRole('teacher')) {
            $builder->whereHas('enrollments.group', fn($q) => $q->where('teacher_id', $user->id));
        }

        if ($user && $user->hasRole('student')) {
            $builder->where('students.id', $user->id);
        }

        if ($user && $user->hasRole('parent')) {
            $builder->where('parent_id', $user->id);
        }
    }

    private function applyPaymentFilter(Builder $builder, $user)
    {
        if ($user && $user->hasRole('teacher')) {
            $builder->whereHas('student.enrollments.group', fn($q) => $q->where('teacher_id', $user->id));
        }

        if ($user && $user->hasRole('student')) {
            $builder->where('student_id', $user->id);
        }

        if ($user && $user->hasRole('parent')) {
            $builder->whereHas('student', fn($q) => $q->where('parent_id', $user->id));
        }
    }


    private function applyExamFilter(Builder $builder, $user)
    {
        if ($user && $user->hasRole('teacher')) {
            $builder->whereHas('group', fn($q) => $q->where('teacher_id', $user->id));
        }

        if ($user && $user->hasRole('student')) {
            $builder->whereHas('results', fn($q) => $q->where('student_id', $user->id));
        }

        if ($user && $user->hasRole('parent')) {
            $builder->whereHas('results.student', fn($q) => $q->where('parent_id', $user->id));
        }
    }

    private function applyExamResultFilter(Builder $builder, $user)
    {
        if ($user && $user->hasRole('teacher')) {
            $builder->whereHas('exam.group', fn($q) => $q->where('teacher_id', $user->id));
        }

        if ($user && $user->hasRole('student')) {
            $builder->where('student_id', $user->id);
        }

        if ($user && $user->hasRole('parent')) {
            $builder->whereHas('student', fn($q) => $q->where('parent_id', $user->id));
        }
    }

    private function applyAttendanceFilter(Builder $builder, $user)
    {
        if ($user && $user->hasRole('teacher')) {
            $builder->whereHas('student.enrollments.group', fn($q) => $q->where('teacher_id', $user->id));
        }

        if ($user && $user->hasRole('student')) {
            $builder->whereHas('student', fn($q) => $q->where('id', $user->id));
        }

        if ($user && $user->hasRole('parent')) {
            $builder->whereHas('student', fn($q) => $q->where('parent_id', $user->id));
        }
    }

    private function applyParentModelFilter(Builder $builder, $user)
    {
        if ($user && $user->hasRole('parent')) {
            $builder->where('id', $user->id);
        }

        if ($user && $user->hasRole('student')) {
            $builder->whereHas('students', fn($q) => $q->where('id', $user->id));
        }

        if ($user && $user->hasRole('teacher')) {
            $builder->whereHas('students.enrollments.group', fn($q) => $q->where('teacher_id', $user->id));
        }
    }

    private function applyTeacherFilter(Builder $builder, $user)
    {
        if ($user && $user->hasRole('teacher')) {
            $builder->where('id', $user->id);
        }

        if ($user && $user->hasRole('student')) {
            $builder->whereHas('groups.enrollments', fn($q) => $q->where('student_id', $user->id));
        }

        if ($user && $user->hasRole('parent')) {
            $builder->whereHas('groups.enrollments.student', fn($q) => $q->where('parent_id', $user->id));
        }
    }

    private function applyCourseFilter(Builder $builder, $user)
    {
        if ($user && $user->hasRole('teacher')) {
            $builder->whereHas('groups', fn($q) => $q->where('teacher_id', $user->id));
        }

        if ($user && $user->hasRole('student')) {
            $builder->whereHas('groups.enrollments', fn($q) => $q->where('student_id', $user->id));
        }

        if ($user && $user->hasRole('parent')) {
            $builder->whereHas('groups.enrollments.student', fn($q) => $q->where('parent_id', $user->id));
        }
    }

    private function applyChatFilter(Builder $builder, $user)
    {
        if ($user) {
            $builder->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            });
        }
    }
}
