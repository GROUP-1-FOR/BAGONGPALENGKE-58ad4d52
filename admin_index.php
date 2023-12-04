<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];


    //to know last log in time of admin
    //include('admin_login_time.php');

?>
    <!DOCTYPE html>
    <html>

    <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title> SIGN IN </title>
  <link rel="stylesheet" type="text/css" href="index.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

    <body>

    

    <div class="sidebar">
        <ul class="sidebar-outside">
        <img class="profile-design" src="assets\images\sign-in\profile-design.png">




        </a>

        </ul>
        <div class="sidebar-inside">
        <ul class="dashboard-sidebar">
            <li><a class="home-index" href=admin_index.php> Home </a></li>
            <li><a class="manage-vendor" href=admin_vendor_manage_accounts.php> Manage Vendor Accounts </a></li>
            <li><a class="report-management" href="#"> Report Management </a></li>
            <li><a class="help-button" href="#"> Help </a></li>
        </ul>
        </div>
        <div class="logout-button">
            <a href=admin_logout.php>
            <h1>LOGOUT</h1>
        </div>
    </div>
    </div>
    

<div class="content">

        <h1>Welcome, <?php echo $admin_userid ?>! </h1>

        <a href=admin_confirmpay.php>Confirm Payment</a>

        <a href=''> View Market Rental Stalls </a>

        <a href="admin_send_announcement.php"> Send Announcements </a>


        

        <a href=admin_messages_preview.php> Messages </a>

        <a href=admin_feature_6.php> Feature 6 </a>


        </div>
        </div>
  
    </body>

    </html>
<?php } else {
    header("location:admin_login.php");
}
