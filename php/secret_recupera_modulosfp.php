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

$ordenCampo=$_POST["ordenCampo"];
$ordenDireccion=$_POST["ordenDireccion"];

$ordenSQL="";
if ($ordenCampo=="codigo") $ordenSQL.=" codigo ".$ordenDireccion.", modulo ASC";
else $ordenSQL.=" modulo ".$ordenDireccion.", codigo ASC";

$res=$mysqli->query("select * from modulosfp order by ".$ordenSQL);

$data["error"]="ok";
$data["registro"]=array();
$contador=0;
while ($reg=$res->fetch_assoc()){
    $data["registro"][$contador]= $reg;
    $contador++;
}


exit(json_encode($data));

