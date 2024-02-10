<?php
require("config.php");

// Initialize variables
$wrong_credentials = "";
$otp_generated = false;

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["admin_login_submit"])) {
  // Check if user ID and password are provided
  if (empty($_POST['admin_userid']) || empty($_POST['admin_password'])) {
    $wrong_credentials = "Input Needed!";
  } else {
    $admin_userid = htmlspecialchars($_POST["admin_userid"]);
    $admin_password = htmlspecialchars($_POST["admin_password"]);

    // Check user credentials in the database
    $result = mysqli_query($connect, "SELECT * FROM admin_sign_in WHERE admin_userid = '$admin_userid'");
    $row = mysqli_fetch_assoc($result);

    if (mysqli_num_rows($result) > 0) {
      // Verify password
      if (password_verify($admin_password, $row["admin_password"])) {

        $_SESSION["login"] = true;
        $_SESSION["id"] = $row["admin_id"];
        $_SESSION["userid"] = $row["admin_userid"];
        $_SESSION["admin_email"] = $row["admin_email"];


        $admin_userid = $_SESSION["userid"];
        include("admin_otp_generation.php");

        // Set OTP generated flag
        $otp_generated = true;
      } else {
        $wrong_credentials = "Wrong Credentials!";
      }
    } else {
      $wrong_credentials = "Wrong Credentials!";
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Sign in</title>
  <link rel="stylesheet" type="text/css" href="index.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <style>
    .otp-text {
      /* float: left; */
      display: flex;
      justify-content: flex-start;
    }

    .overlay {
      position: fixed;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(10px);
      z-index: 999;
      display: <?php echo ($wrong_credentials || $otp_generated) ? 'flex' : 'none'; ?>;
      align-items: flex-start;
      justify-content: center;
      margin-top: 58px;
    }

    .notification {
      width: 500px;
      height: 150px;
      background-color: #D9D9D9;
      color: maroon;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      display: flex;
      flex-direction: column;
    }

    .notification h2 {
      margin-top: 0;
    }

    .button-container4 {
      margin-top: auto;
      /* Pushes the button to the bottom */
      display: flex;
      justify-content: flex-end;
      /* Aligns the button to the right */
    }
  </style>
</head>

<body class="bagongpgalengke-v2 body1">
  <header>
    <img src="assets\images\sign-in\Santa-Rosa-Logo.svg" class="logo-src">
  </header>

  <div class="overlay">
    <div class="notification">
      <?php if ($wrong_credentials) : ?>
        <h2><?php echo $wrong_credentials; ?></h2>
        <div class="button-container4">
          <button class="button" onclick="dismissNotification();">OK</button>
        </div>
      <?php elseif ($otp_generated) : ?>
        <div class="otp-text">
          <h2>OTP Generated!</h2>
        </div>
        <div class="button-container4">
          <button class="button" onclick="window.location.href = 'admin_otp_verification.php';">OK</button>
        </div>
      <?php endif; ?>
    </div>
  </div>



  <div>
    <img class="white-front" src="assets\images\sign-in\white-front.svg" alt="white-front">
    <img class="front-layer-v2" src="assets\images\sign-in\front.svg" alt="front">
    <img class="back-layer-v2" src="assets\images\sign-in\back.svg" alt="back">
  </div>

  <div class="website-title-v2">
    <h1 class="title3"> WELCOME TO </h1>
    <h1 class="title4"> SANTA ROSA<br>PUBLIC MARKET</h1>
  </div>

  <div class="login-form ">
    <form class="form-group" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" autocomplete="off">
      <div>
        <input class="input-box" type="text" name="admin_userid" id="admin_userid" placeholder="Admin User ID" required value=""> <br />
        <input class="input-box" type="password" id="password" placeholder="PASSWORD" name="admin_password" id="admin_password" required value="">
      </div>
      <span class="error-message">
        <?php
        if (isset($_SESSION['wrong_credentials'])) {
          echo $_SESSION['wrong_credentials'];
          unset($_SESSION['wrong_credentials']);
        }
        ?>
      </span>
      <a class="forgot-password" href="admin_forgot_password.php"> Forgot Password?</a> <br />
      <div class="buttons-container">
        <button class="login-btn" type="submit" name="admin_login_submit"> LOGIN </button>
        <br>
        <a class="back-button2" href="vendor_admin_select.php">Back</a>
      </div>
    </form>
  </div>

  <script>
    function dismissNotification() {
      document.querySelector('.overlay').style.display = 'none';
    }

    document.querySelector('.toggle-password').addEventListener('click', function() {
      const passwordInput = document.querySelector('#password');
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
    });
  </script>

  <section> </section>
  <footer> </footer>
</body>

</html>