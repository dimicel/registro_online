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
$curso=$_POST["curso"];
$resp=array();
$dat=$mysqli->query("select * from residentes where id_nie='$id_nie' and curso='$curso'");
if($dat->num_rows>0){
    while($reg=$dat->fetch_assoc()){
        if ($reg["bonificado"]==1) {
            $resp["error"]="bonificado";
            exit(json_encode($resp));
        }
        $resp["datos"]=$reg;
        $resp["error"]="ok";
    }
}
else $resp["error"]="no_inscrito";

$mysqli->close();
exit(json_encode($resp));