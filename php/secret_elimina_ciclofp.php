<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) {
    exit("server");
}

$id=(int)$_POST['id'];

$mysqli->set_charset("utf8");
$mysqli->begin_transaction();

try {
    // Eliminar de ciclos_modulos
    $stmt1 = $mysqli->prepare("DELETE FROM ciclos_modulos WHERE id = ?");
    $stmt1->bind_param("i", $id);
    $stmt1->execute();
    $modulosEliminados = $stmt1->affected_rows;

    // Eliminar de ciclos
    $stmt2 = $mysqli->prepare("DELETE FROM ciclos WHERE id = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $ciclosEliminados = $stmt2->affected_rows;

    $mysqli->commit();

    if ($ciclosEliminados==0) exit("no");
    exit("ok");

    //echo "Eliminados: $modulosEliminados en ciclos_modulos, $ciclosEliminados en ciclos.";

} catch (Exception $e) {
    $mysqli->rollback();
    exit("Error al eliminar registros: " . $e->getMessage());
}





