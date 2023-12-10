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
    <title> SIGN IN </title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="stylesheet" type="text/css" href="box-style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body>
    <header> LOGO</header>

    <div class="main-sidebar">

        <ul class="sidebar-outside">
            <img class="profile-design" src="assets\images\sign-in\profile-design.png">
        </ul>

        <div class="sidebar-inside">
            <ul class="dashboard-sidebar">
                <li><a class="home-index" href=admin_index.php> Home </a></li>
                <li><a class="manage-vendor" href=admin_vendor_manage_accounts.php> Manage Vendor Accounts </a></li>
                <li><a class="report-management" href="#"> Report Management </a></li>
                <li><a class="help-button" href="#"> Help </a></li>
            </ul>
        </div>

        <div>
            <a href=add `min_logout.php>
                <h1 class="logout-button">LOGOUT</h1>
            </a>
        </div>

    </div>
    <div class="map-background">
    <div>
    </div class="bagong-palengke">
        <img src="assets\images\sign-in\bagong-palengke-map.svg" class="palengke-map">
        <div class="box-arrangement">
            <div class="box box-1">1</div>
            <div class="box box-2">2</div>
            <div class="box box-3">3</div>
            <div class="box box-4">4</div>
            <div class="box box-5">5</div>
            <div class="box box-6">6</div>
            <div class="box box-7">7</div>
            <div class="box box-8">8</div>
            <div class="box box-9">9</div>
            <div class="box box-10">10</div>
            <div class="box box-11">11</div>
            <div class="box box-12">12</div>
            <div class="box box-13">13</div>
            <div class="box box-14">14</div>
            <div class="box box-15">15</div>
            <div class="box box-16">16</div>
            <div class="box box-17">17</div> 
            <div class="box box-18">18</div>
            <div class="box box-19">19</div>
            <div class="box box-20">20</div>
            <div class="box box-21">21</div>
            <div class="box box-22">22</div>
            <div class="box box-23">23</div>
            <div class="box box-24">24</div>
            <div class="box box-25">25</div>
            <div class="box box-26">26</div>
            <div class="box box-27">27</div>
            <div class="box box-28">28</div>
            <div class="box box-29">29</div>
            <div class="box box-30">30</div>

            
            </div>
    </div>
    </div>
    <script>
        // JavaScript code for handling box clicks
        document.addEventListener("DOMContentLoaded", function () {
            var boxes = document.querySelectorAll('.box');

            boxes.forEach(function (box) {
                box.addEventListener('click', function () {
                    // Change color to green
                    this.classList.toggle('green');

                    // Prompt for add button (you can customize this part)
                    if (this.classList.contains('green')) {
                        var addButton = confirm("Do you want to add something?");
                        if (addButton) {
                            // Add your logic for the add button action
                            // This can include an AJAX request to a server or any other functionality
                            console.log("Add button clicked for box " + this.textContent);
                        }
                    }
                });
            });
        });
    </script>
    <footer></footer>
</body>

</html>
<?php } else {
    header("location:admin_login.php");
}
?>