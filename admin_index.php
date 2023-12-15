<?php
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

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
    <title>SIGN IN</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <header></header>
    <?php include 'sidebar.php'; ?>

    <div class ="head">
        <img class="public-market-pic" src="assets\images\sign-in\public-market-head.svg" alt="back-layer">
    <div class="head-bottom">
        <img class="head-bottom-1" src="assets\images\sign-in\name-holder.svg" alt="back-layer">
        <img class="head-bottom-2" src="assets\images\sign-in\datetime-holder.svg" alt="back-layer">
    </div>
    <div class="head-bottom">
    <div class="dashboard-announcement"></div>
    <div class="dashboard-map"></div>


    </div>
    </div>



    <!-- <h3 class="announcement-text">Announcement</h3>
    <div class="flex-box">
        <main class="main-container">
            <div class="dashboard-announcement">


        </main>
    </div> -->


    <footer></footer>
</body>
</html>

<?php
} else {
    header("location:admin_logout.php");
}
?>
