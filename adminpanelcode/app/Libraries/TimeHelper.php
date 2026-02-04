<?php

namespace App\Libraries;

use DateTime;

class TimeHelper {
    
    public static function calculateTimeSpan($postTime, $flag = false){
        if ($postTime != '') {
            $seconds = time() - $postTime;
            $year = floor($seconds / 31556926);
            $months = floor($seconds / 2629743);
            $week = floor($seconds / 604800);
            $day = floor($seconds / 86400);
            $hours = floor($seconds / 3600);
            $mins = floor(($seconds - ($hours * 3600)) / 60);
            $secs = floor($seconds % 60);
            
            if ($seconds < 60) $time = $secs . " sec ago";
            else if ($seconds < 3600) $time = ($mins == 1) ? $mins . " min ago" : $mins . " mins ago";
            else if ($seconds < 86400) $time = ($hours == 1) ? $hours . " hour ago" : $hours . " hours ago";
            else if ($seconds < 604800) $time = ($day == 1) ? $day . " day ago" : $day . " days ago";
            else if ($seconds < 2629743) $time = ($week == 1) ? $week . " week ago" : $week . " weeks ago";
            else if ($seconds < 31556926) $time = ($months == 1) ? $months . " month ago" : $months . " months ago";
            else $time = ($year == 1) ? $year . " year ago" : $year . " years ago";
            
            if ($flag && $day > 1) {
                $time = date('d-m-Y', $postTime);
            }
            
            return $time;
        } else {
            return 'not available';
        }
    }

    public static function calculateEndDays($date, $endDay){
        try {
            $dateObj = new DateTime($date);
            $dateObj->modify("+$endDay days");
            return $dateObj->format("Y-m-d");
        } catch (\Exception $e) {
            return "Invalid date format";
        }
    }
}
