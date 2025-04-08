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

$res=$mysqli->query("select * from departamentos order by departamento");

$data["error"]="ok";
$data["registro"]=array();
$contador=0;
while ($reg=$res->fetch_assoc()){
    $data["registro"][$contador]= $reg;
    $contador++;
}


exit(json_encode($data));

