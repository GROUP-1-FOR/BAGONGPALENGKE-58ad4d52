<?php
require("config.php");

// Initialize variables
$incorrect_otp_message = "";

// Check if the user is logged in
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];
    $incorrect_otp_message = "";
} else {
    header("location:admin_logout.php");
}

// Check if OTP is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["admin_otp"])) {
    $entered_otp = htmlspecialchars($_POST["admin_otp"]);
    $max_trials = 3;

    // Retrieve the stored OTP and trials from the database
    $select_query = "SELECT admin_otp, admin_otp_trials FROM admin_sign_in WHERE admin_id = $admin_id";
    $result = mysqli_query($connect, $select_query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $stored_otp = $row["admin_otp"];
        $otp_trials = $row["admin_otp_trials"];

        // Check if entered OTP matches the stored OTP
        if ($entered_otp == $stored_otp) {
            // OTP verification successful
            $reset_trials_query = "UPDATE admin_sign_in SET admin_otp_trials = 0 WHERE admin_id = $admin_id";
            mysqli_query($connect, $reset_trials_query);
            $incorrect_otp_message = "OTP Verified!";
        } else {
            // Increment OTP trials
            $otp_trials++;
            // Update the database with the new trials count
            $update_trials_query = "UPDATE admin_sign_in SET admin_otp_trials = $otp_trials WHERE admin_id = $admin_id";
            mysqli_query($connect, $update_trials_query);

            $incorrect_otp_message = "Wrong OTP!";

            // Check if trials exceed the limit
            if ($otp_trials >= $max_trials) {
                // Redirect the user to the login page
                $reset_trials_query = "UPDATE admin_sign_in SET admin_otp_trials = 0 WHERE admin_id = $admin_id";
                mysqli_query($connect, $reset_trials_query);
                $incorrect_otp_message = "Reached Maximum OTP Trials!";
            }
        }
    } else {
        // Error retrieving OTP and trials from the database
        $incorrect_otp_message = "Error: " . mysqli_error($connect);
    }
}

// Check if OTP resend request is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["admin_resend_otp"])) {
    include("admin_otp_generation.php");
    $reset_trials_query = "UPDATE admin_sign_in SET admin_otp_trials = 0 WHERE admin_id = $admin_id";
    mysqli_query($connect, $reset_trials_query);
    $incorrect_otp_message = "OTP Resent!";
}

// Redirect to logout page if cancel button is clicked
if (isset($_GET['cancel_button'])) {
    $reset_trials_query = "UPDATE admin_sign_in SET admin_otp_trials = 0 WHERE admin_id = $admin_id";
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
    <link rel="stylesheet" type="text/css" href="text-style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style>
        .otp-text {
            display: flex;
            justify-content: flex-start;
        }

        .overlay {
            position: fixed;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            z-index: 999;
            display: <?php echo (!empty($incorrect_otp_message)) ? 'flex' : 'none'; ?>;
            align-items: flex-start;
            justify-content: center;
            margin-top: 58px;
        }

        .notification {
            width: 500px;
            height: 150px;
            background-color: #D9D9D9;
            color: maroon;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            display: flex;
            flex-direction: column;
        }

        .notification h2 {
            margin-top: 0;
        }

        .button-container4 {
            margin-top: auto;
            display: flex;
            justify-content: flex-end;
        }

        /* .ok-button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        } */

        .ok-button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body class="bagongpgalengke-v2 ">
    <header><img src="assets\images\sign-in\Santa-Rosa-Logo.svg" class="logo-src"></header>

    <!-- Add the overlay section -->
    <div class="overlay">
        <div class="notification">
            <h2 class="otp-text"><?php echo $incorrect_otp_message; ?></h2>
            <div class="button-container4">
                <?php if ($incorrect_otp_message === "OTP Verified!") : ?>
                    <!-- Redirect to admin_index.php if OTP is verified -->
                    <button class="button" onclick="window.location.href = 'admin_index.php';">OK</button>
                <?php else : ?>
                    <!-- Redirect back to admin_login.php to enter correct OTP -->
                    <button class="button" onclick="window.location.href = 'admin_otp_verification.php';">OK</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="website-title-v2">
        <h1 class="title3"> WELCOME TO </h1>
        <h1 class="title4"> SANTA ROSA<br>PUBLIC MARKET</h1>
    </div>
    <div>
        <img class="white-front" src="assets\images\sign-in\white-front.svg" alt="white-front">
        <img class="front-layer-v2" src="assets\images\sign-in\front.svg" alt="front">
        <img class="back-layer-v2" src="assets\images\sign-in\back.svg" alt="back">
    </div>
    <div class="login-form">
        <form class="form-group" action="" method="post">
            <div class="flexbox-row">
                <div>
                    <input class="input-box" type="text" pattern="[0-9]{6}" maxlength="6" id="otp" name="admin_otp" title="Please enter six numbers" placeholder="Enter OTP" required>
                </div>
                <div>
                    <button class="verify-button" type="submit">Verify OTP</button> <br>
                </div>
            </div>
            <span id="error-message" style="color: red">
                <?php echo $incorrect_otp_message; ?>
            </span>
            <div class="buttons-container">
                <form action="" method="post" id="resendOTPForm">
                    <input type="hidden" id="wrongOTP" value="<?php echo ($incorrect_otp_message === "Wrong OTP!") ? 'true' : 'false'; ?>">
                    <button class="resend-button" type="submit" id="resendOTPButton" name="admin_resend_otp" <?php echo ($incorrect_otp_message === "Wrong OTP!") ? '' : 'disabled'; ?>>Resend OTP</button>
                    <div id="resendTokenMessage" class="timer-message"></div>
                    <br>
                    <a class="back-button1" href="admin_login.php">Back</a>
                </form>
            </div>
        </form>
    </div>
    <script>
        function hideAlert() {
            var overlay = document.querySelector('.overlay');
            overlay.style.display = 'none';
        }
        // Hide overlay and alert message
        function hideAlert() {
            var overlay = document.getElementById('overlay');
            overlay.style.display = 'none';
        }

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

        // Function to start the countdown timer
        function startTimer(duration, display) {
            var timer = duration;
            setInterval(function() {
                display.textContent = timer;
                if (--timer < 0) {
                    timer = 0;
                }
            }, 1000);
        }

        // Check if resend OTP timer session is set
        <?php if (isset($_SESSION['resend_otp_timer'])) : ?>
            var endTime = <?php echo $_SESSION['resend_otp_timer']; ?>;
            var now = <?php echo time(); ?>;
            var cooldownTime = endTime - now;
            if (cooldownTime > 0) {
                var resendTokenMessage = document.getElementById('resendTokenMessage');
                startTimer(cooldownTime, resendTokenMessage);
            }
        <?php endif; ?>
    </script>
</body>

</html>