<?php

function generateAndSaveOTP($admin_id, $connect)
{
    // Generate a random 6-digit OTP
    $random_numbers = [];
    for ($i = 0; $i < 6; $i++) {
        $random_numbers[] = rand(0, 9);
    }
    $admin_otp = implode('', $random_numbers);

    // Update the database with the generated OTP
    $otp_query = "UPDATE admin_sign_in SET admin_otp = ? WHERE admin_id = ?";
    $stmt = mysqli_prepare($connect, $otp_query);

    // Check if the statement was prepared successfully
    if ($stmt) {
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, "si", $admin_otp, $admin_id);

        // Execute the statement
        mysqli_stmt_execute($stmt);

        $admin_otp_message = $admin_otp;
        $_SESSION['admin_otp_message'] = $admin_otp_message;


        // Check for success or failure
        if (mysqli_stmt_affected_rows($stmt) == 0) {
            echo "Failed to Generate OTP!";
            exit();
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: " . mysqli_error($connect);
    }
}

generateAndSaveOTP($admin_id, $connect);
