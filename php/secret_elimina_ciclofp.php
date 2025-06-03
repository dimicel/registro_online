<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

$data=array();
if ($mysqli->errno>0) {
    $data="server";
    exit($data);
}

$id=$_POST['id'];

$mysqli->set_charset("utf8");
$sql="DELETE FROM ciclos WHERE id='$id";
$result=$mysqli->query($sql);
if ($mysqli->errno>0) {
    $data="server";
    exit($data);
}
if ($mysqli->affected_rows==0) {
    $data="no";
    exit($data);
}  
if ($result==true) {
    $data="ok";
    exit($data);
} else {
    $data="error";
    exit($data);
}



