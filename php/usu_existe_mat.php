<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
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
