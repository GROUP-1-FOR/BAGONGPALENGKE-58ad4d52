<?php

require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $vendor_id = $_SESSION["id"];
    $vendor_userid = $_SESSION["userid"];
    $incorrect_otp_message = "";

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
        <title>VENDOR OTP Verification</title>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>


    </head>

    <body>

        <header class=>
            <h2> LOGO </h2>

            <h1 class="title1"> WELCOME TO </h1>
            <h1 class="title2"> SANTA ROSA PUBLIC MARKET </h1>
            <!--<div class="nav">
      <a href="">HOME</a>
      <a href="">BLOG</a>
      <a href="">About</a>
      <a href="">FAQs</a>
      <a href="">Info</a>
      <a href="">Tricks</a>
      <a href="">LOGIn</a>

    </div> -->
        </header>
        <div>
            <h2>OTP Verification</h2>
            <form action="" method="post">
                <label for="otp">Enter OTP:</label>
                <input type="text" pattern="[0-9]{6}" maxlength="6" id="otp" name="vendor_otp" title="Please enter six numbers" placeholder="123456" required>
                <button type="submit">Verify OTP</button> <br />
                <span style="color: red">
                    <?php
                    echo $incorrect_otp_message;
                    ?>
                </span>

            </form>
        </div>

        <div>
            <br />

            <form action="" method="post" id="resendOTPForm">
                <button type="submit" id="resendOTPButton" name="vendor_resend_otp" disabled>Resend OTP</button>
                <div id="resendOTPMessage"></div>
            </form>
        </div>

        <script>
            $(document).ready(function() {
                var cooldownTime = 60; // 1 minute in seconds
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


        <a href="?cancel_button=1"> CANCEL </a>

        <footer>

        </footer>

    </body>

    </html>


<?php
} else {
    header("location:vendor_logout.php");
}
