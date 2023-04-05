<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$error="";
$Datos="";

include("conexion.php");
if ($mysqli->errno>0) $error="Error en servidor.";

$curso=$_POST["curso_csv_seguro"];

$anno_calculo=substr($curso,0,4);

$res=$mysqli->query("select * from mat_ciclos where curso='$curso' order by turno,grado,ciclo,curso_ciclo,apellidos,nombre");

if ($res->num_rows==0){
    $error="No hay registros que listar.";
}

$Name = 'seguro_escolar_ciclos_'.$curso.'.csv';
$FileName = "./$Name";

$Datos='NIE;ALUMNO;CURSO_ACTUAL;TURNO;GRADO;CICLO;CURSO;FECHA_NAC;EDAD;PAGA_SEGURO'.PHP_EOL;

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
    $paga_seguro="SI";

    $f_n=explode("-",$r["fecha_nac"]);
    $fecha_nac=$f_n[2]."-".$f_n[1]."-".$f_n[0];
    $a_nac=$f_n[0];
    
    $edad=intval($anno_calculo)-intval($a_nac);
    if($edad >= 28) $paga_seguro="NO";

    $Datos.="'".utf8_decode($r["id_nie"])."'".";";
    $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
    $Datos.=utf8_decode($r["curso"].";");
    $Datos.=utf8_decode($r["turno"].";");
    $Datos.=utf8_decode($r["grado"].";");
    $Datos.=utf8_decode($r["ciclo"].";");
    $Datos.=utf8_decode($r["curso_ciclo"].";");
    $Datos.=utf8_decode($fecha_nac.";");
    $Datos.=utf8_decode($edad.";");
    $Datos.=utf8_decode($paga_seguro).PHP_EOL;		
}

echo $Datos;

