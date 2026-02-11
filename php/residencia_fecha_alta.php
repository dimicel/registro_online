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
$curso=$_POST['curso'];
$fecha_alta=$_POST['fecha_alta'];
//$fecha_baja=date('Y-m-d');
if ($fecha_alta!="fecha_registro")
{    
    $fecha_alta=substr($_POST['fecha_alta'],6,4).'/'.substr($_POST['fecha_alta'],3,2).'/'.substr($_POST['fecha_alta'],0,2);
    $sql = "UPDATE residentes SET 
        fecha_alta = ?
        WHERE registro='$registro' and curso='$curso'";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s',  $fecha_alta);
}
else
{
    $sql = "UPDATE residentes SET 
        fecha_alta = fecha_registro
        WHERE registro='$registro' and curso='$curso'";

    $stmt = $mysqli->prepare($sql);
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



