<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");

	
if ($mysqli->errno>0) {
    exit("server");
}

$ley='LOE';
$dpto=$_POST['ciclo_dpto'];
$grado=$_POST['ciclo_grado'];
$ciclo=$_POST['ciclo_ciclo'];
$cursos=$_POST['ciclo_cursos'];
$diurno = isset($_POST['ciclo_diurno']) ? filter_var($_POST['ciclo_diurno'], FILTER_VALIDATE_BOOLEAN) : false;
$vespertino = isset($_POST['ciclo_vespertino']) ? filter_var($_POST['ciclo_vespertino'], FILTER_VALIDATE_BOOLEAN) : false;
$nocturno = isset($_POST['ciclo_nocturno']) ? filter_var($_POST['ciclo_nocturno'], FILTER_VALIDATE_BOOLEAN) : false;
$elearning = isset($_POST['ciclo_elearning']) ? filter_var($_POST['ciclo_elearning'], FILTER_VALIDATE_BOOLEAN) : false;
$bilingue=false;
$dualizable=false;

$mysqli->query("insert into ciclos (ley,grado,denominacion,departamento,cursos,diurno,vespertino,nocturno,`e-learning`,dual_en_empresas,bilingue)
                values ('$ley','$grado','$ciclo','$dpto','$cursos','$diurno','$vespertino','$nocturno','$elearning','$dualizable','$bilingue')");
if ($mysqli->errno>0){
	exit("fallo_alta");		
}
exit("ok"); 