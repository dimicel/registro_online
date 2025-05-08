<?php

session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("../../../php/conexion.php");


if ($mysqli->errno>0) {
    $resp["error"]="servidor";
    exit (json_encode($resp));
}


$c=$mysqli->query("select * from ciclos order by grado,denominacion");
if ($mysqli->errno>0){
    $resp["error"]="error_consulta";
    exit (json_encode($resp));
}
elseif ($c->num_rows==0){
    $resp["error"]="no_ciclos";
    exit (json_encode($resp));
}
else {
    $cont=0;
    while($r=$c->fetch_assoc()){
        $resp["datos"][$cont]=$r;
        $cont++;
    }
    $resp["error"]="ok";
    exit (json_encode($resp));
}