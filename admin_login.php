<?php

require("config.php");

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (isset($_SESSION["otp_verified"]) && $_SESSION["otp_verified"] === "vendor") {
  header("Location: vendor_index.php");
} elseif (isset($_SESSION["otp_verified"]) && $_SESSION["otp_verified"] === "admin") {
  header("Location: admin_index.php");
}

?>

<!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIGN IN</title>
  <link rel="stylesheet" type="text/css" href="index.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body class="bagongpgalengke-v2">
  <header> LOGO </header>

  <div class="website-title-v2">
    <h1 class="title3"> WELCOME TO </h1>
    <h1 class="title4"> SANTA ROSA<br>PUBLIC MARKET</h1>
  </div>

  <div>
    <img class="white-front" src="assets\images\sign-in\white-front.svg" alt="white-front">
    <img class="front-layer-v2" src="assets\images\sign-in\front.svg" alt="front">
    <img class="back-layer-v2" src="assets\images\sign-in\back.svg" alt="back">
  </div>

  <div class="login-form">
    <h2>LOGIN</h2>

    <form class="form-group" action="admin_login_1.php" method="post" autocomplete="off">
      <label for="Admin User ID"> Admin User ID: </label>
      <input type="text" name="admin_userid" id="admin_userid" required value=""> <br />
      <label for="Password"> Password: </label>
      <input type="password" id="password" placeholder="PASSWORD" name="admin_password" id="admin_password" required value=""><br />
      <span style="color: red;">
        <?php
        if (isset($_SESSION['wrong_credentials'])) {
          echo $_SESSION['wrong_credentials'];
          // Unset the session variable after displaying the error
          unset($_SESSION['wrong_credentials']);
        }
        ?>
      </span>
      <button class="login-btn" type="submit" name="admin_login_submit"> LOGIN </button>
    </form>



    <a class="forgot-password" href="admin_forgot_password.php"> Forgot Password?</a> <br />
    <a class="" href="vendor_admin_select.php"> Back</a>
  </div>
  <footer> </footer>
</body>


</html>