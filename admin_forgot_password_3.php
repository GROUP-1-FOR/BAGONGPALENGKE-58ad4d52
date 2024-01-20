<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';



$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'argojosafor@gmail.com';
$mail->Password = 'tuymblznanvyhppt';

$mail->SMTPSecure = 'ssl';
$mail->Port = 465;


$mail->setFrom('argojosafor@gmail.com');
$mail->addAddress($admin_email);
$mail->isHTML(true);
$mail->Subject = 'Password Update';


date_default_timezone_set('Asia/Manila');

// Set the date and time format
$dateFormat = 'Y-m-d H:i:s';

// Get the current date and time in the Philippines time zone
$currentDateTime = date($dateFormat);

// Construct the email body
$mail->Body = '<font color="#008000">' . "Password Updated!" . "</font> Your Bagong Palengke Account's Password has been updated on " . $currentDateTime . ".";

$mail->send();
