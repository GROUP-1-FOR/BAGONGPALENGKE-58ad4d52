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
$mail->Body = '<div style="text-align: center; font-family: Arial, sans-serif; background-color: #f2f2f2; padding: 20px;">
                  <img src="cid:logo" alt="Santa Rosa Public Market" style="max-width: 200px;"><br />
                  <p style="font-size: 18px; color: #333;">Vendor User ID: <strong>' . $vendor_userid . '</strong></p>
                  <p style="font-size: 18px; color: #333;">Vendor Password: <strong>' . $vendor_password_1 . '</strong></p>
                  <p style="font-size: 16px; color: #666;">This is your vendor credentials. For your protection, do not share this with anyone.</p>
               </div>';


$mail->send();
