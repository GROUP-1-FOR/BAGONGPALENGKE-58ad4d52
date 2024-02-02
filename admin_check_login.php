<?php
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];
} else {
    header("location:admin_logout.php");
}



if (isset($_SESSION["otp_verified"]) && $_SESSION["otp_verified"] === "vendor") {
    header("Location: vendor_index.php");
} elseif (isset($_SESSION["otp_verified"]) && $_SESSION["otp_verified"] === "admin") {
}
