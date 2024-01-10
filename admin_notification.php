<?php
// Include your database connection file here
require("config.php");

// Check if the user is logged in
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];
} else {
    header("location:admin_logout.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Page</title>
    <style>
        body {
            text-align: center;
            margin: 50px;
            background-color: #f2f2f2;
        }

        .notification-banner {
            width: 50%;
            margin: auto;
            border: 3px solid #ccc;
            padding: 20px;
            background-color: #fff;
            margin-bottom: 20px;
        }

        .notification-banner h3 {
            color: #850F16;
        }

        .notification-banner p {
            font-size: 1.2em;
            margin: 10px 0;
        }

        .back-button {
            width: 100px;
            /* Adjust the width as needed */
            margin: 20px auto;
            /* Center the button horizontally */
            padding: 10px;
            background-color: #850F16;
            color: #fff;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: block;
        }
    </style>
</head>

<body>
    <?php


    // Fetch notifications from the database
    $query = "SELECT * FROM admin_notification ORDER BY notif_date DESC";
    $result = $connect->query($query);

    // Check if there are notifications
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Check if the notification is confirmed (confirm column == 1)
            if ($row['confirm'] == 1) {
                // Display the notification banner
                echo '<div class="notification-banner">';

                // Make the title clickable and redirect to admin_confirmpay.php
                echo '<h3><a href="admin_confirmpay.php">' . $row['title'] . '</a></h3>';

                echo '<p>From: ' . $row['vendor_name'] . ' | Vendor ID: ' . $row['vendor_userid'] . '</p>';
                echo '<p>Transaction ID: ' . $row['transaction_id'] . '</p>';
                echo '<p>MOP: ' . $row['mop'] . '</p>';
                echo '</div>';
            } else if ($row['message'] == 1) {
                // Display the notification banner
                echo '<div class="notification-banner">';

                // Make the title clickable and redirect to admin_confirmpay.php
                echo '<h3><a href="admin_messages_preview.php">' . $row['title'] . '</a></h3>';

                echo '<p>From: ' . $row['vendor_name'] . ' | Vendor ID: ' . $row['vendor_userid'] . '</p>';
                echo '</div>';
            } else if ($row['edit'] == 1) {
                // Display the notification banner
                echo '<div class="notification-banner">';

                // Make the title clickable and redirect to admin_confirmpay.php
                echo '<h3><a href="admin_vendor_manage_accounts.php">' . $row['title'] . '</a></h3>';

                echo '<p> Vendor ID: ' . $row['vendor_userid'] . '</p>';
                echo '</div>';
            }
        }

        // Add a back button
        echo '<a href="admin_index.php" class="back-button">Home</a>';
    } else {
        // Display a message if there are no notifications
        echo '<p>No notifications available.</p>';
        // Add a back button even if there are no notifications
        echo '<a href="admin_index.php" class="back-button">Home</a>';
    }

    // Close the database connection
    $connect->close();


    ?>
</body>

</html>