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


$sql = "UPDATE config_centro SET 
    director = ?, 
    email_jefe_residencia = ?, 
    centro = ?, 
    cp_centro = ?, 
    direccion_centro = ?, 
    localidad_centro = ?, 
    provincia_centro = ?, 
    tlf_centro = ?, 
    fax_centro = ?, 
    email_centro = ?, 
    residencia_fianza_bonificados = ?, 
    residencia_fianza_no_bonificados = ? 
    WHERE id=0";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('ssssssssssss', $director, $email_jef_res, $centro, $cp, $direccion, $localidad, $provincia, $tlf_centro, $fax_centro, $email_centro, $fianza_bonif, $fianza_nobonif);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $stmt->close();
        $mysqli->close();
        exit("ok");
    } else {
        $stmt->close();
        $mysqli->close();
        exit("database");
    }
} else {
    $stmt->close();
    $mysqli->close();
    exit("Error al actualizar el registro: " . $stmt->error);
}



