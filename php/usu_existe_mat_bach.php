<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
$mysqli->set_charset("utf8");

if ($mysqli->errno>0){
    exit("server");
} 

$id_nie=$_POST["id_nie"];
$curso=$_POST["curso"];
$resp=Array();


$consulta="select * from mat_bach where id_nie='$id_nie' and curso='$curso'";

$res=$mysqli->query($consulta);

if ($res->num_rows>0) {
    $reg=$res->fetch_array(MYSQLI_ASSOC);
    $resp["error"]="ok";
    $resp["denom_curso"]=$reg["grupo"];
    $res->free();
    exit(json_encode($resp));
}
else {
    $res->free();
    $resp["error"]="noexiste";
    exit(json_encode($resp));
}
