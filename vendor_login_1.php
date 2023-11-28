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


            if (md5($vendor_password) === $row["vendor_password"]) {
                $_SESSION["login"] = true;
                $_SESSION["id"] = $row["vendor_id"];
                $_SESSION["userid"] = $row["vendor_userid"];


                $id = $_SESSION["id"];


                //OTP Generation
                include("vendor_otp_generation.php");
            } else {
                
                echo '<script>';
                echo 'alert("Wrong Credentials");';
                echo 'window.location.href = "vendor_login.php";';
                echo '</script>';
            }
        } else {
            
            echo '<script>';
            echo 'alert("Wrong Credentials!");';
            echo 'window.location.href = "vendor_login.php";';
            echo '</script>';
        }
    }
}
