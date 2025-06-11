<?php

use Carbon\Carbon;

if (!function_exists('get_today_day_name')) {

    function get_today_day_name()
    {
        $today = Carbon::today();
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

        return $dayNameArabic[$today->format("l")];
    }
}
