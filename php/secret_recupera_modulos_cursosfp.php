<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: application/json;charset=utf-8");


$data=array();
if ($mysqli->errno>0) {
    $data["error"]="server";
    exit(json_encode($data));
}

$id=$_POST["id"];

$res=$mysqli->query("select * from ciclos_modulos where id=$id order by curso ASC, modulo ASC, codigo ASC");
if ($mysqli->errno>0) {
    $data["error"]="server";
    exit(json_encode($data));
}

$data["error"]="ok";
$contador=0;
$curso_old="";
$data["registro"]=array();
while ($reg=$res->fetch_assoc()){
    if ($curso_old!=$reg["curso"]){
        $contador=0;
        $curso_old=$reg["curso"];
    }
    $data["registro"][$reg["curso"]][$contador]= $reg;
    $contador++;
}

exit(json_encode($data));

