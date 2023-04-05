<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("mail.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) exit("server");

$email=$_POST["email"];
$asunto=$_POST["asunto"];
$mensaje=$_POST["mensaje"];
$mail->addAddress($email, '');
$mail->Subject = 'Registro Online - '.$asunto;
$cuerpo = 'Registro online del IES Universidad Laboral<br>'.$mensaje;
$mail->Body =$cuerpo;
$mail->send();


 
