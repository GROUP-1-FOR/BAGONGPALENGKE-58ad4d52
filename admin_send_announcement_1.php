<?php


require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $announcement_title = htmlspecialchars($_POST['admin_announcement_title']);
        $announcement_subject = htmlspecialchars($_POST['admin_announcement_subject']);
        $announcement_text = htmlspecialchars($_POST["admin_announcement"]);


        if (empty($announcement_title) || empty($announcement_subject) || empty($announcement_text)) {
            // Handle the error (e.g., display a message or redirect with an error flag)
            echo '<script>';
            echo 'alert("All fields are required!");';
            echo 'window.location.href = "admin_send_announcement.php";';
            echo '</script>';
            exit();
        }

        // Insert a message for each user
        $sql = "INSERT INTO announcements (admin_id,announcement_title, announcement_subject, announcement_text) VALUES ('$admin_id', '$announcement_title','$announcement_subject','$announcement_text')";
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
            echo "Error sending announcement to vendors!" . $connect->error;
        }
        echo '<script>';
        echo 'alert("Announcement Sent to All Market Vendors!");';
        echo 'window.location.href = "admin_send_announcement.php";';
        echo '</script>';
    }
    $connect->close();
} else {
    header("location:admin_logout.php");
    exit();
}
