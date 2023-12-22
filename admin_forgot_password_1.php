<?php

$select_query_FetchAdminUserId = "SELECT admin_userid FROM admin_sign_in WHERE admin_email = '$email'";
$resultFetchAdminUserId = mysqli_query($connect, $select_query_FetchAdminUserId);
$rowFetchAdminUserId = mysqli_fetch_assoc($resultFetchAdminUserId);

if ($rowFetchAdminUserId) {
    // Admin user found, you can access $rowFetchAdminUserId['admin_userid'] for further processing
    $admin_userid = $rowFetchAdminUserId['admin_userid'];
} else {
    // Admin user not found, display alert and redirect
    echo '<script>';
    echo 'alert("Admin User Not Found!");';
    echo 'window.location.href = "admin_forgot_password.php";';
    echo '</script>';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

generateAndSaveToken($admin_userid, $connect);


$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'argojosafor@gmail.com';
$mail->Password = 'tuymblznanvyhppt';

$mail->SMTPSecure = 'ssl';
$mail->Port = 465;

$mail->setFrom('argojosafor@gmail.com');
$mail->addAddress($email);
$mail->isHTML(true);
$mail->Subject = 'Reset Password Token';
$otp_token = $_SESSION["reset_password_token"];

unset($_SESSION["reset_password_token"]);

$mail->Body = '<font color="#008000">' . $otp_token . '</font> is your Reset Password Token. For your protection, do not share this code with anyone.';

$mail->send();



function generateAndSaveToken($admin_userid, $connect)
{
    // Generate a random 6-character alphanumeric token
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $token_length = 6;
    $admin_token = '';

    for ($i = 0; $i < $token_length; $i++) {
        $admin_token .= $characters[rand(0, strlen($characters) - 1)];
    }

    // Update the database with the generated token
    $token_query = "UPDATE admin_sign_in SET admin_token = ? WHERE admin_userid = ?";
    $stmt = mysqli_prepare($connect, $token_query);

    // Check if the statement was prepared successfully
    if ($stmt) {
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, "ss", $admin_token, $admin_userid);

        // Execute the statement
        mysqli_stmt_execute($stmt);

        $admin_token_message = $admin_token;
        $_SESSION['reset_password_token'] = $admin_token_message;

        // Check for success or failure
        if (mysqli_stmt_affected_rows($stmt) == 0) {
            echo "Failed to Generate Token!";
            exit();
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: " . mysqli_error($connect);
    }
}
