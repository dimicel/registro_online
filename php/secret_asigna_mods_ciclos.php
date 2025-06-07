<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado'] !== "correcto") exit("Acceso denegado");
include("conexion.php");

if ($mysqli->errno > 0) {
    exit("server");
}

$id = $_POST['id'];
$lista_mods = json_decode($_POST['lista_mods'], true); // true -> array asociativo

if (!is_numeric($id) || !is_array($lista_mods)) {
    exit("datos_invalidos");
}

$mysqli->set_charset("utf8");

try {
    $mysqli->begin_transaction();

    // Eliminar los registros previos con ese ID
    $stmt_del = $mysqli->prepare("DELETE FROM ciclos_modulos WHERE id = ?");
    if (!$stmt_del) throw new Exception("Error al preparar DELETE");

    $stmt_del->bind_param("i", $id);
    if (!$stmt_del->execute()) throw new Exception("Error al ejecutar DELETE");

    $stmt_del->close();

    // Preparar la inserción
    $stmt_ins = $mysqli->prepare("INSERT INTO ciclos_modulos (id, curso, codigo, modulo) VALUES (?, ?, ?, ?)");
    if (!$stmt_ins) throw new Exception("Error al preparar INSERT");

    foreach ($lista_mods as $fila) {
        if (!is_array($fila) || count($fila) !== 3) continue; // Ignorar filas mal formadas

        [$curso, $codigo, $modulo] = $fila;
        $stmt_ins->bind_param("isss", $id, $curso, $codigo, $modulo);

        if (!$stmt_ins->execute()) throw new Exception("Error al insertar fila");
    }

    $stmt_ins->close();

    $mysqli->commit();
    echo "ok";

} catch (Exception $e) {
    $mysqli->rollback();
    error_log("Error en asignación de módulos: " . $e->getMessage()); // Log interno
    exit("server"); // Respuesta genérica al cliente
}
