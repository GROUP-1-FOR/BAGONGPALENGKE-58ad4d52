<?php
require("config.php");

require("admin_check_login.php");

// Check if the admin has the authority to remove confirmed payments
// Add your condition here if necessary

// Perform the removal
$query = "DELETE FROM ven_payments WHERE confirmed = 1 AND archived = 1";
$result = mysqli_query($connect, $query);

if ($result) {
    echo "Confirmed payments removed successfully!";
} else {
    echo "Error removing confirmed payments: " . mysqli_error($connect);
}
