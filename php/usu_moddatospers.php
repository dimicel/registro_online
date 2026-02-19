<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) {
    exit("server");
}
$mysqli->set_charset("utf8");
if (isset($_POST["dat_usuario"])) $usuario=$_POST["dat_usuario"];
else $usuario="alumno";
$id_nie=$_POST["dat_idnie"];
$email_recuperacion=$_POST["mod_email"];
$nif=$_POST["mod_nif"];
$nif_fecha_caducidad=substr($_POST['mod_fecha_caducidad'],6,4).'/'.substr($_POST['mod_fecha_caducidad'],3,2).'/'.substr($_POST['mod_fecha_caducidad'],0,2);
$nif_fecha_caducidad=date('Y-m-d',strtotime($nif_fecha_caducidad));
$pais=$_POST["mod_pais"];
$es_pasaporte=isset($_POST["mod_es_pasaporte"])?1:0;
$nombre=$_POST["mod_nombre"];
$apellidos=$_POST["mod_apellidos"]; 
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
$nss=trim($_POST["dat_nss"]);
$fecha_cambio_nuss=date('Y-m-d');

$checkusu=$mysqli->query("select * form usuarios_dat where id_nie='$id_nie'");
if($checkusu->num_rows==0){
    $mysqli->query("insert into usuarios_dat (id_nie) values ('$id_nie')");
}

$consulta1="update usuarios set nombre='$nombre',apellidos='$apellidos',id_nif='$nif',fecha_caducidad_id_nif='$nif_fecha_caducidad',pais='$pais',es_pasaporte='$es_pasaporte',email='$email_recuperacion' where id_nie='$id_nie'";

$consulta2="update usuarios_dat set sexo='$sexo',
            fecha_nac='$fecha_nac',
            telef_alumno='$telefono',
            email='$email',
            direccion='$direccion',
            cp='$cp',
            localidad='$localidad',
            provincia='$provincia',
            tutor1='$tutor1',
            email_tutor1='$email_tut1',
            tlf_tutor1='$telef_tut1',
            tutor2='$tutor2',
            email_tutor2='$email_tut2',
            tlf_tutor2='$telef_tut2'";
if(strlen($nss)>0){
    $consulta2.= ",nss='$nss',fecha_cambio_nss='$fecha_cambio_nuss' ";
}
$consulta2.= " where id_nie='$id_nie'";

if(!$mysqli->query($consulta1)) exit("Fallo consulta1:".$mysqli->error); 
if(!$mysqli->query($consulta2)) exit("Fallo consulta2:".$mysqli->error);
exit("ok");

$mysqli->close();