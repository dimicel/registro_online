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


$curso=$_POST["curso"];
$fecha=$_POST["fecha"];
$consulta="SELECT * FROM residentes  where curso='$curso' and baja=0 ";

$res=$mysqli->query($consulta);

if ($res->num_rows==0){
    $data["error"]="sin_registros";
    exit(json_encode($data));
}
$data["error"]="ok";
$contador=0;
$data["registros"]=array();

while ($reg=$res->fetch_assoc()){
    $data["registros"][$contador]["id_nie"]= $reg["id_nie"];
    $data["registros"][$contador]["nombre"]=ucwords(strtolower($reg["apellidos"])).", ".ucwords(strtolower($reg["nombre"]));
    $contador++;
}
$res->free();
exit(json_encode($data));

