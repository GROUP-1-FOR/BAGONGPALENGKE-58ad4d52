<?php
// Function to calculate daily market rent payment
function calculateDailyRent($stallType)
{
    // Define the daily rates for each stall type
    $dailyRateA = 100;
    $dailyRateB = 150;

    // Check the stall type and calculate the daily payment
    if ($stallType === 'A') {
        return $dailyRateA;
    } elseif ($stallType === 'B') {
        return $dailyRateB;
    } else {
        // Handle invalid stall type (optional)
        return 0;
    }
}

// Example vendor data
$vendors = [
    'Vendor1' => 'A',
    'Vendor2' => 'B',
];

// Get the current month and year
$month = date('m');
$year = date('Y');

// Get the number of days in the current month
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

// Create a table for the calendar
echo '<table border="1">';
echo '<tr><th>Day</th>';

// Display vendor names
foreach ($vendors as $vendor => $stallType) {
    echo '<th>' . $vendor . '</th>';
}
echo '</tr>';

// Loop through each day of the month
for ($day = 1; $day <= $daysInMonth; $day++) {
    echo '<tr>';
    echo '<td>' . $day . '</td>';

    // Calculate and display daily payments for each vendor
    foreach ($vendors as $vendor => $stallType) {
        $dailyPayment = calculateDailyRent($stallType);
        echo '<td>$' . $dailyPayment . '</td>';
    }

    echo '</tr>';
}

echo '</table>';
