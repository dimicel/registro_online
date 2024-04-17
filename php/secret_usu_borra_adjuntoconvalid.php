<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");
if ($mysqli->errno>0) {
    exit("server");
}
$ruta=$_POST["ruta"];
$_rutadb=substr($ruta,3);

$mysqli->begin_transaction();

try {
    // Eliminar el registro de la base de datos
    $sql = "delete from convalidaciones_docs where ruta=?";
    $stmt = $mysqli->prepare($sql);
    $id = 1; // Aquí debes especificar el ID del registro que deseas borrar
    $stmt->bind_param("s", $_rutadb);
    $stmt->execute();

    // Eliminar el archivo del servidor
    if (!unlink($ruta)) {
        throw new Exception("No se pudo eliminar el archivo");
    }

    // Confirmar la transacción
    $mysqli->commit();
    
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $mysqli->rollback();
    $mysqli->close();
    exit("error");
}

// Cerrar la conexión
$mysqli->close();
exit("ok");

