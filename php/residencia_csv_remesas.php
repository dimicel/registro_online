<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$error="";
$Datos="";

include("conexion.php");
if ($mysqli->errno>0) $error="Error en servidor.";

$curso=$_POST["curso_csv_remesas"];

$res=$mysqli->query("select * from residentes where curso='$curso' order by apellidos,nombre");

if ($res->num_rows==0){
    $error="No hay inscripciones en residencia.";
}

$Name = 'remesas_'.date('d-m-Y').'.csv';
$FileName = "./$Name";
$Datos="Fecha y hora: ".date("d/m/Y H:i:s").PHP_EOL;
$Datos.='NIE;APELLIDOS;NOMBRE;EDIFICIO;CURSO_ACTUAL;DIRECCION;CP;LOCALIDAD;PROVINCIA;TITULAR_CUENTA;IBAN;BAJA;FECHA_BAJA;BONIFICADO;FIANZA'.PHP_EOL;

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
    $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).";");
    $Datos.=utf8_decode(ucwords(strtolower($r["nombre"])).";");
    $Datos.=utf8_decode($r["edificio"].";");
    $Datos.=utf8_decode($r["curso"].";");
    $Datos.=utf8_decode($r["direccion"].";");
    $Datos.=utf8_decode($r["cp"].";");
    $Datos.=utf8_decode($r["localidad"].";");
    $Datos.=utf8_decode($r["provincia"].";");
    $Datos.=utf8_decode($r["titular_cuenta"].";");
    $Datos.=utf8_decode($r["iban"].";");
    if($r["baja"]==1)$Datos.="SI;";
    else $Datos.="NO;";	
    $Datos.=utf8_decode($r["fecha_baja"].";");
    if($r["bonificado"]==1)$Datos.="SI;";
    else $Datos.="NO;";
    $Datos.=$r["fianza"].PHP_EOL;
}

echo $Datos;

