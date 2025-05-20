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

$tabla=$_POST["formulario"];
$tabla_db=$tabla;
$registro=$_POST["registro"];

if(strpos($tabla_db, "premat_") !== false){
    if (strpos($tabla_db,"eso")) $tabla_db="premat_eso";
    else $tabla_db="premat_bach";
}

$consulta="select * from $tabla_db where registro='$registro'";
$res=$mysqli->query($consulta);

if ($res->num_rows==0){
    $data["error"]="sin_registro";
    exit(json_encode($data));
}

$data["error"]="ok";
$data["registro"]=array();
while ($reg=$res->fetch_assoc()){
    $data["registro"]=$reg;
    if (isset($data["registro"]["fecha_nac"])) $data["registro"]["fecha_nac"]=date("d/m/Y",strtotime($reg['fecha_nac']));
}
exit(json_encode($data));

