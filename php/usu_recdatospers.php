<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

// 1. Cambiamos la forma de detectar error de conexión
if ($mysqli->connect_errno) {
    $resp["error"] = "server";
    $resp["detalle"] = $mysqli->connect_error; // Esto te ayudará a ver qué pasa
    exit(json_encode($resp));
}

$id_nie = $mysqli->real_escape_string($_POST["id_nie"]);
$resp = array();

// 2. Consulta SQL con alias
$sql = "SELECT ud.*, 
               u.*, 
               u.email AS email_recuperacion, 
               ud.email AS email_alumno 
        FROM usuarios_dat ud
        LEFT JOIN usuario u ON ud.id_nie = u.id_nie 
        WHERE ud.id_nie = '$id_nie'";

$dat = $mysqli->query($sql);

// 3. Verificamos si la CONSULTA falló (esto daría error 500 si no se controla)
if (!$dat) {
    $resp["error"] = "query";
    $resp["detalle"] = $mysqli->error; // Te dirá si falta una tabla o columna
    exit(json_encode($resp));
}

if($dat->num_rows > 0){
    $reg = $dat->fetch_assoc();
    $resp["datos"] = $reg;
    $resp["error"] = "ok";
} else {
    $resp["error"] = "no_usuarios";
}

$mysqli->close();
exit(json_encode($resp));