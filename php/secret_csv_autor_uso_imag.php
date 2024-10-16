<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$error="";
$Datos="";

include("conexion.php");
if ($mysqli->errno>0) $error="Error en servidor.";

$curso=$_POST["curso_csv_autor_uso_imagenes"];

$Name = 'listado_autorizacion_fotos.csv';
$FileName = "./$Name";


$Datos='CURSO;NIE;ALUMNO;AUTORIZA_USO_FOTOS'.PHP_EOL;


$res=$mysqli->query("SELECT * FROM mat_eso where curso='$curso' ORDER BY grupo,apellidos,nombre");
if ($res->num_rows>0){
    while($r=$res->fetch_array(MYSQLI_ASSOC)){
        if(substr(strtoupper($r["id_nie"]),0,1)== "P") continue;//Los NIE que empiezan por P son usuarios de prueba
        $Datos.=$r["grupo"].";";
        $Datos.=$r["id_nie"].";";
        $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
        $Datos.=utf8_decode($r["autoriza_fotos"].";").PHP_EOL;	
    }
}
$res->free();



$res=$mysqli->query("SELECT * FROM mat_bach where curso='$curso' ORDER BY grupo,apellidos,nombre");
if ($res->num_rows>0){
    while($r=$res->fetch_array(MYSQLI_ASSOC)){
        if(substr(strtoupper($r["id_nie"]),0,1)== "P") continue;//Los NIE que empiezan por P son usuarios de prueba
        $Datos.=$r["grupo"].";";
        $Datos.=$r["id_nie"].";";
        $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
        $Datos.=utf8_decode($r["autoriza_fotos"].";").PHP_EOL;	
    }
}
$res->free();


$res=$mysqli->query("SELECT * FROM mat_ciclos where curso='$curso' ORDER BY ciclo,curso_ciclo,apellidos,nombre");
if ($res->num_rows>0){
    while($r=$res->fetch_array(MYSQLI_ASSOC)){
        if(substr(strtoupper($r["id_nie"]),0,1)== "P") continue;//Los NIE que empiezan por P son usuarios de prueba
        $Datos.=$r["ciclo"]." ".$r["curso_ciclo"].";";
        $Datos.=$r["id_nie"].";";
        $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
        $Datos.=utf8_decode($r["autoriza_fotos"].";").PHP_EOL;	
    }
}
$res->free();

$res=$mysqli->query("SELECT * FROM mat_fpb where curso='$curso' ORDER BY ciclo,curso_ciclo,apellidos,nombre");
if ($res->num_rows>0){
    while($r=$res->fetch_array(MYSQLI_ASSOC)){
        if(substr(strtoupper($r["id_nie"]),0,1)== "P") continue;//Los NIE que empiezan por P son usuarios de prueba
        $Datos.=$r["ciclo"]." ".$r["curso_ciclo"].";";
        $Datos.=$r["id_nie"].";";
        $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
        $Datos.=utf8_decode($r["autoriza_fotos"].";").PHP_EOL;	
    }
}
$res->free();


header('Expires: 0');
header('Cache-control: private');
header('Content-Type: application/x-octet-stream;charset=utf-8'); // Archivo de Excel
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Content-Description: File Transfer');
header('Last-Modified: '.date('D, d M Y H:i:s'));
header('Content-Disposition: attachment; filename="'.$Name.'"');
header("Content-Transfer-Encoding: binary");



echo $Datos;
