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

$id=$_POST["id"];

$consulta="SELECT * FROM usuarios where id_nie='$id'";

$res=$mysqli->query($consulta);

if ($res->num_rows==1){
    $data["error"]="ok";
    $reg=$res->fetch_assoc();
    $data["registro"]["apellidos"]=$reg["apellidos"];
    $data["registro"]["nombre"]=$reg["nombre"];
    $data["registro"]["email"]= $reg["email"];
    $data["registro"]["nif"]= $reg["id_nif"];
    $res->free();
    exit(json_encode($data));
}
else if($res->num_rows>1){
    $data["error"]="duplicado";
    exit(json_encode($data));
}
else {
    $data["error"]="no_existe";
    exit(json_encode($data));
}


