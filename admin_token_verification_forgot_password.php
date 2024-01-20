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

<!DOCTYPE html>
<html>

<head>
    <title>
        Reset Password Token Verification
    </title>
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

<body>
    <div align="center">
        <div>
            <form action="" method="post">
                <input class="otp-box" type="text" maxlength="6" id="token" name="admin_token" title="Please enter six characters" placeholder="Enter Token" required oninput="validateInput()">
                <button class="submit-button" id="submit-button" type="submit" disabled>Verify Token</button> <br />
                <span id="error-message" style="color: red">
                    <?php echo $admin_token_error; ?>
                </span>
            </form>

            <br />
            <div>
                <form action="" method="post" id="resendTokenForm">
                    <button class="resend-button" type="submit" id="resendTokenButton" name="admin_resend_token" disabled>Resend Token</button>
                    <div id="resendTokenMessage"></div>
                </form>
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
        </div>

        <a href=admin_forgot_password.php>
            <h1>Back</h1>
        </a>
    </div>
</body>

</html>