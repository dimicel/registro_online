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


$valor=$_POST['valor'];
$tipo_input=$_POST['tipo_input'];
$accion=$_POST['accion'];
$id=$_POST['id'];
$valor_normalizado = normalizar_nombre($valor);

$mysqli->set_charset("utf8");


if ($accion=="alta" && $tipo_input=="nombre") {
    $sql="SELECT * FROM departamentos WHERE departamento='$valor'";
} elseif ($accion=="modifica" && $tipo_input=="nombre") {
    $sql="SELECT * FROM departamentos WHERE departamento='$valor' AND id!='$id'";
} elseif ($accion=="alta" && $tipo_input=="abreviatura"){
    $sql="SELECT * FROM departamentos WHERE abreviatura='$valor'";
} elseif ($accion=="modifica" && $tipo_input=="abreviatura") {
    $sql="SELECT * FROM departamentos WHERE abreviatura='$valor' AND id!='$id'";
} 

$resultado=$mysqli->query($sql);
if ($mysqli->errno>0) {
    exit("server");
}
if ($resultado->num_rows>0) {
    exit("duplicado");
}

$hacer_comprobacion=false;
if ($accion=="alta" && $tipo_input=="nombre") {
    $sql="SELECT * FROM departamentos";
    $hacer_comprobacion=true;
} elseif ($accion=="modifica" && $tipo_input=="nombre") {
    $sql="SELECT * FROM departamentos WHERE id!='$id'";
    $hacer_comprobacion=true;
}
$coincidencia="";
if ($hacer_comprobacion) {
    $resultado=$mysqli->query($sql);   
    if ($mysqli->errno>0) {
        exit("server");
    }
    if ($resultado->num_rows>0) {
        while ($row = $resultado->fetch_assoc()) {
            if (normalizar_nombre($row['departamento']) == $valor_normalizado) {
                $coincidencia = "duplicado_normalizado";
                break;
            }
        }
    }
}

if($coincidencia=="duplicado_normalizado") {
    exit("duplicado_normalizado");
} else {
    exit("ok");
}   
