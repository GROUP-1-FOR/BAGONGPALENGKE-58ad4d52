<?php /*
require("config.php");
$wrong_credentials = "";

if (isset($_POST["admin_login_submit"])) {


    //server side validation ng submit form if empty
    if (empty($_POST['admin_userid']) || empty($_POST['admin_password'])) {
        echo '<script>';
        echo 'alert("Input Needed!");';
        echo 'window.location.href = "admin_login.php";';
        echo '</script>';
    } else {
        $admin_userid = htmlspecialchars($_POST["admin_userid"]);
        $admin_password = htmlspecialchars($_POST["admin_password"]);

        $result = mysqli_query($connect, "SELECT * FROM admin_sign_in WHERE admin_userid= '$admin_userid'");
        $row = mysqli_fetch_assoc($result);

        if (mysqli_num_rows($result) > 0) {


            if (password_verify($admin_password, $row["admin_password"])) {
                $_SESSION["login"] = true;
                $_SESSION["id"] = $row["admin_id"];
                $_SESSION["userid"] = $row["admin_userid"];
                $_SESSION["admin_email"] = $row["admin_email"];


                $admin_userid = $_SESSION["userid"];


                //OTP Generation
                include("admin_otp_generation.php");
                echo '<script>';
                echo 'alert("OTP Generated!");';
                echo 'window.location.href = "admin_otp_verification.php";';
                echo '</script>';
            } else {
                $wrong_credentials = "Wrong Credentials!";
                $_SESSION['wrong_credentials'] = $wrong_credentials;
                header("Location: admin_login.php");
            }
        } else {
            $wrong_credentials = "Wrong Credentials!";
            $_SESSION['wrong_credentials'] = $wrong_credentials;
            header("Location: admin_login.php");
        }
    }
}
