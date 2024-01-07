<?php
require("config.php");

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $announcementId = $_GET['id'];

    // Remove the announcement from the database
    $sql = "DELETE FROM announcements WHERE announcement_id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("i", $announcementId);
    $stmt->execute();

    $stmt->close();
    $connect->close();

    // Redirect back to the main page after removal
    header("Location: admin_send_announcement.php");
    exit();
} else {
    header("Location: admin_send_announcement.php");
    exit();
}
