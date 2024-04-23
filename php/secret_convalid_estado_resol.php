<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
$mysqli->set_charset("utf8");
require_once('tcpdf/config/tcpdf_config_alt.php');
require_once('tcpdf/tcpdf.php');
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

if ($res_cen>0) $act_rescen=1;
else $act_rescen=0;
if ($res_con>0) $act_rescon=1;
else $act_rescon=0;
if ($res_min>0) $act_resmin=1;
else $act_resmin=0;

$consulta_act_estado="update convalidaciones set resuelve_cen='$act_rescen', resuelve_con='$act_rescon', resuelve_min='$act_resmin' where registro='$registro'";

$mysqli->begin_transaction();

try {
    // Iterar sobre los arrays y actualizar los registros en la base de datos
    for ($i = 0; $i < count($modulos); $i++) {
        if ($estados[$i]==""){
            if ($estados[$i]=="") $elementos_sin_resolver=true;
            continue;
        }
        $sql = "UPDATE convalidaciones_modulos SET resolucion = '" . $estados[$i] . "', motivo_no_favorable = '" . $motivos[$i] . "', resuelto_por = '" . $resuelto_por[$estados[$i]] . "' WHERE registro = '$registro' AND modulo='$modulos[$i]'";

        if ($mysqli->query($sql) !== TRUE) {
            throw new Exception("error_db");
        }
    }
    
    if ($mysqli->query($consulta_act_estado) !== TRUE) {
        throw new Exception("error_db_conval");
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

//Salida del script
if ($elementos_sin_resolver) exit("elementos_sin_resolver");
if($res_cen==0){
    if($res_con==0 && $res_min>0) exit("ok_ministerio");
    elseif($res_con>0 && $res_min==0) exit("ok_consejeria");
    elseif($res_con>0 && $res_min>0) exit("ok_consejeria_ministerio");
}

//Se genera el pdf para el alumno si están todos los módulos resueltos y, al menos, hay uno que resuelve el centro
$concov=$mysqli->query("select * from convalidaciones where registro='$registro'");
if($concov->num_rows<1){
    exit("no_datospdf");
}



//Salida OK
exit("ok");

/*
$sql = "UPDATE convalidaciones SET resolucion='$estado' WHERE registro='$registro'";
$result = $mysqli->query($sql);
if ($mysqli->affected_rows > 0) {
    $mysqli->close();
    exit("ok");
}
else {
    $mysqli->close();
    exit("no_registro");
}
*/
