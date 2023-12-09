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
        <title>Report a Problem</title>
    </head>

    <body>

        <h1 align="center">Report a Problem </h1>

        <form action="admin_send_report_1.php" method="post">
            <label for="admin_report_ticket">Ticket No: </label>
            <input type="text" id="admin_report_ticket" name="admin_report_ticket" required><br />

            <label for="admin_report_message">Report Message</label>
            <textarea name="admin_report_message" id="admin_report_message" cols="30" rows="5" required></textarea><br>

            <input type="submit" value="Submit">
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
