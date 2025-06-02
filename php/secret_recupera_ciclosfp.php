<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

$data=array();
if ($mysqli->errno>0) {
    $data["error"]="server";
    exit(json_encode($data));
}

$dpto=$_POST['dpto'];
$grado=$_POST['grado'];
$diurno=$_POST['diurno'];
$vespertino=$_POST['vespertino'];
$nocturno=$_POST['nocturno'];
$elearning=$_POST['elearning'];

$filtro="";
$filtro="diurno=$diurno and vespertino=$vespertino and nocturno=$nocturno and e-learning=$elearning"; 
if ($dpto!=="") {
    $filtro.=" and departamento='$dpto'";
}
if ($grado!=="") {
    $filtro.=" and grado='$grado'";
}


$res=$mysqli->query("select * from ciclos where " . $filtro . " order by descripcion ASC, grado ASC");

if ($mysqli->errno>0) {
    $data["error"]="server";
    exit(json_encode($data));
}

$data["error"]="ok";
$data["registro"]=array();
$contador=0;
while ($reg=$res->fetch_assoc()){
    $data["registro"][$contador]= $reg;
    $contador++;
}
exit(json_encode($data));

