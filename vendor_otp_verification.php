<?php

require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $vendor_id = $_SESSION["id"];
    $vendor_userid = $_SESSION["userid"];
    $incorrect_otp_message = "";
} else {
    header("location:vendor_logout.php");
}



if (isset($_POST["vendor_otp"])) {
    $entered_otp = htmlspecialchars($_POST["vendor_otp"]);
    $max_trials = 3;

    // Retrieve the stored OTP and trials from the database
    $select_query = "SELECT vendor_otp, vendor_otp_trials FROM vendor_sign_in WHERE vendor_userid = '$vendor_userid'";
    $result = mysqli_query($connect, $select_query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $stored_otp = $row["vendor_otp"];
        $otp_trials = $row["vendor_otp_trials"];

        // Check if entered OTP matches the stored OTP
        if ($entered_otp == $stored_otp) {
            // OTP verification successful
            $reset_trials_query = "UPDATE vendor_sign_in SET vendor_otp_trials = 0 WHERE vendor_userid = '$vendor_userid'";
            mysqli_query($connect, $reset_trials_query);
            echo '<script>';
            echo 'alert("OTP Verified!");';
            echo 'window.location.href = "vendor_index.php";';
            echo '</script>';
        } else {

            // Increment OTP trials
            $otp_trials++;
            // Update the database with the new trials count
            $update_trials_query = "UPDATE vendor_sign_in SET vendor_otp_trials = $otp_trials WHERE vendor_userid = '$vendor_userid'";

            mysqli_query($connect, $update_trials_query);

            $incorrect_otp_message = "Wrong OTP!";


            // Check if trials exceed the limit
            if ($otp_trials >= $max_trials) {
                // Redirect the user to the login page
                $reset_trials_query = "UPDATE vendor_sign_in SET vendor_otp_trials = 0 WHERE vendor_userid = '$vendor_userid'";
                mysqli_query($connect, $reset_trials_query);
                echo '<script>';
                echo 'alert("Reached Maximum OTP Trials!");';
                echo 'window.location.href = "vendor_logout.php";';
                echo '</script>';
                exit();
            }
        }
    } else {
        // Error retrieving OTP and trials from the database
        echo "Error: " . mysqli_error($connect);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["vendor_resend_otp"])) {
    include("vendor_otp_generation.php");
    $reset_trials_query = "UPDATE vendor_sign_in SET vendor_otp_trials = 0 WHERE vendor_userid = '$vendor_userid'";
    mysqli_query($connect, $reset_trials_query);
    echo '<script>';
    echo 'alert("OTP Resent!");';
    echo 'window.location.href = "vendor_otp_verification.php";';
    echo '</script>';
}

if (isset($_GET['cancel_button'])) {
    // Execute the SQL query
    $reset_trials_query = "UPDATE vendor_sign_in SET vendor_otp_trials = 0 WHERE vendor_userid = '$vendor_userid'";
    mysqli_query($connect, $reset_trials_query);

    header("Location: vendor_logout.php");
    exit;
}

?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGN IN</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="stylesheet" type="text/css" href="text-style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body class="bagongpgalengke-v2">
    <header><img src="assets\images\sign-in\Santa-Rosa-Logo.svg" class="logo-src"></header>

    <div class="website-title-v2">
        <h1 class="title3"> WELCOME TO </h1>
        <h1 class="title4"> SANTA ROSA<br>PUBLIC MARKET</h1>
    </div>

    <div>
        <img class="white-front" src="assets\images\sign-in\white-front.svg" alt="white-front">
        <img class="front-layer-v2" src="assets\images\sign-in\front.svg" alt="front">
        <img class="back-layer-v2" src="assets\images\sign-in\back.svg" alt="back">
    </div>

    <!-- <div class="otp-buttons">
        <br>
        <form class="form-group-login" action="" method="post">

            <input class="input-box" type="text" pattern="[0-9]{6}" maxlength="6" id="otp" name="vendor_otp" title="Please enter six numbers" placeholder="Enter OTP" required>
            <button class="submit-button" type="submit">Verify OTP</button> <br />
            <span style="color: red">
                //<//?php echo $incorrect_otp_message;?>
            </span>
        </form><br><br>

        <form action="" method="post" id="resendOTPForm">
            <button class="resend-button" type="submit" id="resendOTPButton" name="admin_resend_otp" disabled>Resend OTP</button>
            <div id="resendOTPMessage"></div>
        </form>


        <a class="cancel-button" href="?cancel_button=1"> CANCEL </a>
    </div> -->





    <div class="login-form">

        <div class="form-group">
            <form class="" action="" method="post">
                <div class="flexbox-row">
                    <div>
                        <input class="input-box" type="text" pattern="[0-9]{6}" maxlength="6" id="otp" name="vendor_otp" title="Please enter six numbers" placeholder="Enter OTP" required>
                    </div>

                    <div>
                        <button class="verify-button" type="submit">Verify OTP</button> <br />
                    </div>
                </div>

                <span id="error-message" style="color: red">
                    <?php
                    echo $incorrect_otp_message;
                    ?>
                </span>
            </form>
            <div class="buttons-container">
                <form action="" method="post" id="resendOTPForm">
                    <button class="resend-button" type="submit" id="resendOTPButton" name="vendor_resend_otp" disabled>Resend OTP</button>
                    <div id="resendTokenMessage" class="timer-message"></div>
                    <br>
                    <a class="back-button1" href="vendor_login.php">Back</a>
                </form>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <script>
            $(document).ready(function() {
                var cooldownTime = 45; // 45 seconds
                var isCooldown = true;

                // Display cooldown message on page load
                $("#resendTokenMessage").text(cooldownTime + " seconds");

                // Start the cooldown timer
                var timer = setInterval(function() {
                    cooldownTime--;
                    $("#resendTokenMessage").text(cooldownTime + " seconds");

                    if (cooldownTime <= 0) {
                        // Enable the button after cooldown
                        $("#resendOTPButton").prop("disabled", false);
                        $("#resendTokenMessage").text(" ");
                        isCooldown = false;
                        clearInterval(timer);
                    }
                }, 1000);
            });
        </script>
        <footer> </footer>
</body>

</html>