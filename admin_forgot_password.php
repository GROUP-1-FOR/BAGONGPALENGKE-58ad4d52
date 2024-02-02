<?php
// Include your database connection file
require("config.php");


// Function to send email with the password reset link
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email address from the form
    $email = htmlspecialchars($_POST["admin_email"]);


    function endsWith($haystack, $needle)
    {
        return substr($haystack, -strlen($needle)) === $needle;
    }

    //server side validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !endsWith($email, "@gmail.com")) {
        echo '<script>';
        echo 'alert("Invalid Email!");';
        echo 'window.location.href = "admin_forgot_password.php";';
        echo '</script>';
    }

    // If the email is valid, generate a unique token
    $result = mysqli_query($connect, "SELECT admin_email FROM admin_sign_in WHERE admin_email= '$email'");
    $row = mysqli_fetch_assoc($result);

    if (mysqli_num_rows($result) > 0) {
        include('admin_forgot_password_1.php');
        echo '<script>';
        echo 'alert("Token sent to ' . $email . '");';  // Corrected concatenation
        echo 'window.location.href = "admin_token_verification_forgot_password.php?email=' . urlencode($email) . '";';
        echo '</script>';
    } else {
        echo '<script>';
        echo 'alert("Admin User Not Found!");';
        echo 'window.location.href = "admin_forgot_password.php";';
        echo '</script>';
    }

    $connect->close();
}
?>

<!-- HTML form for the forgot password section -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script>
        //client side validation
        function validateEmail() {
            // Get the email input value
            var emailInput = document.forms["email_form"]["admin_email"].value;

            // Validate the email format using a regular expression
            var emailRegex = /^[^\s@]+@gmail\.com$/;

            // Get the element to display validation messages
            var messageElement = document.getElementById("emailValidationMessage");

            // Display validation message
            if (emailRegex.test(emailInput)) {
                messageElement.innerHTML = "Valid email address";
                messageElement.style.color = "green";
                // Enable the submit button
                document.getElementById("submitBtn").disabled = false;
            } else {
                messageElement.innerHTML = "Only '@gmail.com' is accepted";
                messageElement.style.color = "red";
                // Disable the submit button
                document.getElementById("submitBtn").disabled = true;
            }
        }
    </script>
</head>

<body>
    <div align="center">
        <div>
            <h2>Forgot Password?</h2>
            <form name="email_form" action="" method="post">
                <label for="email">Email:</label>
                <input type="email" name="admin_email" maxlength="254" oninput="validateEmail()">
                <div id="emailValidationMessage"></div>
                <br />
                <input type="submit" id="submitBtn" value="Send Verification" disabled>
            </form>
        </div>

        <div>
            <a href=admin_login.php>
                <h2>Back</h2> <br />
            </a>
        </div>
    </div>
</body>

</html>