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
$dirRegistro=substr($registro, -17);
$subidopor=$_SESSION['tipo_usu'];
$nombre_doc=$_FILES["documento"]["name"];
$r=$mysqli->query("SELECT * FROM exencion_fct_docs WHERE registro='$registro' ");
if (!$r){
    exit("database");
}
else{
    $numFilas = $r->num_rows;
    $indice=sprintf("%02d", $numFilas+1)."_";
}

$rutaTb="docs/".$id_nie."/exencion_form_emp"."/".$anno_curso."/".$dirRegistro."/docs"."/".$indice.$nombre_doc;
$rutaCompleta=__DIR__."/../docs/".$id_nie."/"."exencion_form_emp/".$anno_curso."/".$dirRegistro."/docs"."/".$indice.$nombre_doc;
$ruta_dir=__DIR__."/../docs/".$id_nie."/"."exencion_form_emp/".$anno_curso."/".$dirRegistro."/docs";

if (!is_file($rutaCompleta)){
    $mysqli->begin_transaction();
    try{
        $stmt2 = $mysqli->prepare("INSERT INTO exencion_fct_docs (id_nie, registro, descripcion, ruta, subidopor) VALUES (?, ?, ?, ?, ?)");
        $stmt2->bind_param("sssss", $id_nie,$registro, $descripcion, $rutaTb, $subidopor);
        $stmt2->execute();
        $stmt2->close();
        
        if (!is_dir($ruta_dir)) {
            mkdir($ruta_dir, 0777, true);
        }
        if(!move_uploaded_file($_FILES["documento"]["tmp_name"], $rutaCompleta)){
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
}
else{
    if (!is_dir($ruta_dir)) {
        mkdir($ruta_dir, 0777, true);
    }
    if(!move_uploaded_file($_FILES["documento"]["tmp_name"], $rutaCompleta)){
        $mysqli->rollback();
        exit("error_subida");
    }
}

exit("ok");
