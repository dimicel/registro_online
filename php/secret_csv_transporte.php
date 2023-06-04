<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$error="";
$Datos="";

include("conexion.php");
if ($mysqli->errno>0) $error="Error en servidor.";

$curso=$_POST["curso_csv_transporte"];

$res=$mysqli->query("select * from transporte where curso='$curso' order by cursa,apellidos,nombre");

if ($res->num_rows==0){
    $error="No hay solicitudes de transporte.";
}

$Name = 'transporte_'.$curso.'.csv';
$FileName = "./$Name";

$Datos='NIE;ALUMNO;CURSO_ACTUAL;ESTUDIOS;CP;LOCALIDAD;PROVINCIA;RUTA Y PARADA;SILLA_RUEDAS'.PHP_EOL;

header('Expires: 0');
header('Cache-control: private');
header('Content-Type: application/x-octet-stream;charset=utf-8'); // Archivo de Excel
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Content-Description: File Transfer');
header('Last-Modified: '.date('D, d M Y H:i:s'));
header('Content-Disposition: attachment; filename="'.$Name.'"');
header("Content-Transfer-Encoding: binary");

if ($error!="") {
    echo $error;
    exit();
}
while($r=$res->fetch_array(MYSQLI_ASSOC)){
    if(substr(strtoupper($r["id_nie"]),0,1)== "P") continue;
    $Datos.="'".utf8_decode($r["id_nie"])."'".";";
    $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
    $Datos.=utf8_decode($r["curso"].";");
    $Datos.=utf8_decode($r["cursa"].";");
    $Datos.=utf8_decode($r["cp"].";");
    $Datos.=utf8_decode($r["localidad"].";");
    $Datos.=utf8_decode($r["provincia"].";");
    $Datos.=utf8_decode($r["ruta"]);
    if($r["sillaruedas"]==1)$Datos.="SI".PHP_EOL;
    else $Datos.="NO".PHP_EOL;	
}

echo $Datos;

