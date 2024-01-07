<?php
require("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['announcement_id'])) {
    $announcementId = $_POST['announcement_id'];
    $announcementTitle = $_POST['announcement_title'];
    $announcementSubject = $_POST['announcement_subject'];
    $announcementText = $_POST['announcement_text'];
    $announcementTime = $_POST['announcement_time'];

    // Update the announcement details in the database
    $sql = "UPDATE announcements SET announcement_title = ?, announcement_subject = ?, announcement_text = ?, announcement_time = ? WHERE announcement_id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("ssssi", $announcementTitle, $announcementSubject, $announcementText, $announcementTime, $announcementId);
    $stmt->execute();

    $stmt->close();
    $connect->close();

    // Redirect back to the main page after updating
    header("Location: admin_send_announcement.php");
    exit();
} else {
    // If the form is not submitted properly, redirect to the main page
    header("Location: admin_send_announcement.php");
    exit();
}
