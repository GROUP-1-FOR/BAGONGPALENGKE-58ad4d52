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
$mail->addAddress($vendor_email);
$mail->isHTML(true);
$mail->Subject = 'Vendor User Account';



$mail->addEmbeddedImage('santarosapublicmarket.png', 'logo');
$mail->Body = '<div style="text-align: center;">
                  <img src="cid:logo" alt="Santa Rosa Public Market"><br />
                  Vendor User ID: ' . $vendor_userid . '<br />
                  Vendor Password: ' . $vendor_password_1 . '<br />
                  This is your vendor credentials. For your protection, do not share this with anyone.
               </div>';

$mail->send();
