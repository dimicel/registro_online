<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
$mysqli->set_charset("utf8");
$respuesta=array();

if ($mysqli->errno>0){
    exit("server");
} 

$id_nie=$_POST["id_nie"];
$curso=$_POST["curso"];

$res=$mysqli->query("select * from residentes where id_nie='$id_nie' and curso='$curso'");

if ($res->num_rows>0) {
    $res->free();
    exit("ok");
}

else {
    $res->free();
    exit("noexiste");
}

