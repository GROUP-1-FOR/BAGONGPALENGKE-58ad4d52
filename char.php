<?php
// Current date
$currentDate = new DateTime();

// Target date (November 30, 2023)
$targetDate = new DateTime('2023-11-30');

// Calculate the difference in days
$interval = $currentDate->diff($targetDate);
$totalDays = $interval->format('%a');

// Output the result
echo "Total days between the current date and November 30, 2023: $totalDays days";
