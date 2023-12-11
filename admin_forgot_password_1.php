<?php
require("config.php");

function isTokenValid($token)
{
    // Split the token into the actual token and the expiration timestamp
    $tokenParts = explode('|', $token);

    if (count($tokenParts) === 2) {
        // Extract the expiration timestamp
        $expirationTimestamp = $tokenParts[1];

        // Check if the token is still within the expiration time
        return time() <= $expirationTimestamp;
    }

    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userid = htmlspecialchars($_POST["admin_userid"]);

    $query = "SELECT admin_token FROM admin_sign_in WHERE admin_userid = '$userid'";
    // Perform the query
    $result = mysqli_query($connect, $query);

    if ($result) {
        // Fetch the user data as an associative array
        $admin_token = mysqli_fetch_assoc($result);

        if ($admin_token && isTokenValid($admin_token['admin_token'])) {

            header("Location: admin_forgot_password_2.php?userid=" . urlencode($userid));
            exit();
        } else {

            echo '<script>';
            echo 'alert("Token Expired!");';
            echo 'window.location.href = "admin_login.php";';
            echo '</script>';
        }
    } else {
        // Handle the query error
        echo "Error: " . mysqli_error($connect);
        echo '<script>';
        echo 'alert("Wrong Admin User ID!");';
        echo 'window.location.href = "admin_login.php";';
        echo '</script>';
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
    <h1 class="title4"> Re-enter<br>Treasury User ID</h1>
  </div>

  <div>
    <img class="white-front" src="assets\images\sign-in\white-front.svg" alt="white-front">
    <img class="front-layer-v2" src="assets\images\sign-in\front.svg" alt="front">
    <img class="back-layer-v2" src="assets\images\sign-in\back.svg" alt="back">
  </div>

  <div class="login-form">
    <form class="form-group" action="" onsubmit="return confirm('Proceed?');" method="post">
        <label for="Admin User ID">Treasury User ID:</label>
        <input type="text" name="admin_userid" value="<?php echo $userid = isset($_GET['userid']) ? htmlspecialchars($_GET['userid']) : ''; ?>" required readonly> <br />
        <button class="login-verif" type="submit"> SUBMIT </button><br />
    </form>

    <a class="" href="admin_login.php"> Back</a>
  </div>
  <footer> </footer>
</body>
</html>
