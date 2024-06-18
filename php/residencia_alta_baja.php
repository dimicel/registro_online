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
$baja=$_POST['baja'];
//$fecha_baja=date('Y-m-d');
if ($baja==0)$fecha_baja="0000-00-00";
else $fecha_baja=substr($_POST['fecha_baja'],6,4).'/'.substr($_POST['fecha_baja'],3,2).'/'.substr($_POST['fecha_baja'],0,2);;

$sql = "UPDATE residentes SET 
    baja = ?,
    fecha_baja= ?
    WHERE registro='$registro'";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('ss',  $baja,$fecha_baja);

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



