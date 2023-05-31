<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) {
    $data["error"]="server";
    exit(json_encode($data));
}
$registro=$_POST["registro"];

$sql = "SELECT *  FROM convalidaciones_docs WHERE registro = '$registro' ORDER BY ruta";
$result = $mysqli->query($sql);
$contador=0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data["datos"][$contador]["descripcion"]=$row["descripcion"];
        $data["datos"][$contador]["ruta"]=$row["ruta"];
        $contador++;
    }
    $mysqli->close();
    $data["error"]="ok";
    exit(json_encode($data));
}
else {
    $data["error"]="sin_adjuntos";
    $mysqli->close();
    exit(json_encode($data));
}

