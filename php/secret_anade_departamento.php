<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");

	
if ($mysqli->errno>0) {
    exit("server");
}

$departamento=$_POST['dpto_nombre'];
$abreviatura=$_POST['dpto_abreviatura'];

$mysqli->query("insert into departamentos (departamento,abreviatura) values ('$departamento','$abreviatura')");
if ($mysqli->errno>0){
	exit("fallo_alta");		
}
exit("ok"); 