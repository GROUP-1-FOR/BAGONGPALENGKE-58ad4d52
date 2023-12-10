<?php

require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Send Announcement</title>
    </head>

    <body>

        <h1>Welcome, <?php echo $admin_userid ?>! </h1>
        <?php
        $sql_sent_announcement = "SELECT DISTINCT announcement_title,announcement_subject,announcement_text, announcement_time FROM announcements ORDER BY announcement_id DESC";
        $result_sent_announcement = $connect->query($sql_sent_announcement);

        $connect->close();


        if ($result_sent_announcement->num_rows > 0) {
            while ($row = $result_sent_announcement->fetch_assoc()) {
        ?>
                <h1 style="color: green;"><?php echo $row['announcement_title']; ?> </h1>
                <h2 style="color: gray;"><?php echo $row['announcement_subject']; ?></h2>
                <p><?php echo $row['announcement_text']; ?></p>
                <p><?php echo $row['announcement_time']; ?></p>

        <?php }
        } else {
            echo "<p>No sent announcements found.</p>";
        }

        ?>

        <h1>Send an Announcement:</h1>

        <form action="admin_send_announcement_1.php" method="post">
            <h2>Market Vendors</h2>
            <label for="admin_announcement_title">Announcement Title</label>
            <input type="text" id="admin_announcement_title" name="admin_announcement_title" required><br />
            <label for="admin_announcement_subject">Announcement Subject</label>
            <input type="text" id="admin_announcement_subject" name="admin_announcement_subject" required><br />
            <label for="admin_announcement">Announcement Message</label>
            <textarea name="admin_announcement" id="admin_announcement" cols="30" rows="5" required></textarea><br>
            <input type="submit" value="Send announcement">
        </form>

        <a href=admin_index.php>
            <h1>BACK</h1>
        </a>
    </body>

    </html>
<?php } else {
    header("location:admin_logout.php");
    exit();
}
