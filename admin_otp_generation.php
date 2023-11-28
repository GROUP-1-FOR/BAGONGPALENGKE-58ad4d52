<?php

$random_numbers = [];
for ($i = 0; $i < 6; $i++) {
    $random_numbers[] = rand(0, 9);
}

$admin_otp = implode('', $random_numbers);
$otp_query = "UPDATE admin_sign_in SET admin_otp = $admin_otp WHERE admin_id = $admin_id";
$stmt = mysqli_prepare($connect, $otp_query);
if (mysqli_stmt_execute($stmt)) {
    echo '<script>';
    echo 'alert("OTP Sent!");';
    echo 'window.location.href = "admin_otp_verification.php";';
    echo '</script>';
    exit();
} else {
    echo "<script> alert('Error Sending OTP!'); </script>" . mysqli_error($connect);
}
