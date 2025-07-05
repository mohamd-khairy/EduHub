<?php

use Carbon\Carbon;

if (!function_exists('get_day_name_by_date')) {

    function get_day_name_by_date($date = null)
    {
        // or in Arabic
        $dayNameArabic = [
            'Saturday' => "السبت",
            'Sunday' => "الأحد",
            'Monday' => " الإثنين",
            'Tuesday' => "الثلاثاء",
            'Wednesday' => "الأربعاء",
            'Thursday' => "الخميس",
            'Friday' => "الجمعة",
        ];

        if (is_null($date)) {
            $day = Carbon::today();
        } else {
            $day = Carbon::parse($date);
        }

        return $day->format("l"); //$dayNameArabic[$day->format("l")];
    }

    function get_arabic_day_name_by_english($day = null)
    {
        $dayNameArabic = [
            'Saturday' => "السبت",
            'Sunday' => "الأحد",
            'Monday' => " الإثنين",
            'Tuesday' => "الثلاثاء",
            'Wednesday' => "الأربعاء",
            'Thursday' => "الخميس",
            'Friday' => "الجمعة",
        ];

        return $dayNameArabic[$day];
    }

    function getModelFromType($type)
    {
        $model = app('App\\Models\\' . ucfirst($type));

        return $model;
    }
}
