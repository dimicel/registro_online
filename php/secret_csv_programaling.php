<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$error="";
$Datos="";

include("conexion.php");
if ($mysqli->errno>0) $error="Error en servidor.";


$curso=$_POST["curso_csv_prog_ling"];

$consulta="select id_nie,apellidos,nombre,curso,prog_ling,grupo from mat_eso where curso='$curso' order by apellidos,nombre,grupo";


$res=$mysqli->query($consulta);

if ($res->num_rows==0){
    $error="No hay matrículas.";
}

$Name = 'eso_programa_ling.csv';
$FileName = "./$Name";

$Datos='NIE;APELLIDOS;NOMBRE;GRUPO;PROGRAMA LINGÜÍSTICO'.PHP_EOL;
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
    $Datos.=utf8_decode($r["id_nie"].";");
    $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).";");
    $Datos.=utf8_decode(ucwords(strtolower($r["nombre"])).";");
    $Datos.=utf8_decode(ucwords(strtolower($r["grupo"])).";");
    $Datos.=utf8_decode(ucwords(strtolower($r["prog_ling"]))).PHP_EOL;			
}

echo $Datos;

