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
$cursos=(int)$_POST['ciclo_cursos'];
$diurno = (int)isset($_POST['ciclo_diurno']) ? filter_var($_POST['ciclo_diurno'], FILTER_VALIDATE_BOOLEAN) : false;
$vespertino = (int)isset($_POST['ciclo_vespertino']) ? filter_var($_POST['ciclo_vespertino'], FILTER_VALIDATE_BOOLEAN) : false;
$nocturno = (int)isset($_POST['ciclo_nocturno']) ? filter_var($_POST['ciclo_nocturno'], FILTER_VALIDATE_BOOLEAN) : false;
$elearning = (int)isset($_POST['ciclo_elearning']) ? filter_var($_POST['ciclo_elearning'], FILTER_VALIDATE_BOOLEAN) : false;
$bilingue=0;
$dualizable=0;

$stmt = $mysqli->prepare("INSERT INTO ciclos (ley, grado, denominacion, departamento, cursos, diurno, vespertino, nocturno, `e-learning`, dual_en_empresas, bilingue)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssiiiiiss", $ley, $grado, $ciclo, $dpto, $cursos, $diurno, $vespertino, $nocturno, $elearning, $dualizable, $bilingue);
$stmt->execute();

if ($mysqli->errno>0){
	exit("fallo_alta");		
}
exit("ok"); 