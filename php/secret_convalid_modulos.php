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
$id_nie=$_POST["id_nie"];

$sql = "SELECT *  FROM convalidaciones_modulos WHERE registro = '$registro' and id_nie='$id_nie' ORDER BY modulo";
$result = $mysqli->query($sql);
$contador=0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data["datos"][$contador]["modulo"]=$row["modulo"];
        $data["datos"][$contador]["resuelto_por"]=$row["resuelto_por"];
        $data["datos"][$contador]["estado"]=$row["estado"];
        $data["datos"][$contador]["motivo_no_favorable"]=$row["motivo_no_favorable"];
        $contador++;
    }
    $mysqli->close();
    $data["error"]="ok";
    exit(json_encode($data));
}
else {
    $data["error"]="sin_modulos";
    $mysqli->close();
    exit(json_encode($data));
}

