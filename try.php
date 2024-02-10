<?php
// Assuming $currentMonth and $currentYear are already defined
//$currentDate = new DateTime();
$currentDay = 29;//intval($currentDate->format('d'));
$currentMonth = 3;//intval($currentDate->format('m'));
$currentYear = 2023;//intval($currentDate->format('Y'));
// Get the timestamp for the first day of the current month
$firstDayOfCurrentMonth = mktime(0, 0, 0, $currentMonth, 1, $currentYear);

// Calculate the timestamp for the last day of the previous month
$lastDayOfPreviousMonth = strtotime('-1 day', $firstDayOfCurrentMonth);

// Get the number of days in the previous month
$daysInPreviousMonth = date('t', $lastDayOfPreviousMonth);

echo "Days in the previous month: $daysInPreviousMonth";
?>