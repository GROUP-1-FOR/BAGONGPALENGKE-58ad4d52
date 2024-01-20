<?php
require("config.php");

// Check if the user is logged in as an admin
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
} else {
    header("location:admin_logout.php");
}

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
