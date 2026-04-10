<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");

$data=array();
if ($mysqli->errno>0) {
    exit("server");
}

$alta_mod=$_POST["alta_mod"];
$tipo=$mysqli->real_escape_string($_POST["tipo"]);
$ruta=$mysqli->real_escape_string($_POST["ruta"]);
$parada=$mysqli->real_escape_string($_POST["parada"]);


