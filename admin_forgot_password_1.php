<?php

$connect = mysqli_connect("localhost", "root", "", "bagong_palengke_db");

if ($connect === false) {
  die("ERROR: Could not connect. " . mysqli_connect_error());
}

$select_query_FetchAdminUserId = "SELECT admin_userid FROM admin_sign_in WHERE admin_email = '$email'";
$resultFetchAdminUserId = mysqli_query($connect, $select_query_FetchAdminUserId);
$rowFetchAdminUserId = mysqli_fetch_assoc($resultFetchAdminUserId);

if ($rowFetchAdminUserId) {
  // Admin user found, you can access $rowFetchAdminUserId['admin_userid'] for further processing
  $admin_userid = $rowFetchAdminUserId['admin_userid'];
} else {
  // Admin user not found, display alert and redirect
  echo '<script>';
  echo 'alert("Admin User Not Found!");';
  echo 'window.location.href = "admin_forgot_password.php";';
  echo '</script>';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

generateAndSaveToken($admin_userid, $connect);


$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'argojosafor@gmail.com';
$mail->Password = 'tuymblznanvyhppt';

$mail->SMTPSecure = 'ssl';
$mail->Port = 465;

$mail->setFrom('argojosafor@gmail.com');
$mail->addAddress($email);
$mail->isHTML(true);
$mail->Subject = 'Reset Password Token';
$otp_token = $_SESSION["reset_password_token"];

unset($_SESSION["reset_password_token"]);

$mail->addEmbeddedImage('santarosapublicmarket.png', 'logo');
$mail->Body = '<div style="text-align: center;">
                  <img src="cid:logo" alt="Santa Rosa Public Market"><br />
                  <p style="color: #008000; font-size: 20px;">' . $otp_token . '</p>
                  is your Reset Password Token. For your protection, do not share this code with anyone.
               </div>';


$mail->send();



function generateAndSaveToken($admin_userid, $connect)
{
  // Generate a random 6-character alphanumeric token
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $token_length = 6;
  $admin_token = '';

  for ($i = 0; $i < $token_length; $i++) {
    $admin_token .= $characters[rand(0, strlen($characters) - 1)];
  }

  // Update the database with the generated token
  $token_query = "UPDATE admin_sign_in SET admin_token = ? WHERE admin_userid = ?";
  $stmt = mysqli_prepare($connect, $token_query);

  // Check if the statement was prepared successfully
  if ($stmt) {
    // Bind the parameters
    mysqli_stmt_bind_param($stmt, "ss", $admin_token, $admin_userid);

    // Execute the statement
    mysqli_stmt_execute($stmt);

    $admin_token_message = $admin_token;
    $_SESSION['reset_password_token'] = $admin_token_message;

    // Check for success or failure
    if (mysqli_stmt_affected_rows($stmt) == 0) {
      echo "Failed to Generate Token!";
      exit();
    }

    // Close the statement
    mysqli_stmt_close($stmt);
  } else {
    echo "Error: " . mysqli_error($connect);
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FORGOT PASWORD</title>
  <link rel="stylesheet" type="text/css" href="index.css">
  <link rel="javascript" type="text/javascript" href="js-style.js">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body class="bagongpgalengke-v2">
  <header><img src="assets\images\sign-in\Santa-Rosa-Logo.svg" class="logo-src"></header>

  <div class="website-title-v2">
    <h1 class="title5">Re-enter<br>Treasury User ID</h1>
  </div>

  <div>
    <img class="white-front" src="assets\images\sign-in\white-front.svg" alt="white-front">
    <img class="front-layer-v2" src="assets\images\sign-in\front.svg" alt="front">
    <img class="back-layer-v2" src="assets\images\sign-in\back.svg" alt="back">
  </div>

  <div class="forgot-password-form">
    <form class="form-group" action="" onsubmit="return confirm('Proceed?');" method="post">
      <input style="background-color:#D1D0D1;" class="input-box" type="text" name="admin_userid" placeholder="Treasury User ID" value="<?php echo $userid = isset($_GET['userid']) ? htmlspecialchars($_GET['userid']) : ''; ?>" required readonly> <br />
      <button class="login-verif" type="submit"> ENTER </button><br />
    </form>

    <a class="back-button1" href="admin_login.php">
      < Back </a>
  </div>
  <footer> </footer>
</body>

</html>