<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");

	
if ($mysqli->errno>0) {
    exit("server");
}

$modulo=$_POST['modulo'];
$codigo=$_POST['codigo'];

$mysqli->query("insert into modulosfp (modulo,codigo) values ('$modulo','$codigo')");
if ($mysqli->errno>0){
	exit("fallo_alta");		
}
exit("ok"); 