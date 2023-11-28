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
        <h1>Send an Announcement:</h1>

        <form action="admin_send_announcement_1.php" method="post">
            <label for="Market Vendors">To all Market Vendors:</label>
            </select><br>
            <label for="admin_announcement">Announcement Message</label>
            <textarea name="admin_announcement" id="admin_announcement" cols="30" rows="5" required></textarea><br>
            <input type="submit" value="Send announcement">
        </form>

        <a href=admin_view_announcement.php>
            <h1>SEE ALL ANNOUNCEMENTS</h1>
        </a>

        <a href=admin_index.php>
            <h1>BACK</h1>
        </a>
    </body>

    </html>
<?php } else {
    header("location:admin_login.php");
    exit();
}
