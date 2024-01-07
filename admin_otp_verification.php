<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];
    $incorrect_otp_message = "";
} else {
    header("location:admin_logout.php");
}



if (isset($_POST["admin_otp"])) {
    $entered_otp = htmlspecialchars($_POST["admin_otp"]);
    $max_trials = 3;

    // Retrieve the stored OTP and trials from the database
    $select_query = "SELECT admin_otp, admin_otp_trials FROM admin_sign_in WHERE admin_userid = '$admin_userid'";
    $result = mysqli_query($connect, $select_query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $stored_otp = $row["admin_otp"];
        $otp_trials = $row["admin_otp_trials"];

        // Check if entered OTP matches the stored OTP
        if ($entered_otp == $stored_otp) {
            // OTP verification successful
            $reset_trials_query = "UPDATE admin_sign_in SET admin_otp_trials = 0 WHERE admin_userid = '$admin_userid'";
            mysqli_query($connect, $reset_trials_query);
            echo '<script>';
            echo 'alert("OTP Verified!");';
            echo 'window.location.href = "admin_index.php";';
            echo '</script>';
        } else {

            // Increment OTP trials
            $otp_trials++;
            // Update the database with the new trials count
            $update_trials_query = "UPDATE admin_sign_in SET admin_otp_trials = $otp_trials WHERE admin_userid = '$admin_userid'";

            mysqli_query($connect, $update_trials_query);

            $incorrect_otp_message = "Wrong OTP!";


            // Check if trials exceed the limit
            if ($otp_trials >= $max_trials) {
                // Redirect the user to the login page
                $reset_trials_query = "UPDATE admin_sign_in SET admin_otp_trials = 0 WHERE admin_userid = '$admin_userid'";
                mysqli_query($connect, $reset_trials_query);
                echo '<script>';
                echo 'alert("Reached Maximum OTP Trials!");';
                echo 'window.location.href = "admin_logout.php";';
                echo '</script>';
                exit();
            }
        }
    } else {
        // Error retrieving OTP and trials from the database
        echo "Error: " . mysqli_error($connect);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["admin_resend_otp"])) {
    include("admin_otp_generation.php");
    $reset_trials_query = "UPDATE admin_sign_in SET admin_otp_trials = 0 WHERE admin_userid = '$admin_userid'";
    mysqli_query($connect, $reset_trials_query);
    echo '<script>';
    echo 'alert("OTP Resent!");';
    echo '</script>';
}


if (isset($_GET['cancel_button'])) {
    // Execute the SQL query
    $reset_trials_query = "UPDATE admin_sign_in SET admin_otp_trials = 0 WHERE admin_userid = '$admin_userid'";
    mysqli_query($connect, $reset_trials_query);

    header("Location: admin_logout.php");
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body class="bagongpgalengke-v2">
    <header> LOGO </header>

    <div class="website-title-v2">
        <h1 class="title3"> WELCOME TO </h1>
        <h1 class="title4"> SANTA ROSA<br>PUBLIC MARKET</h1>
    </div>

    <div>
        <img class="white-front" src="assets\images\sign-in\white-front.svg" alt="white-front">
        <img class="front-layer-v2" src="assets\images\sign-in\front.svg" alt="front">
        <img class="back-layer-v2" src="assets\images\sign-in\back.svg" alt="back">
    </div>

    <div class="otp-verification">

        <div class="otp-heading">
            <h2>OTP Verification</h2>
        </div>
        <div>
            <form action="" method="post">
                <input class="otp-box" type="text" pattern="[0-9]{6}" maxlength="6" id="otp" name="admin_otp" title="Please enter six numbers" placeholder="Enter OTP" required>
                <button class="submit-button" type="submit">Verify OTP</button> <br />
                <span style="color: red">
                    <?php
                    echo $incorrect_otp_message;
                    ?>
                </span>
            </form>
            <div>
                <form action="" method="post" id="resendOTPForm">
                    <button class="resend-button" type="submit" id="resendOTPButton" name="admin_resend_otp" disabled>Resend OTP</button>
                    <div id="resendOTPMessage"></div>
                </form>
            </div>

            <script>
                $(document).ready(function() {
                    var cooldownTime = 45; // 45 seconds
                    var isCooldown = true;

                    // Display cooldown message on page load
                    $("#resendOTPMessage").text(cooldownTime + " seconds");

                    // Start the cooldown timer
                    var timer = setInterval(function() {
                        cooldownTime--;
                        $("#resendOTPMessage").text(cooldownTime + " seconds");

                        if (cooldownTime <= 0) {
                            // Enable the button after cooldown
                            $("#resendOTPButton").prop("disabled", false);
                            $("#resendOTPMessage").text(" ");
                            isCooldown = false;
                            clearInterval(timer);
                        }
                    }, 1000);
                });
            </script>

        </div>
        <a href="?cancel_button=1"> CANCEL </a>
    </div>
    <footer> </footer>
</body>

</html>