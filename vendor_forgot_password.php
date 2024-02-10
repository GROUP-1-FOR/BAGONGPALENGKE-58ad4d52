<?php
// Include your database connection file
$connect = mysqli_connect("localhost", "root", "", "bagong_palengke_db");

if ($connect === false) {
  die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Function to send email with the password reset link


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Get the email address from the form
  $email = htmlspecialchars($_POST["vendor_email"]);


  function endsWith($haystack, $needle)
  {
    return substr($haystack, -strlen($needle)) === $needle;
  }

  //server side validation
  if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !endsWith($email, "@gmail.com")) {
    echo '<script>';
    echo 'alert("Invalid Email!");';
    echo 'window.location.href = "vendor_forgot_password.php";';
    echo '</script>';
  }

  // If the email is valid, generate a unique token
  $result = mysqli_query($connect, "SELECT vendor_email FROM vendor_sign_in WHERE vendor_email= '$email'");
  $row = mysqli_fetch_assoc($result);

  if (mysqli_num_rows($result) > 0) {
    include('vendor_forgot_password_1.php');
    echo '<script>';
    echo 'alert("Token sent to ' . $email . '");';  // Corrected concatenation
    echo 'window.location.href = "vendor_token_verification_forgot_password.php?email=' . urlencode($email) . '";';
    echo '</script>';
  } else {
    echo '<script>';
    echo 'alert("Vendor User Not Found!");';
    echo 'window.location.href = "vendor_forgot_password.php";';
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
    <h1 class="title5"> FORGOT<br>PASSWORD?</h1>
  </div>

  <div>
    <img class="white-front" src="assets\images\sign-in\white-front.svg" alt="white-front">
    <img class="front-layer-v2" src="assets\images\sign-in\front.svg" alt="front">
    <img class="back-layer-v2" src="assets\images\sign-in\back.svg" alt="back">
  </div>

  <div class="login-form">
    <form class="form-group" action="" method="post">
      <div>
        <input class="input-box" type="email" name="vendor_email" maxlength="254" oninput="validateEmail()" placeholder="Email" required>
      </div>

      <div class="error-message" id="emailValidationMessage"></div>
      <div class=" buttons-container">
        <input class="send-verif" type="submit" id="submitBtn" value="Send Verification"><br>
        <a class="back-button1" href="admin_login.php">Back </a>
      </div>

    </form>
  </div>



  <script>
    // JavaScript
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
        messageElement.classList.remove("invalid-email-message");
        messageElement.classList.add("valid-email-message");
        // Enable the submit button
        document.getElementById("submitBtn").disabled = false;
      } else {
        messageElement.innerHTML = "Only '@gmail.com' is accepted";
        messageElement.classList.remove("valid-email-message");
        messageElement.classList.add("invalid-email-message");
        // Disable the submit button
        document.getElementById("submitBtn").disabled = true;
      }
    }
  </script>
  <footer> </footer>
</body>


</html>