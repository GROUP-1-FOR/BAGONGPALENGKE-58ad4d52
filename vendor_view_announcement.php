<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];
} else {
    header("location:vendor_logout.php");
}


// Retrieve announcements for the logged-in user
$sql_messages = "SELECT * FROM announcements  ORDER BY announcement_time DESC";
$result_messages = $connect->query($sql_messages);

$connect->close();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcement</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="javascript" type="text/script" href="js-style.js">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <style>
        .error {
            color: red;
        }

        .counter {
            font-size: 12px;
            color: gray;
        }
    </style>
</head>


<body>
    <header class="header2"></header>
    <?php include 'sidebar2.php'; ?>


    <div class="flex-row">
        <h2 class="announcement-header">ANNOUNCEMENT</h2>
        <div class="report-manage1">

            <?php
            $currentDate = date('Y-m-d');
            if ($result_messages->num_rows > 0) {
                while ($row = $result_messages->fetch_assoc()) {

                    if ($row['announcement_time'] == $currentDate || $row['announcement_time'] < $currentDate) {
            ?>
                        <div class="notification-box">

                            <div class="inside-notification">
                                <p class="announcement-datetime"><?php echo $row['announcement_time']; ?></p>
                                <p class="announcement-title"><?php echo $row['announcement_title']; ?> </p>
                                <p class="announcement-subject"><?php echo $row['announcement_subject']; ?></p>
                                <p class="announcement-content"><?php echo $row['announcement_text']; ?></p>
                            </div>
                        </div>
            <?php  }
                }
            } else {
                echo "<p>No announcements found in your inbox.</p>";
            }
            ?>
        </div>

    </div>

    <footer></footer>
</body>

</html>