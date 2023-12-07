<?php
require("config.php");
session_start();

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

        // Insert admin reply into admin_messages table with vendor details
        $insert_query = "INSERT INTO admin_messages (vendor_name, vendor_stall_number, admin_name, admin_reply, admin_timestamp) VALUES ('$recipient', '$stall_number', '$admin_name', '$admin_reply', NOW())";

        // Execute the query and handle errors
        $insert_result = $connect->query($insert_query);
        if (!$insert_result) {
            die("Error executing the query: " . $connect->error);
        }

        // Redirect back to the messages page
        header("location: admin_messages.php?vendor_name=$recipient&vendor_stall_number=$stall_number");
    } else {
        // Redirect to the home page if accessed without a POST request
        header("location:admin_messages_preview.php");
    }
} else {
    header("location:admin_logout.php");
}
