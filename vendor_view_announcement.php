<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];

    // Retrieve announcements for the logged-in user
    $sql_messages = "SELECT * FROM announcements  ORDER BY announcement_id DESC";
    $result_messages = $connect->query($sql_messages);

    $connect->close();
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Announcements</title>
    </head>

    <body>
        <h1>Announcement Inbox</h1>

        <?php
        if ($result_messages->num_rows > 0) {
            while ($row = $result_messages->fetch_assoc()) {
                // Display messages
                echo "<hr />";
                echo "<p>Treasury Office: </p>";
                echo "<p>Announcement: " . $row['announcement_text'] . "</p>";
                echo "<p>Date and Time: " . $row['announcement_time'] . "</p><hr>";
                echo "<hr />";
            }
        } else {
            echo "<p>No announcements found in your inbox.</p>";
        }
        ?>


        <a href=vendor_index.php>
            <h1>BACK</h1>
        </a>

    </body>

    </html>

<?php } else {
    header("location:vendor_login.php");
}
?>