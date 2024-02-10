<?php
$connect = mysqli_connect("localhost", "root", "", "bagong_palengke_db");

if ($connect === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
$vendor_email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vendor_new_password = isset($_POST["vendor_new_password"]) ? htmlspecialchars($_POST["vendor_new_password"]) : '';
    $vendor_confirm_new_password = isset($_POST["vendor_confirm_new_password"]) ? htmlspecialchars($_POST["vendor_confirm_new_password"]) : '';

    if ($vendor_new_password !== $vendor_confirm_new_password) {
        echo '<script>';
        echo 'alert("Passwords do not match!");';
        echo 'window.location.href = "vendor_forgot_password_2.php";';
        echo '</script>';
        exit();
    }

    $hashedPassword = password_hash($vendor_new_password, PASSWORD_BCRYPT);

    $password_query = "UPDATE vendor_sign_in SET vendor_password = ? WHERE vendor_email = ?";
    $stmt = mysqli_prepare($connect, $password_query);

    // Use "ss" for two string parameters
    $stmt->bind_param("ss", $hashedPassword, $vendor_email);

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        include("vendor_forgot_password_3.php");
        echo '<script>';
        echo 'alert("Password Updated!");';
        echo 'window.location.href = "vendor_login.php";';
        echo '</script>';
    } else {
        echo '<script>';
        echo 'alert("Password is the same with the previous!");';
        echo 'window.location.href = "vendor_login.php";';
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
        function validateForm() {
            return (
                validatePassword() &&
                checkPasswordMatch()
            );
        }

        function validatePassword() {
            var passwordInput = document.getElementById("vendor_new_password");
            var password = passwordInput.value;
            var passwordValidationMessage = document.getElementById("passwordValidationMessage");

            // Define the password patterns
            var lengthPattern = /.{8,16}/;
            var uppercasePattern = /[A-Z]/;
            var lowercasePattern = /[a-z]/;
            var digitPattern = /\d/;
            var specialCharPattern = /[!@#$%^&*()_+]/;

            // Check each pattern and provide feedback
            var isValid = true;
            if (!lengthPattern.test(password)) {
                isValid = false;
                passwordValidationMessage.textContent = "Password must be 8-16 characters.";
            } else if (!uppercasePattern.test(password)) {
                isValid = false;
                passwordValidationMessage.textContent = "Include at least one uppercase letter.";
            } else if (!lowercasePattern.test(password)) {
                isValid = false;
                passwordValidationMessage.textContent = "Include at least one lowercase letter.";
            } else if (!digitPattern.test(password)) {
                isValid = false;
                passwordValidationMessage.textContent = "Include at least one number.";
            } else if (!specialCharPattern.test(password)) {
                isValid = false;
                passwordValidationMessage.textContent = "Include at least one special character from the list ! @ # $ % ^ & * ( ) _ +";
            } else {
                passwordValidationMessage.textContent = "";
            }

            return isValid;
        }

        function checkPasswordMatch() {
            var password = document.getElementsByName("vendor_new_password")[0].value;
            var confirmPassword = document.getElementsByName("vendor_confirm_new_password")[0].value;
            var messageElement = document.getElementById("passwordMatchMessage");
            var confirmPasswordInput = document.getElementsByName("vendor_confirm_new_password")[0];

            // Enable or disable Confirm Password based on whether Password is empty
            confirmPasswordInput.disabled = password.length === 0;

            // Check if the "Password" field is not empty
            if (password.length > 0) {
                // Check if the "Confirm Password" field is also not empty
                if (confirmPassword.length > 0) {
                    // Check if passwords match
                    if (password === confirmPassword) {
                        messageElement.innerHTML = "Passwords match";
                        messageElement.style.color = "green";
                    } else {
                        messageElement.innerHTML = "Passwords do not match.";
                        messageElement.style.color = "red";
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

            return password === confirmPassword;
        }

        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("vendor_new_password");
            var showPasswordCheckbox = document.getElementById("showPassword");

            // Toggle the password visibility
            passwordInput.type = showPasswordCheckbox.checked ? "text" : "password";
        }

        function updateSubmitButton() {
            var submitButton = document.querySelector('button[type="submit"]');
            var formIsValid = validatePassword() && checkPasswordMatch();
            submitButton.disabled = !formIsValid;

            console.log("Update submit button called. Form is valid: ", formIsValid);
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
        <!-- <form class="form-group" action="" method="post" onsubmit="return confirm('Proceed?');">

            <div class="flex-row">
                <label class="label2" for="admin_new_password">New Password:</label>
                <input class="input-box" type="password" name="admin_new_password" id="admin_new_password" oninput="checkPasswordMatch()">
            </div>

            <div class="flex-row">
                <label class="label3" for="new_password">Confirm Password:</label>
                <input class="input-box" type="password" name="admin_confirm_new_password" id="admin_confirm_new_password" required oninput="checkPasswordMatch()">
            </div>
            <div class="notification">
                <span id="passwordMatchMessage"></span>
            </div>
            <input class="update-password-button" type="submit" value="Update Password" disabled>
        </form> -->
        <form class="form-group" action="" method="post" onsubmit="return validateForm()">
            <div class="flex-row">
                <label class="label2" for="vendor_new_password">New Password:</label>
                <input class="input-box" type="password" name="vendor_new_password" id="vendor_new_password" maxlength="16" placeholder="8-16 characters" oninput="validatePassword(); checkPasswordMatch(); updateSubmitButton()">
                <input type="checkbox" id="showPassword" onclick="togglePasswordVisibility()">
                <label for="showPassword">Show Password</label>
                <span style="color: red;" id="passwordValidationMessage"></span><br />
            </div>
            <div class="flex-row">
                <label class="label3" for="vendor_confirm_new_password">Confirm Password:</label>
                <input class="input-box" type="password" name="vendor_confirm_new_password" id="vendor_confirm_new_password" maxlength="16" required oninput="checkPasswordMatch(); updateSubmitButton()">
                <span id="passwordMatchMessage"></span><br />
            </div>
            <button class="update-password-button" type="submit" disabled>Update Password</button>
        </form>
    </div>
</body>

</html>