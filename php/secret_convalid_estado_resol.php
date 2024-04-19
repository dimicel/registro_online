<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) {
    exit("server");
}
$registro=$_POST["registro"];
$modulos=$_POST["modulo_convalid"];
$estados=$_POST["estado_convalid"];
$motivos=$_POST["motivo_no_fav_convalid"];
$elementos_sin_resolver=false;
$resuelto_por=array(
    "FAVORABLE"=>"CENTRO",
    "NO FAVORABLE"=>"CENTRO",
    "CONSEJERIA"=>"CONSEJERIA",
    "MINISTERIO"=>"MINISTERIO"

);
$res_cen=0;
$res_con=0;
$res_min=0;
for ($i=0; $i<count($estados);$i++){
    if($estados[$i]=="FAVORABLE" || $estados[$i]=="NO FAVORABLE") $res_cen++;
    elseif($estados[$i]=="CONSEJERIA") $res_con++;
    elseif($estados[$i]=="MINISTERIO") $res_min++;
}



$mysqli->begin_transaction();

try {
    // Iterar sobre los arrays y actualizar los registros en la base de datos
    for ($i = 0; $i < count($modulos); $i++) {
        if ($estados[$i]==""){
            if ($estados[$i]=="") $elementos_sin_resolver=true;
            continue;
        }
        $sql = "UPDATE tu_tabla SET resolucion = '" . $estados[$i] . "', motivo_no_favorable = '" . $motivos[$i] . "', RESUELTO_POR = '" . $resuelto_por[$estados[$i]] . "' WHERE registro = '$registro' AND modulo='$modulos[$i]'";

        if ($mysqli->query($sql) !== TRUE) {
            throw new Exception("error_db");
        }
    }

    // Confirmar la transacción
    $mysqli->commit();
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $mysqli->rollback();
    $mysqli->close();
    exit ($e->getMessage());
}

// Cerrar conexión
$mysqli->close();

if ($elementos_sin_resolver) exit("elementos_sin_resolver");
if($res_cen==0){
    if($res_con==0 && $res_min>0) exit("ok_ministerio");
    elseif($res_con>0 && $res_min==0) exit("ok_consejeria");
    elseif($res_con>0 && $res_min>0) exit("ok_consejeria_ministerio");
}
exit("ok");

/*$sql = "UPDATE convalidaciones SET resolucion='$estado' WHERE registro='$registro'";
$result = $mysqli->query($sql);
if ($mysqli->affected_rows > 0) {
    $mysqli->close();
    exit("ok");
}
else {
    $mysqli->close();
    exit("no_registro");
}*/
