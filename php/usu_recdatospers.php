<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) {
    $resp["error"]="server";
    exit(json_encode($resp));
}
// Escapar la variable para evitar Inyección SQL
$id_nie = $mysqli->real_escape_string($_POST["id_nie"]);
$resp = array();

// Usamos JOIN para traer campos de ambas tablas. 
// He usado 'u.*' para la tabla usuario y 'ud.*' para usuarios_dat
$sql = "SELECT ud.*, 
               u.*, 
               u.email AS email_recuperacion, 
               ud.email AS email_alumno 
        FROM usuarios_dat ud
        LEFT JOIN usuario u ON ud.id_nie = u.id_nie 
        WHERE ud.id_nie = '$id_nie'";

$dat = $mysqli->query($sql);

if($dat && $dat->num_rows > 0){
    // No hace falta un while si solo esperas un registro único por ID
    $reg = $dat->fetch_assoc();
    $resp["datos"] = $reg;
    $resp["error"] = "ok";
} else {
    $resp["error"] = "no_usuarios";
}

$mysqli->close();
exit(json_encode($resp));