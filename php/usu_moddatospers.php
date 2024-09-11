<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) {
    exit("server");
}
$mysqli->set_charset("utf8");

$id_nie=$_POST["dat_idnie"];
$sexo=$_POST["dat_sexo"];
$fecha_nac=substr($_POST['dat_fecha_nac'],6,4).'/'.substr($_POST['dat_fecha_nac'],3,2).'/'.substr($_POST['dat_fecha_nac'],0,2);
$fecha_nac=date('Y-m-d',strtotime($fecha_nac));
$telefono=$_POST["dat_telefono"];
$email=$_POST["dat_email"];
$direccion=$_POST["dat_direccion"];
$cp=$_POST["dat_cp"];
$localidad=$_POST["dat_localidad"];
$provincia=$_POST["dat_provincia"];
$tutor1=$_POST["dat_tutor1"]; 
$telef_tut1=$_POST["dat_telef_tut1"];
$email_tut1=$_POST["dat_email_tut1"];
$tutor2=$_POST["dat_tutor2"];
$telef_tut2=$_POST["dat_telef_tut2"];
$email_tut2=$_POST["dat_email_tut2"];
$nss=$_POST["dat_nss"];

$checkusu=$mysqli->query("select * form usuarios_dat where id_nie='$id_nie'");
if($checkusu->num_rows==0){
    $mysqli->query("insert into usuarios_dat (id_nie) values ('$id_nie')");
}


$consulta="update usuarios_dat set sexo='$sexo',
                                fecha_nac='$fecha_nac',
                                telef_alumno='$telefono',
                                email='$email',
                                num_ss='$nss',
                                direccion='$direccion',
                                cp='$cp',
                                localidad='$localidad',
                                provincia='$provincia',
                                tutor1='$tutor1',
                                email_tutor1='$email_tut1',
                                tlf_tutor1='$telef_tut1',
                                tutor2='$tutor2',
                                email_tutor2='$email_tut2',
                                tlf_tutor2='$telef_tut2' where id_nie='$id_nie'";



if($mysqli->query($consulta)==TRUE) exit("ok");
else exit("Fallo:".$mysqli->error);

$mysqli->close();