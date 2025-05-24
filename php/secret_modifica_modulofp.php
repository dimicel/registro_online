<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");

	
if ($mysqli->errno>0) {
    exit("server");
}

$modulo=$_POST['modulo'];
$codigo=$_POST['codigo'];
$id_modulo=$_POST['id_modulo'];

$mysqli->query("UPDATE modulosfp SET modulo='$modulo', codigo='$codigo' WHERE id='$id_modulo'");
if ($mysqli->errno>0){
	exit("fallo_modificacion");		
}
elseif ($mysqli->affected_rows==0) {
    exit("no_modificado");
}
exit("ok"); 