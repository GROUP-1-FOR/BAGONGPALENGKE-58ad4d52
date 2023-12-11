<?php
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> SIGN IN </title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="stylesheet" type="text/css" href="box-style.css">
    <link rel="stylesheet" type="type/js-style" href="js-style.js">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body>
    <header> LOGO</header>

    <div class="main-sidebar">
        <ul class="sidebar-outside">
        <div class="profile-container">
        <img class="profile-pic-holder" src="assets\images\sign-in\profile-pic.svg">
            <img class="profile-design" src="assets\images\sign-in\profile-design.png">
        </div>
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
            <a href=admin_logout.php>
                <h1 class="logout-button">LOGOUT</h1>
            </a>
        </div>
    </div>

    <div class="map-background"><div>
    </div class="bagong-palengke">
        <img src="assets\images\sign-in\bagong-palengke-map.svg" class="palengke-map">
        <div class="box-arrangement">
        <?php
        // Loop through 75 tables
        if (isset($connect) && $connect instanceof mysqli && !$connect->connect_error) {
            for ($i = 1; $i <= 75; $i++) {
                // Check if the value of $i exists in the admin_stall_map table
                $query = "SELECT COUNT(*) as count, balance FROM admin_stall_map WHERE vendor_stall_number = ?";
                $stmt = $connect->prepare($query);
                $stmt->bind_param("i", $i);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $count = $row['count'];
                $balance = $row['balance'];
        
                // Set the table color based on whether the value exists in the database and balance
                $tableColor = '';
                if ($count > 0) {
                    if ($balance == 0) {
                        $tableColor = 'background-color: green;';
                    } elseif ($balance > 0) {
                        $tableColor = 'background-color: red;';
                    }
                } else {
                    $tableColor = 'background-color: gray;';
                }
        
                echo '<div class="box box-" id="table' . $i . '" data-count="' . $count . '" onclick="toggleAddButton(' . $i . ')" style="' . $tableColor . '">';
                echo '<p>Stall ' . $i . '</p>'; // Display stall number without leading zeros
                echo '<a class="add-button" href="javascript:void(0);" onclick="confirmAdd(' . $i . ')">Add</a>';
                echo '</div>';
            }
        } else {
            echo "Database connection not established.";
        }
        ?>
    </div>

    <!-- Include your JavaScript file here -->
    <script src="interactive_map.js"></script>
    <script>
    function toggleAddButton(tableNumber) {
        var table = document.getElementById('table' + tableNumber);
        var addButton = table.getElementsByClassName('add-button')[0];

        // Check if the count is greater than 0
        var count = table.getAttribute('data-count');
        if (count > 0) {
            addButton.style.display = 'none';
        } else {
            addButton.style.display = (addButton.style.display === 'none' || addButton.style.display === '') ? 'inline-block' : 'none';
        }
    }

    function confirmAdd(tableNumber) {
        var confirmAddition = confirm('Are you sure you want to add a vendor?');
        if (confirmAddition) {
            var url = 'admin_create_vendor_account.php?stall_number=' + tableNumber;
            window.location.href = url;
        }
    }
</script>
    <center>
        <button><a href="admin_index.php">Home</a></button>
    </center>
</body>

</html>