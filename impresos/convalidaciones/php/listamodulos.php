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
$curso=$_POST["curso"];


if ($curso=="Virtual_Modular"){
    $c=$mysqli->query("SELECT ciclos_modulos.codigo,ciclos_modulos.modulo,ciclos_modulos.curso FROM ciclos JOIN ciclos_modulos ON ciclos.id=ciclos_modulos.id WHERE ciclos.grado='$grado' AND ciclos.denominacion='$ciclo' ORDER BY ciclos_modulos.modulo");
}
else {
    $c=$mysqli->query("SELECT ciclos_modulos.codigo,ciclos_modulos.modulo,ciclos_modulos.curso FROM ciclos JOIN ciclos_modulos ON ciclos.id=ciclos_modulos.id WHERE ciclos.grado='$grado' AND ciclos.denominacion='$ciclo' AND ciclos_modulos.curso='$curso' ORDER BY ciclos_modulos.modulo");
}

if ($mysqli->errno>0){
    $resp["error"]="error_consulta ".$mysqli->errno;
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
        $resp["datos"][$cont]["modulo"]=$r["modulo"];
        $cont++;
    }
    $resp["error"]="ok";
    exit (json_encode($resp));
}