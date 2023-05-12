<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("../../../php/conexion.php");

if ($mysqli->errno>0) {
    exit("servidor");
}

$mysqli->set_charset("utf8");
require_once(__DIR__.'/../../../php/tcpdf/config/tcpdf_config_alt.php');
require_once(__DIR__.'/../../../php/tcpdf/tcpdf.php');

function generaRegistro(){
    $minus="abcdefghijklmnopqrstuvwxyz";
    $nums="0123456789";
    $array=array("","","","","","","","");
    $registro="";
    $array[0]=substr($nums,mt_rand(0,strlen("mayus")-1),1);
    $array[1]=substr($minus,mt_rand(0,strlen("minus")-1),1);
    $array[2]=substr($nums,mt_rand(0,strlen("nums")-1),1);
    $array[3]=substr($minus,mt_rand(0,strlen("mayus")-1),1);
    $array[4]=substr($nums,mt_rand(0,strlen("minus")-1),1);
    $array[5]=substr($minus,mt_rand(0,strlen("nums")-1),1);
    $array[6]=substr($nums,mt_rand(0,strlen("mayus")-1),1);
    $array[7]=substr($minus,mt_rand(0,strlen("signos")-1),1);
    shuffle($array);
    return "iesulabto_conval_".date('dmY')."_".$array[0].$array[1].$array[2].$array[3].$array[4].$array[5].$array[6].$array[7];   
}



$id_nie = urldecode($_POST['id_nie']);
$anno_curso = urldecode($_POST['curso']);
$formulario = urldecode($_POST['formulario']);
$nombre = urldecode($_POST['nombre']);
$apellidos = urldecode($_POST['apellidos']);
$id_nif = urldecode($_POST['id_nif']);
$direccion = urldecode($_POST['direccion']);
$cp = urldecode($_POST['cp']);
$localidad = urldecode($_POST['localidad']);
$provincia = urldecode($_POST['provincia']);
$tlf_fijo = urldecode($_POST['tlf_fijo']);
$tlf_movil = urldecode($_POST['tlf_movil']);
$email = urldecode($_POST['email']);
$grado = urldecode($_POST['grado']);
$ciclo = urldecode($_POST['ciclo']);
$modulos = urldecode($_POST['modulos']);












//GENERA EL ARCHIVO NUEVO
$nombre_fichero=$registro . '.pdf';
if (!is_dir(__DIR__."/../../../docs/".$id_nie))mkdir(__DIR__."/../../../docs/".$id_nie,0777);
if(!is_dir(__DIR__."/../../../docs/".$id_nie."/convalidaciones"))mkdir(__DIR__."/../../../docs/".$id_nie."/convalidaciones",0777);
if(!is_dir(__DIR__."/../../../docs/".$id_nie."/convalidaciones"."/".$anno_curso))mkdir(__DIR__."/../../../docs/".$id_nie."/convalidaciones"."/".$anno_curso,0777);
$ruta=__DIR__."/../../../docs/".$id_nie."/"."convalidaciones/".$anno_curso."/". $nombre_fichero;///hay qu añadir el directorio de cada solicitud de convalidación
$pdf->Output($ruta, 'F');
//FIN GENERA PDF
exit("envio_ok ");