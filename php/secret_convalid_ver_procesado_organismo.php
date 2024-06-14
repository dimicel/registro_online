<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");


$datos=array();
if ($mysqli->errno>0) {
    $datos["error"]="server";
    exit(json_encode($datos));
}
$registro=$_POST["registro"];

$sql = "select * from convalidaciones WHERE registro='$registro'";
$result = $mysqli->query($sql);
if ($mysqli->affected_rows > 0) {
    while($reg=$result->fetch_assoc()){
        $datos["centro"]=$reg["resuelve_cen"];
        $datos["consejeria"]=$reg["resuelve_con"];
        $datos["ministerio"]=$reg["resuelve_min"];
    }
    $mysqli->close();
    $datos["error"]="ok";
    exit(json_encode($datos));
}
else {
    $mysqli->close();
    $datos["error"]="no_registro";
    exit(json_encode($datos));
}
