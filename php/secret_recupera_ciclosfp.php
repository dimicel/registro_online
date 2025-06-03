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

$filtro=1;
if ($diurno==true) {
    if ($filtro==1) {
        $filtro="diurno=true";
    } else {
        $filtro.=" and diurno=true";
    }
}
if ($vespertino==true) {
    if ($filtro==1) {
        $filtro="vespertino=true";
    } else {
        $filtro.=" and vespertino=true";
    }
}
if ($nocturno==true) {
    if ($filtro==1) {
        $filtro="nocturno=true";
    } else {
        $filtro.=" and nocturno=true";
    }
}
if ($elearning==true) {
    if ($filtro===1) {
        $filtro="'e-learning'=true";
    } else {
        $filtro.=" and 'e-learning'=true";
    }
}
if ($dpto!=="") {
    if ($filtro==1) {
        $filtro="departamento='$dpto'";
    } else {
        $filtro.=" and departamento='$dpto'";
    }
}
if ($grado!=="") {
    if ($filtro==1) {
        $filtro="grado='$grado'";
    } else {
        $filtro.=" and grado='$grado'";
    }
}

$consulta="select * from ciclos where " . $filtro . " order by grado ASC, denominacion ASC";

$res=$mysqli->query($consulta);

if ($mysqli->errno>0) {
    //$data["error"]="server";
    $data["error"]="Error: " . $mysqli->error;
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

