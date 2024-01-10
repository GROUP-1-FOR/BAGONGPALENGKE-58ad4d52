<?php

require("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

  <style>

.eye {
  position: relative;
  cursor: pointer;
}

.eye::before {
  content: '\1F441'; /* Unicode for eye icon */
  font-size: 18px;
  color: #aaa;
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
}

input[type="password"] {
  padding-right: 30px; /* Ensure the eye icon doesn't overlap with the text */
}
  </style>

</head>

<body class="bagongpgalengke-v2">
<header><img src="assets\images\sign-in\Santa-Rosa-Logo.svg" class="logo-src"></header>

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
      <input class="input-box" type="text" name="admin_userid" id="admin_userid" placeholder="Admin User ID" required value=""> <br />
      <input class="input-box" type="password" id="password" placeholder="PASSWORD" name="admin_password" id="admin_password" required value="">
     
      <span style="color: red;">
        <?php
        if (isset($_SESSION['wrong_credentials'])) {
          echo $_SESSION['wrong_credentials'];
          // Unset the session variable after displaying the error
          unset($_SESSION['wrong_credentials']);
        }
        ?>
      </span>
      <a class="forgot-password" href="admin_forgot_password.php"> Forgot Password?</a> <br />
      <button class="login-btn" type="submit" name="admin_login_submit"> LOGIN </button>
      <a class="back-button2" href="vendor_admin_select.php"> < Back </a>
    </form>

  </div>

  <script>
  document.querySelector('.toggle-password').addEventListener('click', function () {
    const passwordInput = document.querySelector('#password');
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
  });
</script>
  <footer> </footer>
  
</body>




</html>