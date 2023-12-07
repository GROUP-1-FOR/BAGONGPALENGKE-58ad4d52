<?php
require("config.php");

// Check if the user is logged in
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    // Include your existing code for session validation

    // Get the admin name from the session
    $adminName = $_SESSION["admin_name"];

    // Process the form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['send_message'])) {
        $recipient = $_POST['recipient'];
        $messageText = $_POST['message_text'];

        // Split recipient into vendor_name and vendor_stall_number
        list($vendorName, $vendorStallNumber) = explode("-", $recipient);

        // Insert the message into the admin_messages table with the current timestamp and admin name
        $sqlInsertMessage = "INSERT INTO admin_messages (vendor_name, vendor_stall_number, admin_reply, admin_timestamp, admin_name) VALUES (?, ?, ?, NOW(), ?)";
        $stmtInsertMessage = $connect->prepare($sqlInsertMessage);
        $stmtInsertMessage->bind_param('ssss', $vendorName, $vendorStallNumber, $messageText, $adminName);
        $stmtInsertMessage->execute();

        // Redirect to the messages preview page after processing the form
        header("Location: admin_messages_preview.php");
        exit();
    }
} else {
    header("location:admin_logout.php");
}
