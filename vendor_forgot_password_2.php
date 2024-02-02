<?php
require("config.php");
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
    <title>Forgot Password</title>
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

            // Check each pattern and collect feedback
            var validationMessages = [];

            if (passwordInput.value != "") {
                if (!lengthPattern.test(password)) {
                    validationMessages.push("Password must be 8-16 characters.");
                }

                if (!uppercasePattern.test(password)) {
                    validationMessages.push("Include at least one uppercase letter.");
                }

                if (!lowercasePattern.test(password)) {
                    validationMessages.push("Include at least one lowercase letter.");
                }

                if (!digitPattern.test(password)) {
                    validationMessages.push("Include at least one number.");
                }

                if (!specialCharPattern.test(password)) {
                    validationMessages.push("Include at least one special character from the list: ! @ # $ % ^ & * ( ) _ +");
                }

            } else {
                return false;
            }
            // Display collected messages
            passwordValidationMessage.innerHTML = validationMessages.map(message => `<p>${message}</p>`).join('');

            // Return validity based on messages
            return validationMessages.length === 0;
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

<body>
    <div>
        <h1>New Password</h1><br />
        <form action="" method="post" onsubmit="return validateForm()">
            <label for="vendor_new_password">New Password:</label>
            <input type="password" name="vendor_new_password" id="vendor_new_password" maxlength="16" placeholder="8-16 characters" oninput="validatePassword(); checkPasswordMatch(); updateSubmitButton()">
            <input type="checkbox" id="showPassword" onclick="togglePasswordVisibility()">
            <label for="showPassword">Show Password</label><br />


            <label for="vendor_confirm_new_password">Confirm Password:</label>
            <input type="password" name="vendor_confirm_new_password" id="vendor_confirm_new_password" maxlength="16" required oninput="checkPasswordMatch(); updateSubmitButton()">
            <span id="passwordMatchMessage"></span><br />
            <hr />
            <br />
            <span style="color: red;" id="passwordValidationMessage"></span><br />
            <hr />


            <button type="submit" disabled>Update Password</button>
        </form>
    </div>


    <div><a href="vendor_login.php">Back</a></div>


</body>

</html>