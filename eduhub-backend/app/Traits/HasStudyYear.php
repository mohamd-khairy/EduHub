<?php

namespace App\Traits;

use App\Models\StudyYear;

trait HasStudyYear
{
    public static function bootHasStudyYear()
    {
        static::creating(function ($model) {
            // Only set if not already manually set
            if (empty($model->study_year_id)) {
                $model->study_year_id = StudyYear::where('status', 1)->value('id');
            }
        });
    }
}
