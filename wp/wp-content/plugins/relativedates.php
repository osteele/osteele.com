<?php
/*
Plugin Name: Relative Dates
Version: 1.01
Plugin URI: http://justinblanton.com/projects/relativedates/
Description: Displays the date of your posts relative to the current date and time.
Author: Justin Blanton
Author URI: http://justinblanton.com
*/
	
function relativeDate($posted_date, $relprefix='Posted on ', $prefix='Posted ') {
    $tz = -3;    // change this if your web server and weblog are in different timezones
                // see project page for instructions on how to do this
    
    $month = substr($posted_date,4,2);
    
    if ($month == "02") { // february
    	// check for leap year
    	$leapYear = isLeapYear(substr($posted_date,0,4));
    	if ($leapYear) $month_in_seconds = 2505600; // leap year
    	else $month_in_seconds = 2419200;
    }
    else { // not february
    // check to see if the month has 30/31 days in it
    	if ($month == "04" or 
    		$month == "06" or 
    		$month == "09" or 
    		$month == "11")
    		$month_in_seconds = 2592000; // 30 day month
    	else $month_in_seconds = 2678400; // 31 day month;
    }
  
/* 
some parts of this implementation borrowed from:
http://maniacalrage.net/archives/2004/02/relativedatesusing/ 
*/
  
    $in_seconds = strtotime(substr($posted_date,0,8).' '.
                  substr($posted_date,8,2).':'.
                  substr($posted_date,10,2).':'.
                  substr($posted_date,12,2));
    $diff = time() - ($in_seconds + ($tz*3600));
    $months = floor($diff/$month_in_seconds);
    $diff -= $months*2419200;
    $weeks = floor($diff/604800);
    $diff -= $weeks*604800;
    $days = floor($diff/86400);
    $diff -= $days*86400;
    $hours = floor($diff/3600);
    $diff -= $hours*3600;
    $minutes = floor($diff/60);
    $diff -= $minutes*60;
    $seconds = $diff;
	
    if ($months>0) {
        // over a month old, just show date ("Month, Day Year")
        echo $relprefix; the_time('F jS, Y');
    } else {
        if ($weeks>0) {
            // weeks and days
			$relative_date = twocardinals($weeks, 'week', $days, 'day');
        } elseif ($days>0) {
			$relative_date = twocardinals($days, 'day', $hours, 'hour');
        } elseif ($hours>0) {
			$relative_date = twocardinals($hours, 'hour', $minutes, 'minute');
        } elseif ($minutes>0) {
            // minutes only
            $relative_date .= ($relative_date?', ':'').$minutes.' minute'.($minutes>1?'s':'');
        } else {
            // seconds only
            $relative_date .= ($relative_date?', ':'').$seconds.' second'.($seconds>1?'s':'');
        }
        
        // show relative date and add proper verbiage
    	echo $prefix.$relative_date.' ago';
    }
    
}

function isLeapYear($year) {
        return $year % 4 == 0 && ($year % 400 == 0 || $year % 100 != 0);
}

function formatCardinalUnits($n, $unit, $prefix='') {
	$s = '';
	if ($n) {
		$s .= $prefix;
		if ($n == 1) {
			$s .= 'one '.$unit;
		} else {
			$s .= $n.' '.$unit.'s';
		}
	}
	return $s;
}

function twocardinals($a, $unita, $b, $unitb) {
	return formatCardinalUnits($a, $unita) . formatCardinalUnits($b, $unitb, ' and ');
}
?>