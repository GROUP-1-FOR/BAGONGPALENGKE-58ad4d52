<?php

require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];
} else {
    header("location:admin_logout.php");
}

$sql_ticket_number = "SELECT MAX(ticket_number) AS max_ticket FROM report_bug";
$result = $connect->query($sql_ticket_number);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentTicketNumber = $row['max_ticket'] + 1;
} else {
    // If no existing ticket, start from 1
    $currentTicketNumber = 1;
}
$formattedTicketNumber = sprintf('%04d', $currentTicketNumber);

$connect->close();


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGN IN</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="javascript" type="text/script" href="js-style.js">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body>
    <header></header>
    <?php include 'sidebar.php'; ?>

    <div class="flex-row">
        <h2 class="manage-account-header">MANAGE ACCOUNTS</h2>
        <div class="manage-account">

            <div class="flex-box1">
                <div class="main-container">



                </div>

            </div>
        </div>

    </div>


    <footer></footer>
</body>

</html>