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
$primero=true;
if ($diurno!=="") {
    if ($primero) {
        if ($diurno==1) $filtro.="diurno=$diurno";
        $primero=false;
    } else {
        $filtro.=" and diurno=$diurno";
    }
}
if ($vespertino!=="") {
    if ($primero) {
        if ($vespertino==1) $filtro.="vespertino=$vespertino";
        $primero=false;
    } else {
        $filtro.=" and vespertino=$vespertino";
    }
}
if ($nocturno!=="") {
    if ($primero) {
        if ($nocturno==1) $filtro.="nocturno=$nocturno";
        $primero=false;
    } else {
        $filtro.=" and nocturno=$nocturno";
    }
}
if ($elearning!=="") {
    if ($primero) {
        if ($elearning==1) $filtro.="elearning=$elearning";
        $primero=false;
    } else {
        $filtro.=" and elearning=$elearning";
    }
}
if ($dpto!=="") {
    if ($primero) {
        $filtro.="dpto='$dpto'";
        $primero=false;
    } else {
        $filtro.=" and dpto='$dpto'";
    }
}
if ($grado!=="") {
    if ($primero) {
        $filtro.="grado='$grado'";
        $primero=false;
    } else {
        $filtro.=" and grado='$grado'";
    }
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

