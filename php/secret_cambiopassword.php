<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
include("mail.php");
header("Content-Type: text/html;charset=utf-8");
session_start();
session_regenerate_id();
header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
	
if ($mysqli->errno>0) {
    exit("server");
}

$nie=$_POST["nie"];
$email=$_POST["email"];
$password=$_POST["password"];
$pass=password_hash($_POST["password"],PASSWORD_BCRYPT);
$mysqli->set_charset("utf8");

$mysqli->query("update usuarios set password='$pass' where id_nie='$nie'");
if ($mysqli->errno>0){
	exit("fallo_cambio " . $mysqli->error);		
}
if ($email!=''){
    $mensaje="      TRAMITACIÓN ONLINE DE DOCUMENTACIÓN DEL IES UNIVERSIDAD LABORAL DE TOLEDO <br><br><br>";
    $mensaje.="No responda a este correo. Este es un mensaje automático.<br>";
    $mensaje.="Sus credenciales para el acceso son:";
    $mensaje.="          NIE: ".$nie . "<br>";
    $mensaje.="          Contraseña: ".$password;
    
    $mail->addAddress($email, '');
    $mail->Subject = 'Nuevo Acceso - Sistema Tramitación Online del IES Universidad Laboral';
    
    $mail->Body =$mensaje;
    
    $check_envio=$mail->send();
    
    if (!$check_envio) exit("envio");
    else exit("email");
}
exit("ok");