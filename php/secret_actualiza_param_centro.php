<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

$data=array();
if ($mysqli->errno>0) {
    exit("server");
}

$tipo_param=$_POST['tipo_param'];
if($tipo_param=="centro"){
    $director=$_POST['director'];
    $centro=$_POST['centro'];
    $direccion=$_POST['direccion'];
    $cp=$_POST['cp'];
    $localidad=$_POST['localidad'];
    $provincia=$_POST['provincia'];
    $tlf_centro=$_POST['tlf_centro'];
    $fax_centro=$_POST['fax_centro'];
    $email_centro=$_POST['email_centro'];

    $sql = "UPDATE config_centro SET 
    director = ?, 
    centro = ?, 
    cp_centro = ?, 
    direccion_centro = ?, 
    localidad_centro = ?, 
    provincia_centro = ?, 
    tlf_centro = ?, 
    fax_centro = ?,
    email_centro = ?
    WHERE id=0";
}
elseif ($tipo_param=="residencia"){
    $email_jef_res=$_POST['email_jef_res'];
    $fianza_bonif=$_POST['finza_bonif'];
    $fianza_nobonif=$_POST['finza_nobonif'];

    $sql = "UPDATE config_centro SET 
    email_jefe_residencia = ?, 
    residencia_fianza_bonificados = ?, 
    residencia_fianza_no_bonificados = ? 
    WHERE id=0";
}






$stmt = $mysqli->prepare($sql);
if($tipo_param=="centro"){
    $stmt->bind_param('sssssssss', $director, $centro, $cp, $direccion, $localidad, $provincia, $tlf_centro, $fax_centro, $email_centro);
}
elseif($tipo_param=="residencia"){
    $stmt->bind_param('sss', $email_jef_res, $fianza_bonif, $fianza_nobonif);
}

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



