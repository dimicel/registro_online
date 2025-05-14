<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");

	
if ($mysqli->errno>0) {
    exit("server");
}

$departamento=$_POST['dpto_nombre'];
$abreviatura=$_POST['dpto_abreviatura'];
$id=$_POST['dpto_id'];

$mysqli->query("UPDATE departamentos SET departamento='$departamento', abreviatura='$abreviatura' WHERE id='$id'");
if ($mysqli->errno>0){
	exit("fallo_modificacion");		
}
elseif ($mysqli->affected_rows==0) {
    exit("no_modificado");
}
exit("ok"); 