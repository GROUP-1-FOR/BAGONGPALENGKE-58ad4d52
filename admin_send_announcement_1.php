<?php
require("config.php");
require("admin_check_login.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $announcement_title = htmlspecialchars($_POST['admin_announcement_title']);
    $announcement_subject = htmlspecialchars($_POST['admin_announcement_subject']);
    $announcement_text = htmlspecialchars($_POST["admin_announcement"]);
    $announcement_time =  htmlspecialchars($_POST['admin_announcement_time']);


    if (empty($announcement_title) || empty($announcement_subject) || empty($announcement_text) || empty($announcement_time)) {
        // Handle the error (e.g., display a message or redirect with an error flag)
        echo '<script>';
        echo 'alert("All fields are required!");';
        echo 'window.location.href = "admin_send_announcement.php";';
        echo '</script>';
        exit();
    }

    // Insert a message for each user
    $sql = "INSERT INTO announcements (admin_id,announcement_title, announcement_subject, announcement_text, announcement_time) VALUES ('$admin_id', '$announcement_title','$announcement_subject','$announcement_text','$announcement_time')";
    // Insert into vendor_notification table
    $notifTitle = "See New Announcement!";
    $announceValue = 1; // Set the confirm value to 1
    $announceDate = date('Y-m-d H:i:s');
    $vendor_userid = "ALL";

    $sqlInsertNotification = "INSERT INTO vendor_notification (vendor_userid, title, announcement, notif_date) VALUES (?, ?, ?, ?)";
    $stmtInsertNotification = $connect->prepare($sqlInsertNotification);
    $stmtInsertNotification->bind_param('ssis', $vendor_userid, $notifTitle, $announceValue, $announceDate);
    $stmtInsertNotification->execute();

    if ($connect->query($sql) !== TRUE) {
        echo "Error Adding Announcement!" . $connect->error;
    }
    echo '<script>';
    echo 'alert("Announcement Added!");';
    echo 'window.location.href = "admin_send_announcement.php";';
    echo '</script>';
}
$connect->close();
