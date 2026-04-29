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

$id_nie = $_POST["id_nie"];
$curso = $_POST["curso"];


$sql = "
    DELETE FROM transporte
    WHERE id_nie = ? AND curso = ? 
    AND cursa NOT IN (
        SELECT grupo AS cursa FROM mat_eso WHERE id_nie = ? AND curso = ?
        UNION ALL
        SELECT grupo AS cursa FROM mat_bach WHERE id_nie = ? AND curso = ?
        UNION ALL
        SELECT CONCAT(curso_ciclo, ' - GRADO ', grado, ' ', ciclo) AS cursa FROM mat_ciclos WHERE id_nie = ? AND curso = ?
        UNION ALL
        SELECT CONCAT(curso_ciclo, ' - FPB ', ciclo) AS cursa FROM mat_fpb WHERE id_nie = ? AND curso = ?
    )
";

$stmt = $mysqli->prepare($sql);

if ($stmt) {
    // Los 10 parámetros siguen siendo necesarios
    $stmt->bind_param("ssssssssss", $id_nie, $curso, $id_nie, $curso, $id_nie, $curso, $id_nie, $curso, $id_nie, $curso);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $resp["error"] = "ok";
            $stmt->close();

            $directorio = "../docs/" . $id_nie . "/transporte_escolar/" . $curso . "/";
            if (is_dir($directorio)) {
                $archivos = glob($directorio . "*");

                foreach ($archivos as $archivo) {
                    if (is_file($archivo)) {
                        unlink($archivo);
                    }
                }
            }

            exit(json_encode($resp));
        } else {
            $resp["error"] = "no_encontrado";
            $stmt->close();
            exit(json_encode($resp));
        } 
    } else {
        $resp["error"] = "Error en la ejecución: " . $stmt->error;
        $stmt->close();
        exit(json_encode($resp));
    }
} else {
    $resp["error"] = "Error en la preparación: " . $mysqli->error;
    exit(json_encode($resp));
}