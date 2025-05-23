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

$buscar=trim($_POST["buscar"]);
if ($buscar!="") {
    $res=$mysqli->query("select * from modulosfp where modulo like '%$buscar%' or codigo like '%$buscar%' order by modulo ASC, codigo ASC");
} else {
    $res=$mysqli->query("select * from modulosfp order by modulo ASC, codigo ASC");
}

$data["error"]="ok";
$data["registro"]=array();
$contador=0;
while ($reg=$res->fetch_assoc()){
    $data["registro"][$contador]= $reg;
    $contador++;
}
exit(json_encode($data));

