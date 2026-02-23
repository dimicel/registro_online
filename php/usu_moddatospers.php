<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado'] !== "correcto") {
    exit("Acceso denegado");
}

include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->connect_errno) {
    exit("server_error");
}

$mysqli->set_charset("utf8");

// --- 1. RECOGIDA DE TODOS LOS DATOS (COMPLETO) ---
$id_nie = $_POST["dat_idnie"] ?? '';
$usuario = $_POST["dat_usuario"] ?? "alumno";

$email_recuperacion = $_POST["mod_email"] ?? '';
$nif = $_POST["mod_nif"] ?? '';
$pais = $_POST["mod_pais"] ?? '';
$es_pasaporte = isset($_POST["mod_es_pasaporte"]) ? 1 : 0;
$nombre = $_POST["mod_nombre"] ?? '';
$apellidos = $_POST["mod_apellidos"] ?? '';

$sexo = $_POST["dat_sexo"] ?? '';
$telefono = $_POST["dat_telefono"] ?? '';
$email = $_POST["dat_email"] ?? '';
$direccion = $_POST["dat_direccion"] ?? '';
$cp = $_POST["dat_cp"] ?? '';
$localidad = $_POST["dat_localidad"] ?? '';
$provincia = $_POST["dat_provincia"] ?? '';

$tutor1 = $_POST["dat_tutor1"] ?? '';
$telef_tut1 = $_POST["dat_telef_tut1"] ?? '';
$email_tut1 = $_POST["dat_email_tut1"] ?? '';
$tutor2 = $_POST["dat_tutor2"] ?? '';
$telef_tut2 = $_POST["dat_telef_tut2"] ?? '';
$email_tut2 = $_POST["dat_email_tut2"] ?? '';

$nss = trim($_POST["dat_nss"] ?? '');
$fecha_cambio_nuss = date('Y-m-d');

// --- FORMATEO DE FECHAS ---
// Usamos str_replace para asegurar que strtotime entienda el formato (cambiando / por -)
$nif_fecha_caducidad = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['mod_fecha_caducidad'])));
$fecha_nac = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['dat_fecha_nac'])));

// --- INICIO DE LA TRANSACCIÓN ---
$mysqli->begin_transaction();

try {
    // A. Asegurar existencia en usuarios_dat
    $stmtCheck = $mysqli->prepare("SELECT id_nie FROM usuarios_dat WHERE id_nie = ?");
    $stmtCheck->bind_param("s", $id_nie);
    $stmtCheck->execute();
    if ($stmtCheck->get_result()->num_rows == 0) {
        $stmtIns = $mysqli->prepare("INSERT INTO usuarios_dat (id_nie) VALUES (?)");
        $stmtIns->bind_param("s", $id_nie);
        $stmtIns->execute();
    }

    // B. Actualizar tabla 'usuarios'
    $sql1 = "UPDATE usuarios SET nombre=?, apellidos=?, id_nif=?, fecha_caducidad_id_nif=?, pais=?, es_pasaporte=?, email=? WHERE id_nie=?";
    $stmt1 = $mysqli->prepare($sql1);
    $stmt1->bind_param("sssssiss", $nombre, $apellidos, $nif, $nif_fecha_caducidad, $pais, $es_pasaporte, $email_recuperacion, $id_nie);
    if (!$stmt1->execute()) throw new Exception("Error en 'usuarios': " . $stmt1->error);

    // C. Actualizar tabla 'usuarios_dat' (Construcción con o sin NSS)
    if (strlen($nss) > 0) {
        $sql2 = "UPDATE usuarios_dat SET sexo=?, fecha_nac=?, telef_alumno=?, email=?, direccion=?, cp=?, localidad=?, provincia=?, tutor1=?, email_tutor1=?, tlf_tutor1=?, tutor2=?, email_tutor2=?, tlf_tutor2=?, num_ss=?, fecha_cambio_nss=? WHERE id_nie=?";
        $stmt2 = $mysqli->prepare($sql2);
        $stmt2->bind_param("sssssssssssssssss", $sexo, $fecha_nac, $telefono, $email, $direccion, $cp, $localidad, $provincia, $tutor1, $email_tut1, $telef_tut1, $tutor2, $email_tut2, $telef_tut2, $nss, $fecha_cambio_nuss, $id_nie);
    } else {
        $sql2 = "UPDATE usuarios_dat SET sexo=?, fecha_nac=?, telef_alumno=?, email=?, direccion=?, cp=?, localidad=?, provincia=?, tutor1=?, email_tutor1=?, tlf_tutor1=?, tutor2=?, email_tutor2=?, tlf_tutor2=? WHERE id_nie=?";
        $stmt2 = $mysqli->prepare($sql2);
        $stmt2->bind_param("sssssssssssssss", $sexo, $fecha_nac, $telefono, $email, $direccion, $cp, $localidad, $provincia, $tutor1, $email_tut1, $telef_tut1, $tutor2, $email_tut2, $telef_tut2, $id_nie);
    }
    if (!$stmt2->execute()) throw new Exception("Error en 'usuarios_dat': " . $stmt2->error);

    // D. BLOQUE CONDICIONAL (Segundo script): Solo si NO es 'alumno'
    if ($usuario !== 'alumno') {
        $tablas = $mysqli->query("SHOW TABLES");
        if ($tablas) {
            while ($fila = $tablas->fetch_row()) {
                $tablaActual = $fila[0];
                // Comprobamos si la tabla tiene la columna 'apellidos' para actualizarla
                $checkCol = $mysqli->query("SHOW COLUMNS FROM `$tablaActual` LIKE 'apellidos'");
                if ($checkCol && $checkCol->num_rows > 0) {
                    $sqlExtra = "UPDATE `$tablaActual` SET nombre=?, apellidos=?, email=?, id_nif=? WHERE id_nie=?";
                    $stmtExtra = $mysqli->prepare($sqlExtra);
                    $stmtExtra->bind_param("sssss", $nombre, $apellidos, $email_recuperacion, $nif, $id_nie);
                    if (!$stmtExtra->execute()) {
                        throw new Exception("Error en tabla dinámica $tablaActual: " . $stmtExtra->error);
                    }
                }
            }
        }
    }

    // --- FIN DE LA OPERACIÓN: COMMIT ---
    $mysqli->commit();
    echo "ok";

} catch (Exception $e) {
    // --- ERROR: ROLLBACK (Deshace todo lo anterior) ---
    $mysqli->rollback();
    exit("fallo: " . $e->getMessage());
}

$mysqli->close();
