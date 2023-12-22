<?php
require("config.php");
$admin_email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';

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

    $password_query = "UPDATE admin_sign_in SET admin_password = ? WHERE admin_email = ?";
    $stmt = mysqli_prepare($connect, $password_query);

    // Use "ss" for two string parameters
    $stmt->bind_param("ss", $hashedPassword, $admin_email);

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
    <title>Forgot Password</title>
    <script>
        function validateForm() {
            return (
                validatePassword() &&
                checkPasswordMatch()
            );
        }

        function validatePassword() {
            var passwordInput = document.getElementById("admin_new_password");
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
            var password = document.getElementsByName("admin_new_password")[0].value;
            var confirmPassword = document.getElementsByName("admin_confirm_new_password")[0].value;
            var messageElement = document.getElementById("passwordMatchMessage");
            var confirmPasswordInput = document.getElementsByName("admin_confirm_new_password")[0];

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
            var passwordInput = document.getElementById("admin_new_password");
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

<body>
    <div>
        <h1>New Password</h1><br />
        <form action="" method="post" onsubmit="return validateForm()">
            <label for="admin_new_password">New Password:</label>
            <input type="password" name="admin_new_password" id="admin_new_password" maxlength="16" placeholder="8-16 characters" oninput="validatePassword(); checkPasswordMatch(); updateSubmitButton()">
            <input type="checkbox" id="showPassword" onclick="togglePasswordVisibility()">
            <label for="showPassword">Show Password</label>
            <span style="color: red;" id="passwordValidationMessage"></span><br />

            <label for="admin_confirm_new_password">Confirm Password:</label>
            <input type="password" name="admin_confirm_new_password" id="admin_confirm_new_password" maxlength="16" required oninput="checkPasswordMatch(); updateSubmitButton()">
            <span id="passwordMatchMessage"></span><br />

            <button type="submit" disabled>Update Password</button>
        </form>
    </div>


    <div><a href="admin_login.php">Back</a></div>


</body>

</html>