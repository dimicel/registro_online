<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

$data=array();
if ($mysqli->errno>0) {
    exit("server");
}

$director=$_POST['director'];
$centro=$_POST['centro'];
$direccion=$_POST['direccion'];
$cp=$_POST['cp'];
$localidad=$_POST['localidad'];
$provincia=$_POST['provincia'];
$tlf_centro=$_POST['tlf_centro'];
$fax_centro=$_POST['fax_centro'];
$email_centro=$_POST['email_centro'];
$email_jef_res=$_POST['email_jef_res'];
$fianza_bonif=$_POST['finza_bonif'];
$fianza_nobonif=$_POST['finza_nobonif'];


$sql="update config_centro set director='$director',email_jefe_residencia='$email_jef_res',centro='$centro',cp_centro='$cp',direccion_centro='$direccion',";
$sql.="localidad_centro='$localidad',provincia_centro='$provincia',tlf_centro='$tlf_centro',fax_centro='$fax_centro',email_centro='$email_centro',";
$sql.="residencia_fianza_bonificados='$fianza_bonif',residencia_fianza_no_bonificados='$fianza_nobonif' where 1";
if ($conn->query($sql) === TRUE) {
    if ($conn->affected_rows > 0) {
        exit("ok");
    } else {
        exit("database");
    }
} else {
    exit("Error al actualizar el registro: " . $conn->error);
}


