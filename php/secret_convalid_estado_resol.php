<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) {
    exit("server");
}
$registro=$_POST["registro"];
$estado=$_POST["estado"];

$sql = "UPDATE convalidaciones SET resolucion='$estado' WHERE registro='$registro'";
$result = $mysqli->query($sql);
if ($result->num_rows > 0) {
    $mysqli->close();
    exit("ok");
}
else {
    $mysqli->close();
    exit("no_registro");
}
