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
  <title>SIGN IN</title>
  <link rel="stylesheet" type="text/css" href="index.css">
  <link rel="javascript" type="text/javascript" href="js-style.js">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body class="bagongpgalengke-v2">
  <header><img src="assets\images\sign-in\Santa-Rosa-Logo.svg" class="logo-src"></header>

  <div class="website-title-v2">
    <h1 class="title4"> FORGOT<br>PASSWORD?</h1>
  </div>

  <div>
    <img class="white-front" src="assets\images\sign-in\white-front.svg" alt="white-front">
    <img class="front-layer-v2" src="assets\images\sign-in\front.svg" alt="front">
    <img class="back-layer-v2" src="assets\images\sign-in\back.svg" alt="back">
  </div>

  <div class="login-form">
    <h2>Forgot Password?</h2>
    <form class="form-group" action="" method="post">
        <label for="Admin User ID">Treasury User ID:</label>
        <input type="text" name="admin_userid" required> <br />
        <label for="email">Email:</label>
        <input type="email" name="admin_email" required> <br />
        <button class="login-verif" type="submit"> SUBMIT </button><br />
    </form>

    <a class="" href="admin_login.php"> Back</a>
  </div>
  <footer> </footer>
</body>


</html>
