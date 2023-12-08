<?php
// Include your database connection file
require("config.php");


//token expires after 5 mins
function generateToken($length = 32, $expirationTime = 60)
{
    // Generate a random token
    $token = bin2hex(random_bytes($length));

    // Calculate expiration time (current time + expirationTime)
    $expirationTimestamp = time() + $expirationTime;

    // Append the expiration time to the token
    $expiringToken = $token . '|' . $expirationTimestamp;

    return $expiringToken;
}


// Function to send email with the password reset link


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email address from the form
    $email = htmlspecialchars($_POST["admin_email"]);
    $userid = htmlspecialchars($_POST["admin_userid"]);

    // If the email is valid, generate a unique token

    $result = mysqli_query($connect, "SELECT admin_userid, admin_email FROM admin_sign_in WHERE admin_userid = '$userid' && admin_email= '$email'");
    $row = mysqli_fetch_assoc($result);

    if (mysqli_num_rows($result) > 0) {
        $token = generateToken();
        $email_query = "UPDATE admin_sign_in SET admin_token = ? WHERE admin_userid = ?";
        $stmt = mysqli_prepare($connect, $email_query);

        // Use "ss" for two string parameters
        $stmt->bind_param("ss", $token, $userid);

        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo '<script>';
            echo 'alert("View Email!");';
            echo 'window.location.href = "admin_forgot_password_1.php?userid=' . urlencode($userid) . '";';
            echo '</script>';
        } else {
            echo "Failed to send token.";
            exit();
        }

        // Close the statement
        $stmt->close();
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
                <label for="Admin User ID">Admin User ID:</label>
                <input type="text" name="admin_userid" required> <br />
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