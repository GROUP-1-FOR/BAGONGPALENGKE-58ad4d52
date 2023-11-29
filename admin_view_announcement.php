<?php

require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    $sql_sent_announcement = "SELECT DISTINCT announcement_text, announcement_time FROM announcements ORDER BY announcement_id DESC";
    $result_sent_announcement = $connect->query($sql_sent_announcement);

    $connect->close();

?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sent Announcements</title>
    </head>

    <body>
        <h1>Sent Announcements</h1>

        <?php
        if ($result_sent_announcement->num_rows > 0) {
            while ($row = $result_sent_announcement->fetch_assoc()) {
                // Display sent announcements
                echo "<p>Announcement: " . $row['announcement_text'] . "</p>";
                echo "<p>Date and Time: " . $row['announcement_time'] . "</p><hr>";
                echo "<hr />";
            }
        } else {
            echo "<p>No sent announcements found.</p>";
        }
        ?>

        <a href=admin_send_announcement.php>
            <h1>BACK</h1>
        </a>
    </body>

    </html>

<?php } else {
    header("location:admin_login.php");
    exit();
}
