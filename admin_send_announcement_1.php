<?php

require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $announcement_text = $_POST['admin_announcement'];

        // Retrieve all user IDs
        $sql_users = "SELECT vendor_id FROM vendor_sign_in";
        $result_users = $connect->query($sql_users);

        if ($result_users->num_rows > 0) {
            while ($row = $result_users->fetch_assoc()) {
                $recipient_id = $row['vendor_id'];

                // Insert a message for each user
                $sql = "INSERT INTO announcements (admin_id, vendor_id, announcement_text) VALUES ('$id', '$recipient_id', '$announcement_text')";

                if ($connect->query($sql) !== TRUE) {
                    echo "Error sending announcement to user with ID $recipient_id: " . $connect->error;
                }
            }
            echo '<script>';
            echo 'alert("Announcement Sent to All Market Vendors!");';
            echo 'window.location.href = "admin_send_announcement.php";';
            echo '</script>';
        } else {
            echo '<script>';
            echo 'alert("No Market Vendors Found!");';
            echo 'window.location.href = "admin_send_announcement.php";';
            echo '</script>';
        }
    }
    $connect->close();
} else {
    header("location:admin_login.php");
    exit();
}
