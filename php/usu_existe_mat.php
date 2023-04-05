<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");

$respuesta=array();

$id_nie=$_POST["id_nie"];
$curso=$_POST["curso"];

$mats=array();
$mats=glob("../docs/".$id_nie."/matriculas"."/".$curso."/*.pdf");
if (sizeof($mats)>0){
    $respuesta["error"]="ok";
    exit(json_encode($respuesta));
}
else {
    $respuesta["error"]="noexiste";
    exit(json_encode($respuesta));
}
