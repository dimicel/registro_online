<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");

$data=array();
if ($mysqli->errno>0) {
    exit("server");
}

$tipo=$mysqli->real_escape_string($_POST["tipo"]);
$ruta=$mysqli->real_escape_string($_POST["ruta"]);
$parada=$mysqli->real_escape_string($_POST["parada"]);

if ($tipo=="ruta"){
$mysqli->begin_transaction();

try {
    // 1. Borramos las paradas usando una subconsulta para encontrar el ID
    $sql1 = "DELETE FROM transporte_paradas 
             WHERE id_ruta = (SELECT id_ruta FROM transporte_rutas WHERE ruta = ? LIMIT 1)";
    $stmt1 = $mysqli->prepare($sql1);
    $stmt1->bind_param("s", $ruta);
    $stmt1->execute();

    // 2. Ahora borramos la ruta principal por su nombre
    $sql2 = "DELETE FROM transporte_rutas WHERE ruta = ?";
    $stmt2 = $mysqli->prepare($sql2);
    $stmt2->bind_param("s", $ruta);
    $stmt2->execute();

    $mysqli->commit();
    exit("ok");
} catch (Exception $e) {
    $mysqli->rollback();
    exit("error");
}
}
else if ($tipo=="parada"){
    $consulta="DELETE FROM transporte_paradas WHERE parada='$parada' AND id_ruta='$ruta'";
    $mysqli->query($consulta);
}
