<?php
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $vendor_id = $_SESSION["id"];
    $vendor_userid = $_SESSION["userid"];
} else {
    header("location:vendor_logout.php");
}

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (isset($_SESSION["otp_verified"]) && $_SESSION["otp_verified"] === "vendor") {
} elseif (isset($_SESSION["otp_verified"]) && $_SESSION["otp_verified"] === "admin") {
    header("Location: admin_index.php");
}
