<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");

if ($mysqli->errno>0) exit("server");

$res=$mysqli->query("select * from matricula");
$reg=$res->fetch_array(MYSQLI_ASSOC);
$res->free();
exit(json_encode($reg));
