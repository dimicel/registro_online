<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) {
    exit("server");
}
$registro=$_POST["registro"];
$estado_procesado=$_POST["estado_procesado"];
$organismo=$_POST["organismo"];

if ($organismo=="centro") $resuelto_por="resuelto_cen";
elseif ($organismo=="consejeria") $resuelto_por="resuelto_con";
elseif ($organismo=="ministerio") $resuelto_por="resuelto_min";

$sql2="select * from convalidaciones where registro='$registro'";
$cont=$mysqli->query($sql2);
if($cont->num_rows==0){
    $mysqli->close();
    exit("no_registro");
}

$sql = "UPDATE convalidaciones SET $resuelto_por='$estado_procesado' WHERE registro='$registro'";
$result = $mysqli->query($sql);
if ($result) {
    $mysqli->close();
    exit("ok");
}
else {
    $mysqli->close();
    exit("db");
}
