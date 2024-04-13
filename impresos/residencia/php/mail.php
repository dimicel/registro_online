<?php

require __DIR__.'/../../../PHPmailer/src/Exception.php';
require __DIR__.'/../../../PHPmailer/src/PHPMailer.php';
require __DIR__.'/../../../PHPmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

$mail->CharSet = 'UTF-8';
$mail->isSMTP();
$mail->Host = 'ssl://mail.ulaboral.org';
$mail->SMTPAuth = TRUE;
$mail->SMTPSecure = 'tls';
$mail->Username = 'noresponder@ies.ulaboral.org';
$mail->Password = 'Uni-L@boral-23';

// Set the SMTP port. 587 para TLS 
$mail->Port = 465;
$mail->setLanguage('es', 'PHPmailer/language/');
$mail->setFrom('noresponder@ies.ulaboral.org', 'Registro Online - IES Universidad Laboral');
$mail->isHTML(true);

