<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];

?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Email OTP Simulation</title>
    </head>

    <body>
        <h2 align="center">Remember your OTP, <?php echo $userid  ?>! </h2>


        <h1 style="color: green;" align="center">
            <?php
            if (isset($_SESSION['admin_otp_message'])) {
                echo $_SESSION['admin_otp_message'];
                unset($_SESSION['admin_otp_message']);
            }
            ?>
        </h1>


        <a href=admin_otp_verification.php>
            <h2 align="center">Proceed</h2>
        </a>

        <a href=admin_logout.php>
            <h5 align="right">LOGOUT</h5>
        </a>
    </body>


    </html>
<?php } else {
    header("location:admin_login.php");
}
