<?php

$random_numbers = [];
for ($i = 0; $i < 6; $i++) {
    $random_numbers[] = rand(0, 9);
}

$admin_otp = implode('', $random_numbers);
$otp_query = "UPDATE admin_sign_in SET admin_otp = $admin_otp WHERE admin_id = $admin_id";
$stmt = mysqli_prepare($connect, $otp_query);
