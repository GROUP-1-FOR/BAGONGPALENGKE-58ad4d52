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
            width: 100px; /* Adjust the width as needed */
            margin: 20px auto; /* Center the button horizontally */
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
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $userid = $_SESSION["userid"];

    // Fetch notifications using prepared statement
    $sqlNotifications = "SELECT * FROM vendor_notification WHERE vendor_userid = ? ORDER BY notif_date DESC";
    $stmtNotifications = $connect->prepare($sqlNotifications);
    $stmtNotifications->bind_param('s', $userid); // Use 's' for VARCHAR
    $stmtNotifications->execute();
    $resultNotifications = $stmtNotifications->get_result();

    // Check if there are notifications
    if ($resultNotifications->num_rows > 0) {
        while ($row = $resultNotifications->fetch_assoc()) {
            // Display the notification banner
            echo '<div class="notification-banner">';
            
            // Make the title clickable and redirect to the appropriate page based on the notification type
            if ($row['confirm'] == 1) {
                echo '<h3><a href="vendor_transaction_history.php">' . $row['title'] . '</a></h3>';
                echo '<p>Transaction ID: ' . $row['transaction_id'] . '</p>';
                echo '<p>MOP: ' . $row['mop'] . '</p>';
            } elseif ($row['message'] == 1) {
                echo '<h3><a href="vendor_messages.php">' . $row['title'] . '</a></h3>';
            } elseif ($row['announcement'] == 1) {
                echo '<h3><a href="vendor_index.php">' . $row['title'] . '</a></h3>';
            } elseif ($row['edit'] == 1) {
                echo '<h3><a href="vendor_index.php">' . $row['title'] . '</a></h3>';
            }
            
            echo '<p>By: ' . $row['admin_name'] . '</p>';
            
            echo '</div>';
        }
        
        // Add a back button
        echo '<a href="vendor_index.php" class="back-button">Home</a>';
    } else {
        // Display a message if there are no notifications
        echo '<p>No notifications available.</p>';
        // Add a back button even if there are no notifications
        echo '<a href="vendor_index.php" class="back-button">Home</a>';
    }

    // Close the prepared statement
    $stmtNotifications->close();
    // Close the database connection
    $connect->close();
} else {
    // Redirect the user to the login page or display a message
    header("Location: login.php");
    exit();
}
?>
</body>

</html>
