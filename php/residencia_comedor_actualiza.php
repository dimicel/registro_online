<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

$data=array();
if ($mysqli->errno>0) {
    $data["error"]="server";
    exit(json_encode($data));
}

$curso=$_POST["curso"];
$fecha=DateTime::createFromFormat('d/m/Y', $_POST["fecha"]);
$fecha_mysql = $fecha->format('Y-m-d');
$asistencias = json_decode($_POST["asistencias"], true);

