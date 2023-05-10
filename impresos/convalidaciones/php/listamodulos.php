<?php

session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("../../../php/conexion.php");


if ($mysqli->errno>0) {
    $resp["error"]="servidor";
    exit (json_encode($resp));
}

$ciclo=$_POST["ciclo"];
$grado=$_POST["grado"];



$c=$mysqli->query("SELECT materias_ciclos.materia FROM ciclos JOIN materias_ciclos ON ciclos.id=materias_ciclos.id WHERE ciclos.grado='$grado' AND ciclos.ciclo='$ciclo' ORDER BY materias_ciclos.materia");
if ($mysqli->errno>0){
    $resp["error"]="error_consulta";
    exit (json_encode($resp));
}
elseif ($c->num_rows==0){
    $resp["error"]="no_materias";
    exit (json_encode($resp));
}
else {
    $cont=0;
    while($r=$c->fetch_assoc()){
        $resp["datos"][$cont]["codigo"]=$r["codigo"];
        $resp["datos"][$cont]["materia"]=$r["materia"];
        $cont++;
    }
    $resp["error"]="ok";
    exit (json_encode($resp));
}