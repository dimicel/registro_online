<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");
if ($mysqli->errno>0) {
    exit("server");
}
$ruta=$_POST["ruta"];
$tabla=$_POST["tabla"];
$_rutadb=substr($ruta,3);//$ruta viene con ../ delante. $_rutadb es la ruta sin eso
exit($ruta . "     ".$tabla);
$registro="";
$res=$mysqli->query("select * from $tabla  where ruta='$_rutadb'");
if($res->num_rows>0){
    while($reg=$res->fetch_assoc()){
        $registro=$reg["registro"];
    }
}

try {
    // Eliminar el registro de la base de datos
    $sql = "delete from $tabla where ruta=?";
    $stmt = $mysqli->prepare($sql);
    $id = 1; // Aquí debes especificar el ID del registro que deseas borrar
    $stmt->bind_param("s", $_rutadb);
    $stmt->execute();

    // Eliminar el archivo del servidor
    if (file_exists($ruta)){
        if (!unlink($ruta)) {
            throw new Exception("error");
        }
    }
    

    // Confirmar la transacción
    $mysqli->commit();
    
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $mysqli->rollback();
    $mysqli->close();
    exit($e->getMessage());
}

$res_consejeria=$mysqli->query("select * from convalidaciones_docs where registro='$registro' and descripcion='Resolución de Consejería'");
$res_ministerio=$mysqli->query("select * from convalidaciones_docs where registro='$registro' and descripcion='Resolución del Ministerio'");
if ($res_consejeria->num_rows>0) $resuelto_consejeria=1;
else $resuelto_consejeria=0;
if ($res_ministerio->num_rows>0) $resuelto_ministerio=1;
else $resuelto_ministerio=0;
$mysqli->query("UPDATE convalidaciones SET resuelto_con='$resuelto_consejeria',resuelto_min='$resuelto_ministerio' WHERE registro='$registro'");

// Cerrar la conexión
$mysqli->close();
exit("ok");

