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


$modulo=$_POST['modulo'];
$codigo=$_POST['codigo'];
$accion=$_POST['accion'];
$id=$_POST['id'];
$modulo_normalizado = normalizar_nombre($modulo);
$codigo_normalizado = normalizar_nombre($codigo);

$mysqli->set_charset("utf8");


if ($accion=="alta") {
    $sql="SELECT * FROM modulosfp WHERE modulo='$modulo' AND codigo='$codigo'";
} elseif ($accion=="modifica") {
    $sql="SELECT * FROM modulosfp WHERE modulo='$modulo' AND id!='$id'";
}

$resultado=$mysqli->query($sql);
if ($mysqli->errno>0) {
    exit("server");
}
if ($resultado->num_rows>0) {
    exit("duplicado");
}

if ($accion=="alta") {
    $sql="SELECT * FROM modulosfp";
} elseif ($accion=="modifica") {
    $sql="SELECT * FROM modulosfp WHERE id!='$id'";
}
$coincidencia="";
$resultado=$mysqli->query($sql);   
if ($mysqli->errno>0) {
    exit("server");
}
if ($resultado->num_rows>0) {
    while ($row = $resultado->fetch_assoc()) {
        if (normalizar_nombre($row['modulo']) == $modulo_normalizado && normalizar_nombre($row['codigo']) == $codigo_normalizado) {
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
