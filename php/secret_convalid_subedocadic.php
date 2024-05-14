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
$descripcion=urldecode($_POST["descripcion"]);
$dirRegistro=substr($registro, 17);
$subidopor=$_SESSION['tipo_usu'];

$r=$mysqli->query("SELECT * FROM convalidaciones_docs WHERE registro='$registro' AND subidopor='$subidopor'");
if (!$r){
    exit("database");
}
else{
    $numFilas = $r->num_rows;
    $indice=sprintf("%02d", $numFilas+1)."_";
}

$mysqli->begin_transaction();
try{
    $stmt2 = $mysqli->prepare("INSERT INTO convalidaciones_docs (id_nie, registro, descripcion, ruta, subidopor) VALUES (?, ?, ?, ?, ?)");
    $rutaTb="docs/".$id_nie."/convalidaciones"."/".$anno_curso."/".$dirRegistro."/docs"."/".$indice.$_FILES["documento"]["name"];
    $stmt2->bind_param("sssss", $id_nie,$registro, $descripcion, $rutaTb, $subidopor);
    $stmt2->execute();
    $stmt2->close();
    $rutaCompleta=__DIR__."/../docs/".$id_nie."/"."convalidaciones/".$anno_curso."/".$dirRegistro."/docs"."/";
    if (!is_dir($rutaCompleta)) {
        mkdir($rutaCompleta, 0777, true);
    }
    if(!move_uploaded_file($_FILES["documento"]["tmp_name"], $rutaCompleta.$indice.$_FILES["documento"]["name"])){
        $mysqli->rollback();
        exit("error_subida");
    }
    $mysqli->commit();
}
catch (Exception $e) {
    // En caso de error, revertir la transacción
    $mysqli->rollback();
    exit("database");
}

$mysqli->close();
exit("ok");
