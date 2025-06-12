<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");

	
if ($mysqli->errno>0) {
    exit("server");
}

$id_nie=$_POST["id_nie"];
$lista_fechas = json_decode($_POST['lista_fechas'], true);

$fechas = array_column($lista_fechas, 0);

$mysqli->begin_transaction();

try {
    // Borrado
    if (count($fechas) > 0) {
        $placeholders = implode(',', array_fill(0, count($fechas), '?'));
        $sql = "DELETE FROM residentes_comedor WHERE id_nie = ? AND fecha_no_comedor IN ($placeholders)";
        $stmt = $mysqli->prepare($sql);
        $types = str_repeat('s', count($fechas) + 1);
        $params = array_merge([$id_nie], $fechas);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        if ($stmt->errno > 0) throw new Exception("Error en borrado");
        $stmt->close();
    }

    // Inserción
    $sql_insert = "INSERT INTO residentes_comedor (id_nie, fecha_no_comedor) VALUES (?, ?)";
    $stmt_insert = $mysqli->prepare($sql_insert);
    foreach ($lista_fechas as $item) {
        if ($item[1] == 1) {
            $stmt_insert->bind_param("ss", $id_nie, $item[0]);
            $stmt_insert->execute();
            if ($stmt_insert->errno > 0) throw new Exception("Error en inserción");
        }
    }
    $stmt_insert->close();

    $mysqli->commit();
    exit("ok");
} catch (Exception $e) {
    $mysqli->rollback();
    exit("fallo_alta");
}

exit("ok"); 