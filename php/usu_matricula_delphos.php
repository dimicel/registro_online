<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
$mysqli->set_charset("utf8");
$resp=array();
if ($mysqli->errno>0){
    $resp["error"]="server";
    exit(json_encode($resp));
} 

$id_nie=$_POST["id_nie"];
$curso=$_POST["curso"];


$res=$mysqli->query("select * from matriculas_delphos where id_nie='$id_nie' and curso='$curso'");

if ($res->num_rows>0){
    $d=$res->fetch_array(MYSQLI_ASSOC);
    $resp["error"]="ok";
}
else $resp["error"]="no_procesada";

$res->free();

exit (json_encode($resp));

