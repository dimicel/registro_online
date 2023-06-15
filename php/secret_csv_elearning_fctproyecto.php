<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$error="";
$Datos="";

include("conexion.php");
if ($mysqli->errno>0) $error="Error en servidor.";


$curso=$_POST["curso_csv_elearning_fctproyecto"];
$turno="E-Learning_f";
$curso="2023-2024";

$consulta="select * from mat_ciclos where turno='$turno' and curso='$curso' order by apellidos,nombre";
$res=$mysqli->query($consulta);

if ($res->num_rows==0){
    $error="No hay matrículas.";
}

$Name = 'matricula_elearning_fct_proyecto.csv';
$FileName = "./$Name";

$Datos='NIE;APELLIDOS;NOMBRE;NIF;REGISTRO;GRADO;CICLO;NUEVO_DE_OTRA_COMUNIDAD;EMAIL;TELEFONO;MAYOR_28_AÑOS;PROYECTO;FCT'.PHP_EOL;
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
    $Datos.=utf8_decode(ucwords(strtolower($r["id_nif"])).";");
    $Datos.=utf8_decode(ucwords(strtolower($r["registro"])).";");
    $Datos.=utf8_decode(ucwords(strtolower($r["grado"])).";");
    $Datos.=utf8_decode(ucwords(strtolower($r["ciclo"])).";");
    $Datos.=utf8_decode(ucwords(strtolower($r["al_nuevo_otracomunidad"])).";");
    $Datos.=utf8_decode(ucwords(strtolower($r["email"])).";");
    $Datos.=utf8_decode(ucwords(strtolower($r["telefono"])).";");
    $Datos.=utf8_decode(ucwords(strtolower($r["mayor_28"])).";");
    $Datos.=utf8_decode(ucwords(strtolower($r["proyecto"])).";");
    $Datos.=utf8_decode(ucwords(strtolower($r["fct"]))).PHP_EOL;			
}

echo $Datos;

