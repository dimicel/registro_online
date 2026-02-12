<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) {
    exit("server");
}
$tabla=$_POST["formulario"];
$estado = isset($_POST['estado']) ? $_POST['estado'] : 0;
$registro=$_POST["registro"];


$sql = "UPDATE $tabla SET pasado_delphos='$estado' WHERE registro='$registro'";
$result = $mysqli->query($sql);

if ($mysqli->affected_rows > 0) {
    $mysqli->close();
    exit("ok");
}
else{
    $mysqli->close();
    exit("errordb");
}
