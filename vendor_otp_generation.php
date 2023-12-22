<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

generateAndSaveOTP($vendor_userid, $connect);


$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'argojosafor@gmail.com';
$mail->Password = 'tuymblznanvyhppt';

$mail->SMTPSecure = 'ssl';
$mail->Port = 465;

$vendor_email = $_SESSION["vendor_email"];

$mail->setFrom('argojosafor@gmail.com');
$mail->addAddress($vendor_email);
$mail->isHTML(true);
$mail->Subject = 'OTP Code';
$otp_code = $_SESSION["vendor_otp_message"];

unset($_SESSION["vendor_otp_message"]);

$mail->Body = '<font color="#008000">' . $otp_code . "</font> is your OTP code. For your protection, do not share this code with anyone.";

$mail->send();

function generateAndSaveOTP($vendor_userid, $connect)
{
    // Generate a random 6-digit OTP
    $random_numbers = [];
    for ($i = 0; $i < 6; $i++) {
        $random_numbers[] = rand(0, 9);
    }
    $vendor_otp = implode('', $random_numbers);

    // Update the database with the generated OTP
    $otp_query = "UPDATE vendor_sign_in SET vendor_otp = ? WHERE vendor_userid = ?";
    $stmt = mysqli_prepare($connect, $otp_query);

    // Check if the statement was prepared successfully
    if ($stmt) {
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, "ss", $vendor_otp, $vendor_userid);

        // Execute the statement
        mysqli_stmt_execute($stmt);
        $vendor_otp_message = $vendor_otp;
        $_SESSION['vendor_otp_message'] = $vendor_otp_message;

        // Check for success or failure
        if (mysqli_stmt_affected_rows($stmt) == 0) {
            echo "Failed to Update OTP!";
            exit();
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: " . mysqli_error($connect);
    }
}
