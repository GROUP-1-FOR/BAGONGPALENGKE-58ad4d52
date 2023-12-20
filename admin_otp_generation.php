<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

generateAndSaveOTP($admin_userid, $connect);


$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'blacqueswan@gmail.com';
$mail->Password = 'brvtgvombbkvwugy';

$mail->SMTPSecure = 'ssl';
$mail->Port = 465;

$admin_email = $_SESSION["admin_email"];

$mail->setFrom('blacqueswan@gmail.com');
$mail->addAddress($admin_email);
$mail->isHTML(true);
$mail->Subject = 'OTP Code';
$otp_code = $_SESSION["admin_otp_message"];

unset($_SESSION["admin_otp_message"]);

$mail->Body = $otp_code;

$mail->send();




function generateAndSaveOTP($admin_userid, $connect)
{
    // Generate a random 6-digit OTP
    $random_numbers = [];
    for ($i = 0; $i < 6; $i++) {
        $random_numbers[] = rand(0, 9);
    }
    $admin_otp = implode('', $random_numbers);

    // Update the database with the generated OTP
    $otp_query = "UPDATE admin_sign_in SET admin_otp = ? WHERE admin_userid = ?";
    $stmt = mysqli_prepare($connect, $otp_query);

    // Check if the statement was prepared successfully
    if ($stmt) {
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, "ss", $admin_otp, $admin_userid);

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
