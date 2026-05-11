<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("../../../php/conexion.php");

$resp["error"]="ok";
if ($mysqli->errno>0) {
    $resp["error"]="servidor";
    exit(json_encode($resp));
}


$curso=$_POST["curso"];
$años = explode("-", $curso);

// Restamos uno a cada año
$año_inicio_anterior = (int)$años[0] - 1;
$año_fin_anterior = (int)$años[1] - 1;

// Volvemos a unir con el formato deseado
$curso_anterior = $año_inicio_anterior . "-" . $año_fin_anterior;

$sql="SELECT * FROM mat_ciclos WHERE curso = ?";
$stmt = $mysqli->prepare($sql); // Asignamos a $stmt

if ($stmt) {
    $stmt->bind_param("s", $curso);
    $stmt->execute();
    
    // Obtenemos el conjunto de resultados de la sentencia
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $resp["datos"] = $resultado->fetch_assoc();
    } else {
        $resp["error"] = "no_existe";
    }
    $stmt->close();
} else {
    $resp["error"] = "error_sql";
}

exit(json_encode($resp));