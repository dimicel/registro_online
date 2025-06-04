<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");

	
if ($mysqli->errno>0) {
    exit("server");
}

$id=(int)$_POST['ciclo_id'];
$dpto=$_POST['ciclo_dpto'];
$grado=$_POST['ciclo_grado'];
$ciclo=$_POST['ciclo_ciclo'];
$cursos=$_POST['ciclo_cursos'];
$diurno = (int)isset($_POST['ciclo_diurno']) ? filter_var($_POST['ciclo_diurno'], FILTER_VALIDATE_BOOLEAN) : false;
$vespertino = (int)isset($_POST['ciclo_vespertino']) ? filter_var($_POST['ciclo_vespertino'], FILTER_VALIDATE_BOOLEAN) : false;
$nocturno = (int)isset($_POST['ciclo_nocturno']) ? filter_var($_POST['ciclo_nocturno'], FILTER_VALIDATE_BOOLEAN) : false;
$elearning = (int)isset($_POST['ciclo_elearning']) ? filter_var($_POST['ciclo_elearning'], FILTER_VALIDATE_BOOLEAN) : false;

$con=$mysqli->prepare("UPDATE ciclos SET grado=?, denominacion=?,departamento=?,cursos=?,diurno=?,vespertino=?,nocturno=?,`e-learning`=? WHERE id=?");
$con->bind_param("sssiiiii",$grado,$ciclo,$dpto,$cursos,$diurno,$vespertino,$nocturno,$elearning,$id);
$con->execute();
if ($mysqli->errno>0){
	exit("fallo_modificacion");		
}
elseif ($mysqli->affected_rows==0) {
    exit("no_modificado");
}
exit("ok"); 