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

        return $dayNameArabic[$day->format("l")];
    }
}
