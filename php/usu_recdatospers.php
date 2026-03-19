<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
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
$sql = "SELECT u.*, 
       ud.*, 
       u.email AS email_recuperacion, 
       ud.email AS email_alumno 
FROM usuarios u
LEFT JOIN usuarios_dat ud ON u.id_nie = ud.id_nie 
WHERE u.id_nie = '$id_nie'";

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

    // Inicializamos a 0 por defecto por si el campo es NULL o no ha caducado
    $resp["datos"]["documento_caducado"] = 0;

    if (!empty($reg["fecha_caducidad_id_nif"])) {
        // Comparamos: si la fecha de la DB es menor que la fecha de hoy ("today" a las 00:00:00)
        if (strtotime($reg["fecha_caducidad_id_nif"]) < strtotime("today")) {
            $resp["datos"]["documento_caducado"] = 1;
        }
    }

    $resp["error"] = "ok";
} else {
    $resp["error"] = "no_usuarios";
}

$mysqli->close();
exit(json_encode($resp));