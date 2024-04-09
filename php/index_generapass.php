<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");


header("Content-Type: text/html;charset=utf-8");
include("mail.php");
include("conexion.php");
$nie=strtoupper($_POST['nie']);
if ($nie=="S4500175G" || $nie=="S4500175GJEF" || $nie=="S4500175GRES") exit("reservado");
$a_medias=false;
$envio=false;

if ($mysqli->errno>0){
	exit("server");
}

$acentos = $mysqli->query("SET NAMES 'utf8'");


$usu=$mysqli->query("select * from usuarios where id_nie='$nie'");
if ($usu->num_rows<=0){
	exit("usuario");
}
$registro=$usu->fetch_assoc();
$nombre=$registro['nombre'];
$apellidos=$registro['apellidos'];
$email=$registro['email'];
$nif=$registro['id_nif'];
$nie=$registro['id_nie'];
$primer_acceso=$registro['no_ha_entrado'];
if ($primer_acceso){
	exit("primer_acceso");
}

$mayus="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$minus="abcdefghijklmnopqrstuvwxyz";
$nums="0123456789";
$array=array("","","","","","","","");
$password="";
$array[0]=substr($mayus,mt_rand(0,strlen("mayus")-1),1);
$array[1]=substr($minus,mt_rand(0,strlen("minus")-1),1);
$array[2]=substr($nums,mt_rand(0,strlen("nums")-1),1);
$array[3]=substr($mayus,mt_rand(0,strlen("mayus")-1),1);
$array[4]=substr($minus,mt_rand(0,strlen("minus")-1),1);
$array[5]=substr($nums,mt_rand(0,strlen("nums")-1),1);
$array[6]=substr($mayus,mt_rand(0,strlen("mayus")-1),1);
$array[7]=substr($minus,mt_rand(0,strlen("signos")-1),1);
shuffle($array);
$password=$array[0].$array[1].$array[2].$array[3].$array[4].$array[5].$array[6].$array[7];

$pass_encript=password_hash($password,PASSWORD_BCRYPT);

$datos=$mysqli->query("update usuarios set password='$pass_encript' where id_nie='$nie'");
if ($mysqli->errno>0){
	exit("grabar");
}

$mensaje="      TRAMITACIÓN ONLINE DE DOCUMENTACIÓN DEL IES UNIVERSIDAD LABORAL DE TOLEDO <br><br><br>";
$mensaje.="No responda a este correo. Este es un mensaje automático.<br>";
$mensaje.="Guarde sus nuevas credenciales en lugar seguro y no las comparta con nadie.<br><br>";
$mensaje.="Sus nuevos datos para el acceso son:<br><br>";
$mensaje.="          NIE (Nº Identificación Escolar): ".$nie . "<br><br>";
$mensaje.="          Contraseña: ".$password;

$mail->addAddress($email, '');
$mail->Subject = 'Nuevo Acceso - Sistema Tramitación Online del IES Universidad Laboral';

$mail->Body =$mensaje;

$check_envio=$mail->send();

if (!$check_envio) exit("envio");
echo "ok";
