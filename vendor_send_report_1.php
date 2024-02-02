<?php
require("config.php");

require("vendor_check_login.php");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vendor_report_ticket = htmlspecialchars($_POST['vendor_report_ticket']);
    $vendor_report_message = trim(htmlspecialchars($_POST['vendor_report_message']));

    // Server-side validation
    if (empty($vendor_report_message)) {
        echo '<script>';
        echo 'alert("Empty Message!");';
        echo 'window.location.href = "vendor_send_report.php";';
        echo '</script>';
    } elseif (strlen($vendor_report_message) > 500) {
        echo '<script>';
        echo 'alert("Report Message Too Long!");';
        echo 'window.location.href = "vendor_send_report.php";';
        echo '</script>';
    }

    // Initialize $vendor_name outside the conditional block
    $vendor_name = "";

    $sql_fetch_user_details = "SELECT vendor_name FROM vendor_sign_in WHERE vendor_userid ='$vendor_userid'";
    $result_fetch_user_details = $connect->query($sql_fetch_user_details);

    // Check if the query was successful
    if ($result_fetch_user_details) {
        // Fetch data row by row
        while ($row = $result_fetch_user_details->fetch_assoc()) {
            // Access individual columns using $row['column_name']
            $vendor_name = $row['vendor_name'];
        }

        // Free result set
        $result_fetch_user_details->free_result();
    } else {
        echo "Error: " . $sql_fetch_user_details . "<br>" . $connect->error;
    }

    // Insert the report details
    $sql = "INSERT INTO report_bug (vendor_userid,vendor_name, ticket_number, report_message) VALUES ('$vendor_userid', '$vendor_name', '$vendor_report_ticket','$vendor_report_message')";
    if ($connect->query($sql) !== TRUE) {
        echo '<script>';
        echo 'alert("Error sending report to developers!");';
        echo 'window.location.href = "vendor_index.php";';
        echo '</script>';
    }
    echo '<script>';
    echo 'alert("Report Sent to Developers!");';
    echo 'window.location.href = "vendor_index.php";';
    echo '</script>';
}
$connect->close();
