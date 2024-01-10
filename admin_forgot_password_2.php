<?php
require("config.php");
$userid = isset($_GET['userid']) ? htmlspecialchars($_GET['userid']) : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $admin_new_password = isset($_POST["admin_new_password"]) ? htmlspecialchars($_POST["admin_new_password"]) : '';
    $admin_confirm_new_password = isset($_POST["admin_confirm_new_password"]) ? htmlspecialchars($_POST["admin_confirm_new_password"]) : '';

    if ($admin_new_password !== $admin_confirm_new_password) {
        echo '<script>';
        echo 'alert("Passwords do not match!");';
        echo 'window.location.href = "admin_forgot_password_2.php";';
        echo '</script>';
        exit();
    }

    $hashedPassword = password_hash($admin_new_password, PASSWORD_BCRYPT);

    $password_query = "UPDATE admin_sign_in SET admin_password = ? WHERE admin_userid = ?";
    $stmt = mysqli_prepare($connect, $password_query);

    // Use "ss" for two string parameters
    $stmt->bind_param("ss", $hashedPassword, $userid);

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo '<script>';
        echo 'alert("Password Updated!");';
        echo 'window.location.href = "admin_login.php";';
        echo '</script>';
    } else {
        echo '<script>';
        echo 'alert("Password is the same with the previous!");';
        echo 'window.location.href = "admin_login.php";';
        echo '</script>';
        exit();
    }
}


?>

<!DOCTYPE html>
<html>

<head>
<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>FORGOT PASSWORD</title>
  <link rel="stylesheet" type="text/css" href="index.css">
  <link rel="javascript" type="text/javascript" href="js-style.js">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <script>
        function checkPasswordMatch() {
            var password = document.getElementsByName("admin_new_password")[0].value;
            var confirmPassword = document.getElementsByName("admin_confirm_new_password")[0].value;
            var messageElement = document.getElementById("passwordMatchMessage");
            var confirmPasswordInput = document.getElementsByName("admin_confirm_new_password")[0];
            var submitButton = document.querySelector('input[type="submit"]');

            // Enable or disable Confirm Password based on whether Password is empty
            confirmPasswordInput.disabled = password.length === 0;

            // Check if the "Password" field is not empty
            if (password.length > 0) {
                // Check if the "Confirm Password" field is also not empty
                if (confirmPassword.length > 0) {
                    // Check if passwords match
                    if (password === confirmPassword) {
                        // Check if passwords have at least 8 characters
                        if (password.length >= 8 && confirmPassword.length >= 8) {
                            messageElement.innerHTML = "Passwords match and meet the minimum length requirement.";
                            messageElement.style.color = "green";
                            submitButton.disabled = false; // Enable the button
                        } else {
                            messageElement.innerHTML = "Passwords match but do not meet the minimum length requirement (8 characters).";
                            messageElement.style.color = "red";
                            submitButton.disabled = true; // Enable the button
                        }
                    } else {
                        messageElement.innerHTML = "Passwords do not match.";
                        messageElement.style.color = "red";
                        submitButton.disabled = true; // Enable the button
                    }
                } else {
                    // "Confirm Password" field is empty, clear the message
                    messageElement.innerHTML = "";
                }
            } else {
                // "Password" field is empty, clear the message and "Confirm Password" field
                messageElement.innerHTML = "";
                confirmPasswordInput.value = "";
            }

            return password === confirmPassword && password.length >= 8 && confirmPassword.length >= 8;
        }
    </script>

</head>

<body class="bagongpgalengke-v2">
    <header><img src="assets\images\sign-in\Santa-Rosa-Logo.svg" class="logo-src"></header>

    <div class="website-title-v2">
    <h1 class="title5"> New<br>Password</h1>
  </div>

    <div>
        <img class="white-front" src="assets\images\sign-in\white-front.svg" alt="white-front">
        <img class="front-layer-v2" src="assets\images\sign-in\front.svg" alt="front">
        <img class="back-layer-v2" src="assets\images\sign-in\back.svg" alt="back">
    </div>
    <div class="forgot-password-form">
            <form class="form-group" action="" method="post" onsubmit="return confirm('Proceed?');">
        
            <div class="flex-row">
                <label class="label1" for="Admin User ID">Admin User ID:</label>
                <input style="background-color:#D1D0D1;" class="input-box" type="text" name="admin_userid" value="<?php echo $userid; ?>" required readonly>
            </div>

            <div class="flex-row">
                <label class="label2" for="admin_username">New Password:</label>
                <input  class="input-box" type="password" name="admin_new_password" id="admin_new_password" oninput="checkPasswordMatch()">
            </div>

            <div class="flex-row">
            <label class="label3" for="new_password">Confirm Password:</label>
                <input class="input-box" type="password" name="admin_confirm_new_password" id="admin_confirm_new_password" required oninput="checkPasswordMatch()">
            </div>
            <div class="notification">
                <span id="passwordMatchMessage"></span>
            </div>
            <input class="update-password-button" type="submit" value="Update Password" disabled>
        </form>
        


    </div>
</body>

</html>