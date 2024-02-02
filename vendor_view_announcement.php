<?php
require("config.php");
require("vendor_check_login.php");

// Retrieve announcements for the logged-in user
$sql_messages = "SELECT * FROM announcements  ORDER BY announcement_time DESC";
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
    $currentDate = date('Y-m-d');
    if ($result_messages->num_rows > 0) {
        while ($row = $result_messages->fetch_assoc()) {

            if ($row['announcement_time'] == $currentDate || $row['announcement_time'] < $currentDate) {
    ?>
                <h1 style="color: green;"><?php echo $row['announcement_title']; ?> </h1>
                <h2 style="color: gray;"><?php echo $row['announcement_subject']; ?></h2>
                <p> <?php echo $row['announcement_text']; ?></p>
                <p><?php echo $row['announcement_time']; ?></p>
    <?php  }
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