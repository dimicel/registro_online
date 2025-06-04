<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
include("funcion_normalizacion_valor.php");
header("Content-Type: text/html;charset=utf-8");

$data=array();
if ($mysqli->errno>0) {
    exit("server");
}


$ciclo=$_POST['ciclo_ciclo'];
$accion=$_POST['accion'];
$id=$_POST['id'];
$ciclo_normalizado = normalizar_nombre($ciclo);

$mysqli->set_charset("utf8");


if ($accion=="alta") {
    $sql="SELECT * FROM ciclos WHERE descripcion='$ciclo'";
} elseif ($accion=="modifica") {
    $sql="SELECT * FROM modulosfp WHERE descripcion='$ciclo' AND id!='$id'";
}

$resultado=$mysqli->query($sql);
if ($mysqli->errno>0) {
    exit("server");
}
if ($resultado->num_rows>0) {
    exit("duplicado");
}

if ($accion=="alta") {
    $sql="SELECT * FROM ciclos";
} elseif ($accion=="modifica") {
    $sql="SELECT * FROM ciclos WHERE id!='$id'";
}
$coincidencia="";
$resultado=$mysqli->query($sql);   
if ($mysqli->errno>0) {
    exit("server");
}
if ($resultado->num_rows>0) {
    while ($row = $resultado->fetch_assoc()) {
        if (normalizar_nombre($row['descripcion']) == $ciclo_normalizado) {
            $coincidencia = "duplicado_normalizado";
            break;
        }
    }
}

if($coincidencia=="duplicado_normalizado") {
    exit("duplicado_normalizado");
} else {
    exit("ok");
}   
