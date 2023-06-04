<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");
    
if ($mysqli->errno>0) {
    exit("server");
}

$id_nie=$_POST["id_nie"];
$habilitar=$_POST["habilitar"];

$consulta="UPDATE usuarios SET habilitado=$habilitar where id_nie='$id_nie'";
$mysqli->query($consulta);
if ($mysqli->errno>0) {
    exit("fallo");
}

exit("ok");



