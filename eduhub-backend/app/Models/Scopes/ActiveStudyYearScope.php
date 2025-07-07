<?php

namespace App\Models\Scopes;

use App\Models\StudyYear;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ActiveStudyYearScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */

    public function apply(Builder $builder, Model $model)
    {
        // Get the active study year ID once
        $activeYearId = cache()->remember('active_study_year_id', 60, function () {
            return StudyYear::where('status', 1)->value('id');
        });

        // Only apply scope if the column exists
        if (schema()->hasColumn($model->getTable(), 'study_year_id')) {
            $builder->where($model->getTable() . '.study_year_id', $activeYearId);
        }
    }

}
