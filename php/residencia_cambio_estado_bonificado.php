<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

$data=array();
if ($mysqli->errno>0) {
    exit("server");
}


$registro=$_POST['registro'];
$bonificado=$_POST['bonificado'];

$sql = "UPDATE residentes SET 
    bonificado = ?
    WHERE registro=?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('s,s',  $bonificado,$registro);

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



