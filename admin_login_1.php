<?php
require("config.php");

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
        $hashedPassword = md5($admin_password);

        $result = mysqli_query($connect, "SELECT * FROM admin_sign_in WHERE admin_userid= '$admin_userid'");
        $row = mysqli_fetch_assoc($result);


        if (mysqli_num_rows($result) > 0) {


            if ($hashedPassword == $row["admin_password"]) {
                $_SESSION["login"] = true;
                $_SESSION["id"] = $row["admin_id"];
                $_SESSION["userid"] = $row["admin_userid"];


                $id = $_SESSION["id"];


                //OTP Generation
                include("admin_otp_generation.php");
            } else {
                echo '<script>';
                echo 'alert("Wrong Credentials");';
                echo 'window.location.href = "admin_login.php";';
                echo '</script>';
            }
        } else {
            echo '<script>';
            echo 'alert("Wrong Credentials!");';
            echo 'window.location.href = "admin_login.php";';
            echo '</script>';
        }
    }
}
