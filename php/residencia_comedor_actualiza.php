<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) {
    exit("server");
}

$curso=$_POST["curso"];
$fecha=DateTime::createFromFormat('d/m/Y', $_POST["fecha"]);
$fecha_mysql = $fecha->format('Y-m-d');
$asistencias = json_decode($_POST["asistencias"], true);

$mysqli->begin_transaction();
try {
    foreach ($asistencias as $asistencia) {
        $id_nie = $asistencia[0];
        $desayuno = $asistencia[1];
        $comida = $asistencia[2];
        $cena = $asistencia[3];

        // De momento conservo el día elegido por el usuario para no ir al comedor, aunque haya asistido.
        // El código comentado eliminaba ese registro si el usuario, pese haber marcado que no iba al comedor se presenta.
        // 1. Eliminar si hay registro en fecha_no_comedor para ese id_nie y fecha
        //if($desayuno==1 || $comida==1 || $cena==1) {
        //    $sql_del = "DELETE FROM residentes_comedor WHERE id_nie = ? AND fecha_no_comedor = ?";
        //    $stmt_del = $mysqli->prepare($sql_del);
        //    $stmt_del->bind_param("ss", $id_nie, $fecha);
        //    $stmt_del->execute();
        //    $stmt_del->close();
        //}

        // 2. Comprobar si existe registro para id_nie y fecha_comedor
        $sql_check = "SELECT COUNT(*) FROM residentes_comedor WHERE id_nie = ? AND fecha_comedor = ?";
        $stmt_check = $mysqli->prepare($sql_check);
        $stmt_check->bind_param("ss", $id_nie, $fecha);
        $stmt_check->execute();
        $stmt_check->bind_result($existe);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($existe) {
            // 3. Si existe, actualizar
            $sql_update = "UPDATE residentes_comedor SET desayuno = ?, comida = ?, cena = ? WHERE id_nie = ? AND fecha_comedor = ?";
            $stmt_update = $mysqli->prepare($sql_update);
            $stmt_update->bind_param("iiiss", $desayuno, $comida, $cena, $id_nie, $fecha);
            $stmt_update->execute();
            $stmt_update->close();
        } else {
            // 4. Si no existe, insertar
            $sql_insert = "INSERT INTO residentes_comedor (id_nie, fecha_comedor, desayuno, comida, cena) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $mysqli->prepare($sql_insert);
            $stmt_insert->bind_param("ssiii", $id_nie, $fecha, $desayuno, $comida, $cena);
            $stmt_insert->execute();
            $stmt_insert->close();
        }
    }
    $mysqli->commit();
    exit("ok");
} catch (Exception $e) {
    $mysqli->rollback();
    exit("fallo_alta");
}



