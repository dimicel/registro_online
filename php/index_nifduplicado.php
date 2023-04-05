<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");
header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");	

if ($mysqli->errno>0) {
    exit("server");
}

$nif=$_POST["nu_nif"];
$id_nie=$_POST["id_nie"];
$mysqli->set_charset("utf8");

$consulta=$mysqli->query("select * from usuarios where id_nif='$nif' and id_nie!='$id_nie'");
if ($consulta->num_rows>0) exit("duplicado");
$consulta->free();
exit ("ok");
