<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$error="";
$Datos="";

include("conexion.php");
if ($mysqli->errno>0) $error="Error en servidor.";

$curso=$_POST["curso_csv_seguro"];

$anno_calculo=substr($curso,0,4);

$res=$mysqli->query("SELECT usuarios.apellidos, usuarios.nombre, usuarios.id_nie, usuarios_dat.num_ss FROM usuarios INNER JOIN usuarios_dat ON usuarios.id_nie=usuarios_dat.id_nie where usuarios_dat.num_ss is not NULL and usuarios_dat.num_ss<>''  ORDER BY usuarios.apellidos ASC, usuarios.nombre ASC");

if ($res->num_rows==0){
    $error="No hay registros que listar.";
}

$Name = 'listado_num_ss.csv';
$FileName = "./$Name";

$Datos='NIE;ALUMNO;Nº SEGURIDAD SOCIAL'.PHP_EOL;

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
    if(substr(strtoupper($r["id_nie"]),0,1)== "P") continue;//Los NIE que empiezan por P son usuarios de prueba

    $Datos.="'".utf8_decode($r["id_nie"])."'".";";
    $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
    $Datos.=utf8_decode($r["num_ss"].";");
    $Datos.=utf8_decode($paga_seguro).PHP_EOL;		
}

echo $Datos;
