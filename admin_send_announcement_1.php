<?php


require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $announcement_title = htmlspecialchars($_POST['admin_announcement_title']);
        $announcement_subject = htmlspecialchars($_POST['admin_announcement_subject']);
        $announcement_text = htmlspecialchars($_POST["admin_announcement"]);


        // Insert a message for each user
        $sql = "INSERT INTO announcements (admin_id,announcement_title, announcement_subject, announcement_text) VALUES ('$admin_id', '$announcement_title','$announcement_subject','$announcement_text')";

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
