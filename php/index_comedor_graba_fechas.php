<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");

	
if ($mysqli->errno>0) {
    exit("server");
}

$id_nie=$_POST["id_nie"];
$lista_fechas = json_decode($_POST['lista_fechas'], true);

$stmt = $mysqli->prepare("INSERT INTO ciclos (ley, grado, denominacion, departamento, cursos, diurno, vespertino, nocturno, `e-learning`, dual_en_empresas, bilingue)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssiiiiiss", $ley, $grado, $ciclo, $dpto, $cursos, $diurno, $vespertino, $nocturno, $elearning, $dualizable, $bilingue);
$stmt->execute();

if ($mysqli->errno>0){
	exit("fallo_alta");		
}
exit("ok"); 