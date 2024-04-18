<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) {
    exit("server");
}
$registro=$_POST["registro"];
$modulos=$_POST["modulo_convalid"];
$estados=$_POST["estado_convalid"];
$motivos=$_POST["motivo_no_fav_convalid"];

$sql = "UPDATE convalidaciones SET resolucion='$estado' WHERE registro='$registro'";
$result = $mysqli->query($sql);
if ($mysqli->affected_rows > 0) {
    $mysqli->close();
    exit("ok");
}
else {
    $mysqli->close();
    exit("no_registro");
}
