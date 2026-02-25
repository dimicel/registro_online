<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$error="";
$Datos="";

include("conexion.php");
if ($mysqli->errno>0) $error="Error en servidor.";

$curso=$_POST["curso_csv_doc_id"];

$res=$mysqli->query("
    SELECT 
        u.apellidos, 
        u.nombre, 
        u.id_nie, 
        u.fecha_caducidad_id_nif,
        u.pais,
        u.id_nif,
        u.es_pasaporte
    FROM usuarios u
    WHERE (
          EXISTS (
              SELECT 1 
              FROM mat_ciclos mc 
              WHERE mc.id_nie = u.id_nie 
                AND mc.curso = '$curso'
          )
          OR
          EXISTS (
              SELECT 1 
              FROM mat_fpb mf 
              WHERE mf.id_nie = u.id_nie 
                AND mf.curso = '$curso'
          )
          OR
          EXISTS (
              SELECT 1 
              FROM mat_eso me 
              WHERE me.id_nie = u.id_nie 
                AND me.curso = '$curso'
          )
          OR
          EXISTS (
              SELECT 1 
              FROM mat_bach mb 
              WHERE mb.id_nie = u.id_nie 
                AND mb.curso = '$curso'
          )
      )
    ORDER BY u.apellidos ASC, u.nombre ASC
");

if ($res->num_rows==0){
    $error="No hay registros que listar.";
}

$Name = 'listado_num_doc_'.$curso.'.csv';
$FileName = "./$Name";

$Datos='NIE;ALUMNO;Nº DOCUMENTO;ES_PASAPORTE;FECHA_CADUCIDAD;PAIS'.PHP_EOL;

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
    $Datos.=utf8_decode("\t".$r["id_nif"].";");
    $Datos.=utf8_decode(($r["es_pasaporte"]?"1":"0").";");
    $Datos.=utf8_decode($r["fecha_caducidad_id_nif"].";");
    $Datos.=utf8_decode($r["pais"]).PHP_EOL;		
}

echo $Datos;

