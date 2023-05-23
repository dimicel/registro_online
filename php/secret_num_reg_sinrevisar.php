<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) {
    $data["error"]="server";
    exit(json_encode($data));
}

$sql = "SELECT DISTINCT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'ulaboral_imp_sec_online' AND COLUMN_NAME = 'procesado'";
$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_array()) {
        $table = $row[0];

        // Verificar si la tabla cumple con el criterio
        $count_sql = "SELECT COUNT(*) FROM $table WHERE procesado = 0";
        $count_result = $mysqli->query($count_sql);
        $count_row = $count_result->fetch_array();
        $count = $count_row[0];

        $data["datos"][$table]=$count;
    }
}

// Cierre de la conexión
$mysqli->close();
$data["error"]="ok";
exit(json_encode($data));