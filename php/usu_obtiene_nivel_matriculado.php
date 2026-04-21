<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");

if ($mysqli->errno>0) exit("server");

$id_nie=$_POST["id_nie"];
$curso=$_POST["curso"];

$sql = "
    SELECT 'mat_eso' AS tabla FROM mat_eso WHERE id_nie = ? AND curso = ?
    UNION ALL
    SELECT 'mat_bach' AS tabla FROM mat_bach WHERE id_nie = ? AND curso = ?
    UNION ALL
    SELECT 'mat_ciclos' AS tabla FROM mat_ciclos WHERE id_nie = ? AND curso = ?
    UNION ALL
    SELECT 'mat_fpb' AS tabla FROM mat_fpb WHERE id_nie = ? AND curso = ?
";

$stmt = $mysqli->prepare($sql);

if ($stmt) {
    
    $stmt->bind_param("ssssssss", $id_nie, $curso, $id_nie, $curso, $id_nie, $curso, $id_nie, $curso);
    
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($fila = $resultado->fetch_assoc()) {
        echo $fila['tabla'];
    } else {
        echo "no_matricula";
    }

    $stmt->close();
} else {
    echo "Error en la preparación: " . $mysqli->error;
}




