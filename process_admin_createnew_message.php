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

        // Fetch the vendor_userid based on vendor_name and vendor_stall_number
        $sqlFetchVendorUserId = "SELECT vendor_userid FROM vendor_sign_in WHERE vendor_name = ? AND vendor_stall_number = ?";
        $stmtFetchVendorUserId = $connect->prepare($sqlFetchVendorUserId);
        $stmtFetchVendorUserId->bind_param('ss', $vendorName, $vendorStallNumber);
        $stmtFetchVendorUserId->execute();
        $resultFetchVendorUserId = $stmtFetchVendorUserId->get_result();

        if ($resultFetchVendorUserId->num_rows > 0) {
            $rowVendorUserId = $resultFetchVendorUserId->fetch_assoc();
            $vendorUserId = $rowVendorUserId['vendor_userid'];

            // Insert the message into the admin_messages table with the current timestamp, admin name, and vendor_userid
            $sqlInsertMessage = "INSERT INTO admin_messages (vendor_name, vendor_stall_number, vendor_userid, admin_reply, admin_timestamp, admin_name) VALUES (?, ?, ?, ?, NOW(), ?)";
            $stmtInsertMessage = $connect->prepare($sqlInsertMessage);
            $stmtInsertMessage->bind_param('sssss', $vendorName, $vendorStallNumber, $vendorUserId, $messageText, $adminName);
            $stmtInsertMessage->execute();

            // Redirect to the messages preview page after processing the form
            header("Location: admin_messages_preview.php");
            exit();
        } else {
            // Handle the case where vendor_userid is not found
            die("Error: Vendor userid not found.");
        }
    }
} else {
    header("location:admin_logout.php");
}
?>
