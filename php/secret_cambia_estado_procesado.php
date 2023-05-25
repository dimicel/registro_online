<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) {
    exit("server");
}
$tabla=$_POST["tabla"];
$estado=$_POST["estado"];
$registro=$_POST["registro"];

$sql = "UPDATE $tabla SET procesado='$estado' WHERE registro='$registro'";
$result = $mysqli->query($sql);

if ($mysqli->affected_rows > 0) {
    exit("errordb");
}


// Cierre de la conexión
$mysqli->close();
exit("ok");