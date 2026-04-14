<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 0);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");

$data=array();
if ($mysqli->errno>0) {
    exit("server");
}

$alta_mod=$_POST["alta_mod"];  //0 -> alta; 1 -> modificar
$tipo=$mysqli->real_escape_string($_POST["tipo"]);
$ruta=$mysqli->real_escape_string($_POST["ruta"]);
$parada=$mysqli->real_escape_string($_POST["parada"]);
$ruta_old=$mysqli->real_escape_string($_POST["ruta_old"]);
$parada_old=$mysqli->real_escape_string($_POST["parada_old"]);
$ruta_normalizada=normalizarTexto($ruta);
$parada_normalizada=normalizarTexto($parada);
$ruta_old_normalizada=normalizarTexto($ruta_old);
$parada_old_normalizada=normalizarTexto($parada_old);


if ($alta_mod==0){
    if ($tipo=="ruta"){
        $stmt_check = $mysqli->prepare("SELECT id_ruta FROM transporte_rutas WHERE ruta_normalizada = ?");
        $stmt_check->bind_param("s", $ruta_normalizada);
        $stmt_check->execute();
        $stmt_check->store_result(); // Necesario para usar num_rows

        if ($stmt_check->num_rows > 0) {
            $stmt_check->close();
            exit("alta_ruta_existente");
        } else {
            $stmt_check->close();

            $stmt_insert = $mysqli->prepare("INSERT INTO transporte_rutas (ruta, ruta_normalizada) VALUES (?, ?)");
            
            $stmt_insert->bind_param("ss", $ruta, $ruta_normalizada);
            
            if ($stmt_insert->execute()) {
                echo "ok_alta_ruta";
            } else {
                echo "Error al guardar: " . $mysqli->error;
            }
            
            $stmt_insert->close();
        }
    }
    else if ($tipo=="parada"){
        $stmt_check = $mysqli->prepare("SELECT id_ruta FROM transporte_paradas WHERE parada_normalizada = ? AND id_ruta=(SELECT id_ruta FROM transporte_rutas WHERE ruta=?)");
        $stmt_check->bind_param("ss", $parada_normalizada,$ruta);
        $stmt_check->execute();
        $stmt_check->store_result(); // Necesario para usar num_rows

        if ($stmt_check->num_rows > 0) {
            $stmt_check->close();
            exit("alta_parada_existente");
        } else {
            $stmt_check->close();

            $stmt_insert = $mysqli->prepare("INSERT INTO transporte_paradas (id_ruta, parada, parada_normalizada) VALUES ((SELECT id_ruta FROM transporte_rutas WHERE ruta=?),?, ?)");
            
            $stmt_insert->bind_param("sss", $ruta, $parada, $parada_normalizada);
            
            if ($stmt_insert->execute()) {
                echo "ok_alta_parada";
            } else {
                echo "Error al guardar: " . $mysqli->error;
            }
            
            $stmt_insert->close();
        }
    }
}
else if ($alta_mod==1){
    if($tipo=="ruta"){
        $stmt_check = $mysqli->prepare("SELECT id_ruta FROM transporte_rutas WHERE ruta_normalizada = ? AND id_ruta != (SELECT id_ruta FROM transporte_rutas WHERE ruta=?)");
        $stmt_check->bind_param("ss", $ruta_normalizada,$ruta_old);
        $stmt_check->execute();
        $stmt_check->store_result(); // Necesario para usar num_rows

        if ($stmt_check->num_rows > 0) {
            $stmt_check->close();
            exit("mod_ruta_existente");
        } 

        $stmt_check->close();

        $stmt_update = $mysqli->prepare("
            UPDATE transporte_rutas 
            SET ruta = ?, 
                ruta_normalizada = ? 
            WHERE ruta = ?
        ");

        $stmt_update->bind_param("sss", $ruta, $ruta_normalizada, $ruta_old);

        if ($stmt_update->execute()) {
            echo "mod_ruta_ok";
        } else {
            echo "Error al actualizar: ". $mysqli->error;
        }

        $stmt_update->close();

    }
    else if($tipo=="parada"){
        $stmt_check = $mysqli->prepare("SELECT id FROM transporte_paradas WHERE parada_normalizada = ? AND id != (SELECT id FROM transporte_paradas WHERE parada=?) AND id_ruta = (SELECT id_ruta FROM transporte_rutas WHERE ruta=?)");
        $stmt_check->bind_param("sss", $parada_normalizada,$parada_old,$ruta_old);
        $stmt_check->execute();
        $stmt_check->store_result(); // Necesario para usar num_rows

        if ($stmt_check->num_rows > 0) {
            $stmt_check->close();
            exit("mod_parada_existente");
        } 

        $stmt_check->close();

        $stmt_update = $mysqli->prepare("
            UPDATE transporte_paradas 
            SET parada = ?, 
                parada_normalizada = ? 
            WHERE parada=? AND id_ruta = (SELECT id_ruta FROM transporte_rutas WHERE ruta=? LIMIT 1) 
        ");

        $stmt_update->bind_param("ssss", $parada, $parada_normalizada, $parada_old, $ruta_old);

        if ($stmt_update->execute()) {
            echo "mod_parada_ok";
        } else {
            echo "Error al actualizar: ". $mysqli->error;
        }

        $stmt_update->close();
    }
} 



function normalizarTexto($texto) {
    // 1. Pasar a minúsculas
    $texto = mb_strtolower($texto, 'UTF-8');

    // 2. Mapa de sustitución de acentos y diéresis
    $buscar  = array('á', 'é', 'í', 'ó', 'ú', 'à', 'è', 'ì', 'ò', 'ù', 'ä', 'ë', 'ï', 'ö', 'ü');
    $reemplazar = array('a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u');
    $texto = str_replace($buscar, $reemplazar, $texto);

    // 3. Eliminar espacios en blanco extra al inicio, final y entre palabras
    $texto = trim(preg_replace('/\s+/', ' ', $texto));

    return $texto;
}

