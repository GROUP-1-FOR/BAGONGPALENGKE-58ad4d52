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
        <link rel="stylesheet" type="type/js-style" href="js-style.js">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
        <!-- Add these lines for Bootstrap CSS and JS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.9/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </head>

    <body>
        <header class="header2"></header>
        <?php include 'sidebar.php'; ?>
        <div class="map-background">
            <div>
            </div class="bagong-palengke">
            <img src="assets\images\sign-in\bagong-palengke-map.svg" class="palengke-map">
            <div class="box-arrangement">
                <?php
                // Loop to generate boxes
                for ($i = 1; $i <= 74; $i++) {
                    // Fetch data from the database for the current box
                    $query = "SELECT COUNT(*) as count, balance, vacant, vendor_name FROM admin_stall_map WHERE vendor_stall_number = ?";
                    $stmt = $connect->prepare($query);
                    $stmt->bind_param("i", $i);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                    $count = $row['count'];
                    $balance = $row['balance'];
                    $vacant = $row['vacant'];
                    $vendorName = $row['vendor_name'];

                    // Set the box color based on the fetched data
                    $boxColor = '';
                    if ($count > 0) {
                        if ($balance == 0) {
                            $boxColor = 'background-color: green;';
                        } elseif ($balance > 0) {
                            $boxColor = 'background-color: red;';
                        }
                    } else {
                        $boxColor = 'background-color: gray;';
                    }

                    // Output the box with the determined color and clickable status
                    if ($vacant == 0) {
                        echo '<div class="box box-' . $i . '" style="' . $boxColor . '" onclick="handleBoxClick(' . $i . ')">' . $i . '</div>';
                    } else {
                        // Add a Bootstrap tooltip to show vendor_name on hover
                        echo '<div class="box box-' . $i . '" style="' . $boxColor . '" data-toggle="tooltip" data-placement="top" title="' . $vendorName . '" onclick="handleBoxClick(' . $i . ')">' . $i . '</div>';
                    }
                }

                // Add this script to initialize Bootstrap tooltips
                echo '<script>$(document).ready(function(){
                        $(\'[data-toggle="tooltip"]\').tooltip();
                    });</script>';
                ?>

                <script>
                    function handleBoxClick(tableNumber) {
                        var confirmAddition = confirm('Add vendor to Stall ' + tableNumber + '?');
                        if (confirmAddition) {
                            var url = 'admin_create_vendor_account.php?stall_number=' + tableNumber;
                            window.location.href = url;
                        }
                    }
                </script>
                <footer></footer>
    </body>

    </html>
<?php } else {
    header("location:admin_login.php");
}
?>