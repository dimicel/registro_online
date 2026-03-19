<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");

if ($mysqli->errno>0) exit("server");

$res=$mysqli->query("select * from matricula");
$reg=$res->fetch_array(MYSQLI_ASSOC);
$res->free();
exit(json_encode($reg));
