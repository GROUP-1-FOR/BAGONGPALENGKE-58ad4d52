<?php
require("config.php");
$email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
$admin_token_error = "";

if (isset($_POST["admin_token"])) {
    $entered_token = htmlspecialchars($_POST["admin_token"]);

    if (strlen($entered_token) !== 6 ||  !ctype_alnum($entered_token)) {
        echo '<script>';
        echo 'alert("Invalid Token Format!");';
        echo 'window.location.href = "admin_token_verification_forgot_password.php";';
        echo '</script>';
    }

    $select_query = "SELECT admin_token FROM admin_sign_in WHERE admin_email = '$email'";
    $result = mysqli_query($connect, $select_query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $stored_token = $row["admin_token"];

        // Check if entered OTP matches the stored OTP
        if ($entered_token == $stored_token) {
            echo '<script>';
            echo 'alert("Token Verified!");';
            echo 'window.location.href = "admin_forgot_password_2.php?email=' . urlencode($email) . '";';
            echo '</script>';
        } else {
            $admin_token_error = "Wrong Token";
        }
    } else {
        // Error retrieving OTP and trials from the database
        echo "Error: " . mysqli_error($connect);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["admin_resend_token"])) {
    include("admin_forgot_password_1.php");
    echo '<script>';
    echo 'alert("Token Resent!");';
    echo '</script>';
}

?>
<!-- HTML form for the forgot password section -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGN IN</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="javascript" type="text/javascript" href="js-style.js">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        function validateInput() {
            var tokenInput = document.getElementById("token");
            var errorMessage = document.getElementById("error-message");
            var submitButton = document.getElementById("submit-button");

            // Validate the token length
            if (tokenInput.value.length !== 6) {
                errorMessage.innerHTML = "Please enter a six-digit token.";
                submitButton.disabled = true;
            } else if (!/^[a-zA-Z0-9]+$/.test(tokenInput.value)) {
                errorMessage.innerHTML = "No special characters.";
                submitButton.disabled = true;
            } else {
                errorMessage.innerHTML = "";
                submitButton.disabled = false;
            }
        }
    </script>
</head>

<body class="bagongpgalengke-v2">
    <header><img src="assets\images\sign-in\Santa-Rosa-Logo.svg" class="logo-src"></header>

    <div class="website-title-v2">
        <h1 class="title5"> FORGOT<br>PASSWORD?</h1>
    </div>

    <div>
        <img class="white-front" src="assets\images\sign-in\white-front.svg" alt="white-front">
        <img class="front-layer-v2" src="assets\images\sign-in\front.svg" alt="front">
        <img class="back-layer-v2" src="assets\images\sign-in\back.svg" alt="back">
    </div>

    <div class="login-form">


        <!-- <form class="form-group" name="email_form" action="" method="post">
      <input class="input-box" type="email" name="admin_email" placeholder="Email" maxlength="254" oninput="validateEmail()" required>
      <div id="emailValidationMessage"></div>
      <button class="send-verif" type="submit" id="submitBtn"> Send Verification </button>
    </form> -->
        <form class="form-group" action="" method="post">
            <div class="flexbox-row">
                <div>
                    <input class="input-box" type="text" maxlength="6" id="token" name="admin_token" title="Please enter six characters" placeholder="Enter Token" required oninput="validateInput()">
                </div>
                <div>
                    <button class="verify-button" id="submit-button" type="submit" disabled>Verify</button> <br />
                </div>
            </div>

            <span id="error-message" style="color: red">
                <?php echo $admin_token_error; ?>
            </span>
        </form>

        <div class="buttons-container">
            <form class="" action="" method="post" id="resendTokenForm">
                <!-- <div id="resendTokenMessage"></div> -->
                <button class="login-btn" type="submit" id="resendTokenButton" name="admin_resend_token" disabled>Resend</button>
                <div id="resendTokenMessage" class="timer-message"></div>
                <br>
                <a class="back-button1" href="admin_login.php">Back</a>
            </form>
        </div>

    </div>
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
                    $("#resendTokenButton").prop("disabled", false);
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