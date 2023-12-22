<?php
// Include your database connection file
require("config.php");


// Function to send email with the password reset link


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email address from the form
    $email = htmlspecialchars($_POST["admin_email"]);

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
</head>

<body>
    <div align="center">
        <div>
            <h2>Forgot Password?</h2>
            <form action="" method="post">
                <label for="email">Email:</label>
                <input type="email" name="admin_email" required> <br />
                <input type="submit" value="Send Verification"><br />
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