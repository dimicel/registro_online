<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");


$resp = array();

// 1. Cambiamos la forma de detectar error de conexión

if ($mysqli->connect_errno) {
    $resp["error"] = "server";
    $resp["detalle"] = $mysqli->connect_error; // Esto te ayudará a ver qué pasa
    exit(json_encode($resp));
}

$id_nie = $mysqli->real_escape_string($_POST["id_nie"]);
$curso = $mysqli->real_escape_string($_POST["curso"]);


$sql = "
    SELECT cursa FROM transporte
    WHERE id_nie = ? AND curso = ? AND cursa=(SELECT grupo AS cursa FROM mat_eso WHERE id_nie = ? AND curso = ?
                                            UNION ALL
                                            SELECT grupo AS cursa FROM mat_bach WHERE id_nie = ? AND curso = ?
                                            UNION ALL
                                            SELECT CONCAT(curso_ciclo, ' - GRADO ',grado, ' ', ciclo) AS cursa FROM mat_ciclos WHERE id_nie = ? AND curso = ?
                                            UNION ALL
                                            SELECT CONCAT(curso_ciclo, ' - FPB ', ciclo) AS cursa FROM mat_fpb WHERE id_nie = ? AND curso = ?)
";

$stmt = $mysqli->prepare($sql);

if ($stmt) {
    
    $stmt->bind_param("ssssssssss", $id_nie, $curso, $id_nie, $curso, $id_nie, $curso, $id_nie, $curso, $id_nie, $curso);
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        echo $fila['cursa'];
    } else {
        echo "no_matricula";
    }

    $stmt->close();
} else {
    echo "Error en la preparación: " . $mysqli->error;
}