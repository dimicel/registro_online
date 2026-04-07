<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");

$data=array();
if ($mysqli->errno>0) {
    $data["error"]="server";
    exit(json_encode($data));
}

$consulta="select * from transporte_rutas order by ruta, parada";
$res=$mysqli->query($consulta);
if ($res->num_rows==0){
    $data["error"]="sin_rutas";
    exit(json_encode($data));
}
$data["error"]="ok";
$data["rutas"]=array();
while ($reg=$res->fetch_assoc()){
    $data["rutas"][]=$reg;
}
exit(json_encode($data));