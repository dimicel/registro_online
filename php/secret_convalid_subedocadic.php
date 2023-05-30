<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) {
    exit("servidor");
}
$registro=urldecode($_POST["registro"]);
$id_nie=urldecode($_POST["id_nie"]);
$anno_curso=urldecode($_POST["curso"]);
$dirRegistro=substr($registro, 17);

$mysqli->begin_transaction();
try{
    $stmt2 = $mysqli->prepare("INSERT INTO convalidaciones_docs (id_nie, registro, descripcion, ruta, subidopor) VALUES (?, ?, ?, ?, ?)");
    $descDoc="Resolucion";
    $subidopor="secretaria";
    $rutaTb="docs/".$id_nie."/convalidaciones"."/".$anno_curso."/".$dirRegistro."/docs/resolucion/resolucion.pdf";
    $stmt2->bind_param("sssss", $id_nie,$registro, $descDoc, $rutaTb, $subidopor);
    $stmt2->execute();
    $stmt2->close();
    $rutaCompleta=__DIR__."/../docs/".$id_nie."/"."convalidaciones/".$anno_curso."/".$dirRegistro."/docs/resolucion"."/";
    if (!is_dir($rutaCompleta)) {
        mkdir($rutaCompleta, 0777, true);
    }
    if(!move_uploaded_file($_FILES["documento"]["tmp_name"], $rutaCompleta."/resolucion.pdf")){
        $mysqli->rollback();
        exit("error_subida");
    }
    $mysqli->commit();
}
catch (Exception $e) {
    // En caso de error, revertir la transacción
    $mysqli->rollback();
    unlink($tempFile);
    exit("database");
}

$mysqli->close();
exit("ok");
