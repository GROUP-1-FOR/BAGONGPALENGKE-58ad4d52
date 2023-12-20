<?php
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    // Include database connection or functions
    // Example: include('db_connection.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get values from the form
        $admin_name = $_POST['admin_name'];
        $recipient = $_POST['recipient'];
        $stall_number = $_POST['stall_number'];
        $admin_reply = $_POST['admin_reply'];

        // Fetch vendor_userid based on vendor_name and vendor_stall_number
        $vendor_userid_query = "SELECT vendor_userid FROM vendor_sign_in WHERE vendor_name = '$recipient' AND vendor_stall_number = '$stall_number' LIMIT 1";
        $vendor_userid_result = $connect->query($vendor_userid_query);

        if ($vendor_userid_result->num_rows > 0) {
            $vendor_userid_row = $vendor_userid_result->fetch_assoc();
            $vendor_userid = $vendor_userid_row['vendor_userid'];

            // Insert admin reply into admin_messages table with vendor details
            $insert_query = "INSERT INTO admin_messages (vendor_name, vendor_stall_number, vendor_userid, admin_name, admin_reply, admin_timestamp) VALUES ('$recipient', '$stall_number', '$vendor_userid', '$admin_name', '$admin_reply', NOW())";

            // Execute the query and handle errors
            $insert_result = $connect->query($insert_query);
            if (!$insert_result) {
                die("Error executing the query: " . $connect->error);
            }

             // Insert into vendor_notification table
             $notifTitle = "You have a Message!";
             $messageValue = 1; // Set the confirm value to 1
             $messageDate = date('Y-m-d H:i:s');
 
             $sqlInsertNotification = "INSERT INTO vendor_notification (vendor_userid, title, message, notif_date, admin_name) VALUES (?, ?, ?, ?, ?)";
             $stmtInsertNotification = $connect->prepare($sqlInsertNotification);
             $stmtInsertNotification->bind_param('ssiss', $vendor_userid, $notifTitle, $messageValue, $messageDate, $admin_name);
             $stmtInsertNotification->execute();

            // Redirect back to the messages page
            header("location: admin_messages.php?vendor_userid=$vendor_userid&vendor_name=$recipient&vendor_stall_number=$stall_number");
        } else {
            // Handle the case where vendor_userid is not found
            die("Error: Vendor userid not found.");
        }
    } else {
        // Redirect to the home page if accessed without a POST request
        header("location:admin_messages_preview.php");
    }
} else {
    header("location:admin_logout.php");}
?>