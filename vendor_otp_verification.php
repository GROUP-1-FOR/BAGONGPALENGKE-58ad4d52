<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];


    if (isset($_POST["vendor_otp"])) {
        $entered_otp = htmlspecialchars($_POST["vendor_otp"]);
        $max_trials = 3;
        $incorrect_otp_message = "";


        // Retrieve the stored OTP and trials from the database
        $select_query = "SELECT vendor_otp, vendor_otp_trials FROM vendor_sign_in WHERE vendor_id = $id";
        $result = mysqli_query($connect, $select_query);
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            $stored_otp = $row["vendor_otp"];
            $otp_trials = $row["vendor_otp_trials"];

            // Check if entered OTP matches the stored OTP
            if ($entered_otp == $stored_otp) {
                // OTP verification successful
                $reset_trials_query = "UPDATE vendor_sign_in SET vendor_otp_trials = 0 WHERE vendor_id = $id";
                mysqli_query($connect, $reset_trials_query);
                echo '<script>';
                echo 'alert("OTP Verified!");';
                echo 'window.location.href = "vendor_index.php";';
                echo '</script>';
            } else {

                // Increment OTP trials
                $otp_trials++;
                // Update the database with the new trials count
                $update_trials_query = "UPDATE vendor_sign_in SET vendor_otp_trials = $otp_trials WHERE vendor_id = $id";

                mysqli_query($connect, $update_trials_query);

                $incorrect_otp_message = "Wrong OTP!";
                echo $incorrect_otp_message;

                // Check if trials exceed the limit
                if ($otp_trials >= $max_trials) {
                    // Redirect the user to the login page
                    $reset_trials_query = "UPDATE vendor_sign_in SET vendor_otp_trials = 0 WHERE vendor_id = $id";
                    mysqli_query($connect, $reset_trials_query);
                    echo "<script>window.location.href='vendor_login.php';</script>";
                    exit();
                }
            }
        } else {
            // Error retrieving OTP and trials from the database
            echo "Error: " . mysqli_error($connect);
        }
    }
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>VENDOR OTP Verification</title>
        <link rel="stylesheet" type="text/css" href="index.css">
    </head>

    <body>

        <header class=>
            <h2> LOGO </h2>

            <h1 class="title1"> WELCOME TO </h1>
            <h1 class="title2"> SANTA ROSA PUBLIC MARKET </h1>
            <!--<div class="nav">
      <a href="">HOME</a>
      <a href="">BLOG</a>
      <a href="">About</a>
      <a href="">FAQs</a>
      <a href="">Info</a>
      <a href="">Tricks</a>
      <a href="">LOGIn</a>

    </div> -->
        </header>
        <div class="main-content">
            <h2>OTP Verification</h2>
            <form action="" method="post">
                <label for="otp">Enter OTP:</label>
                <input type="text" pattern="[0-9]{6}" maxlength="6" id="otp" name="vendor_otp" title="Please enter six numbers" placeholder="123456" required>
                <button type="submit">Verify OTP</button>

            </form>

            <a href=vendor_logout.php> CANCEL </a>
        </div>
        <footer>

        </footer>

    </body>

    </html>


<?php
} else {
    header("location:vendor_login.php");
}
