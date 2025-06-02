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
if ($diurno) {
    if ($filtro===1) {
        $filtro="diurno=$diurno";
    } else {
        $filtro.=" and diurno=$diurno";
    }
}
if ($vespertino) {
    if ($filtro===1) {
        $filtro="vespertino=$vespertino";
    } else {
        $filtro.=" and vespertino=$vespertino";
    }
}
if ($nocturno) {
    if ($filtro===1) {
        $filtro="nocturno=$nocturno";
    } else {
        $filtro.=" and nocturno=$nocturno";
    }
}
if ($elearning) {
    if ($filtro===1) {
        $filtro="elearning=$elearning";
    } else {
        $filtro.=" and elearning=$elearning";
    }
}
if ($dpto!=="") {
    if ($filtro===1) {
        $filtro="dpto='$dpto'";
    } else {
        $filtro.=" and dpto='$dpto'";
    }
}
if ($grado!=="") {
    if ($filtro===1) {
        $filtro="grado='$grado'";
    } else {
        $filtro.=" and grado='$grado'";
    }
}

$consulta="select * from ciclos where " . $filtro . " order by descripcion ASC, grado ASC";
$data["consulta"]=$diurno."    ".$vespertino."    ".$nocturno."    ".$elearning."    ".$dpto."    ".$grado;    
exit(json_encode($data));

$res=$mysqli->query($consulta);

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

