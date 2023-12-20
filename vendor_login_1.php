<?php
require("config.php");

if (isset($_POST["vendor_login_submit"])) {


    //server side validation ng submit form if empty
    if (empty($_POST['vendor_userid']) || empty($_POST['vendor_password'])) {
        echo '<script>';
        echo 'alert("Input Needed!");';
        echo 'window.location.href = "vendor_login.php";';
        echo '</script>';
    } else {
        $vendor_userid = mysqli_real_escape_string($connect, $_POST["vendor_userid"]);
        $vendor_password = mysqli_real_escape_string($connect, $_POST["vendor_password"]);

        $result = mysqli_query($connect, "SELECT * FROM vendor_sign_in WHERE vendor_userid= '$vendor_userid' ");
        $row = mysqli_fetch_assoc($result);


        if (mysqli_num_rows($result) > 0) {


            if (password_verify($vendor_password, $row["vendor_password"])) {
                $_SESSION["login"] = true;
                $_SESSION["id"] = $row["vendor_id"];
                $_SESSION["userid"] = $row["vendor_userid"];
                $_SESSION["vendor_email"] = $row["vendor_email"];



                $vendor_userid = $_SESSION["userid"];


                //OTP Generation
                include("vendor_otp_generation.php");
                echo '<script>';
                echo 'alert("OTP Generated!");';
                echo 'window.location.href = "vendor_otp_verification.php";';
                echo '</script>';
            } else {
                $wrong_credentials = "Wrong Credentials!";
                $_SESSION['wrong_credentials'] = $wrong_credentials;
                header("Location: vendor_login.php");
            }
        } else {
            $wrong_credentials = "Wrong Credentials!";
            $_SESSION['wrong_credentials'] = $wrong_credentials;
            header("Location: vendor_login.php");
        }
    }
}
