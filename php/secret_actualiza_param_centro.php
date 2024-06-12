<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

$data=array();
if ($mysqli->errno>0) {
    exit("server");
}


if ($conn->query($sql) === TRUE) {
    if ($conn->affected_rows > 0) {
        exit("ok");
    } else {
        exit("DATABASE");
    }
} else {
    exit("Error al actualizar el registro: " . $conn->error);
}


