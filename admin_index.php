<?php
require("config.php");

//Check if user is logged in
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];
} else {
    header("location:admin_logout.php");
}

include('admin_login_time.php');

$sql = "SELECT admin_name FROM admin_sign_in WHERE admin_userid = '$admin_userid'";

// Execute the query
$result = $connect->query($sql);
$admin_name = "";
$admin_name_error = "";

// Check if any rows were returned
if ($result->num_rows > 0) {
    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        $admin_name = $row['admin_name'];
    }
} else {
    $admin_name_error = "No results found for user ID $admin_userId";
}


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>SIGN IN</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="stylesheet" type="text/css" href="text-style.css">
    <link rel="stylesheet" type="text/css" href="box-style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<header></header>
<?php include 'sidebar.php'; ?>

<div class="head">

    <img class="public-market-pic-v2" src="assets\images\sign-in\public-market-head-v2.svg" alt="back-layer">

    <div class="head-bottom">
        <div>
            <p class="user-name">Welcome, <?php echo $admin_name  ?>! </p> <br />
            <img class="head-bottom-1" src="assets\images\sign-in\name-holder2.svg" alt="back-layer">
        </div>

        <div>
            <p class="admin-datetime-text"> Date and Time</p>
            <p class="admin-datetime">December 25 | 10:30 PM</p>
            <img class="head-bottom-2" src="assets\images\sign-in\datetime-holder3.svg" alt="back-layer">
        </div>

    </div>

    <div class="head-bottom">

        <div class="flex-column2">
            <div class="dashboard-announcement">

                <div class="flex-row-1">
                    <div>
                        <h2 class="interactive-map-header">Interactive Map</h2>
                        <div class="status-heading">
                            <div class=interactive-map-position>
                                <h1 class="rent-status-header"> Rent <br>Status </h1>
                            </div>
                            <div>
                                <p class="index-notifs"> Paid: </p>
                                <button class="index-notifs"> Ongoing: </button>
                                <button class="index-notifs"> Vacant: </button>
                            </div>
                        </div>
                    </div>

                    <a href=admin-map.php><img class="map-icon" src="assets\images\sign-in\map-icon.svg" alt="map"> </a>

                </div>

            </div>
            <div class="dashboard-announcementv2">

                <div class="flex-row-1">

                    <h2 class="notification-header">Notifications</h2>
                    <div class="message-notif">
                        <p class="admin-datetime-text-v2"> December 25, 2024</p>
                        <h1 class="admin-message-notif">You have a message!</h1>


                        <p class="admin-vendor-notif">From: Vendor1</p>

                    </div>


                </div>

                <center><input class="submit-button3" type="submit" value="View"></center>
            </div>
        </div>

        <div class="dashboard-map">
            <center>
                <a href=admin_vendor_manage_accounts.php><button class="index-buttons"> <img class="icons" src="assets\images\sign-in\payment-record.svg" alt="Sticker" class="sticker"> PAYMENT RECORDS </button> </a>
                <a href=admin_send_announcement.php><button class="index-buttons"> <img class="icons" src="assets\images\sign-in\announce.svg" alt="Sticker" class="sticker">ANNOUNCEMENTS </button> </a>
                <a href=admin_confirmpay.php><button class="index-buttons"> <img class="icons" src="assets\images\sign-in\pay.svg" alt="Sticker" class="sticker"> PAYMENTS </button> </a>
                <a href=admin_messages.php><button class="index-buttons"> <img class="icons" src="assets\images\sign-in\messages.svg" alt="Sticker" class="sticker"> INBOX </button> </a>
            </center>
        </div>



    </div>
</div>





<footer></footer>

</html>