<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");
header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if ($mysqli->errno>0) {
    exit("server");
}

$nie=$_POST["nu_nie"];
$nif=$_POST["nu_nif"];
$nombre=$_POST["nu_nombre"];
$apellidos=$_POST["nu_apellidos"];
$email=$_POST["nu_email"];
$password=$_POST["nu_password"];
$pass=password_hash($password,PASSWORD_BCRYPT);
$mysqli->set_charset("utf8");

$consulta=$mysqli->query("select * from usuarios where id_nie='$nie' and no_ha_entrado=0");
if ($consulta->num_rows>0) exit("registrado");
$consulta->free();

$conCadena="update usuarios set id_nif='$nif', password='$pass', nombre='$nombre', apellidos='$apellidos',email='$email',no_ha_entrado=0 where id_nie='$nie'";
$comprobacion=$mysqli->query("select * from usuarios_dat where id_nie='$nie'");
if ($comprobacion->num_rows==0)$mysqli->query("insert into usuarios_dat (id_nie) values ('$nie')");

if ($mysqli->query($conCadena)===TRUE){
    $mysqli->close();
    if(!is_dir("../docs/".$nie)) mkdir("../docs/".$nie,0777);
    if (!is_dir("../docs/".$nie."/seguro")) mkdir("../docs/".$nie."/seguro",0777);
    if (!is_dir("../docs/".$nie."/dni")) mkdir("../docs/".$nie."/dni",0777);
    if (!is_dir("../docs/".$nie."/certificado_notas")) mkdir("../docs/".$nie."/certificado_notas",0777);
    exit("ok");
}
else {
    $mysqli->close();
    exit("fallo_alta");
}
