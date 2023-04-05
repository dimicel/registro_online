<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) {
    $resp["error"]="server";
    exit(json_encode($resp));
}
$id_nie=$_POST["id_nie"];
$resp=array();
$dat=$mysqli->query("select * from usuarios_dat where id_nie='$id_nie'");
if($dat->num_rows>0){
    while($reg=$dat->fetch_assoc()){
        $resp["datos"]=$reg;
        $resp["error"]="ok";
    }
}
else $resp["error"]="no_usuarios";

$mysqli->close();
exit(json_encode($resp));