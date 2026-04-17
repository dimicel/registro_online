<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");

$id_nie=$_POST["id_nie"];
$curso=$_POST["curso"];

$partes="";

if (is_file(("../../../docs/".$id_nie."/dni"."/".$id_nie."-A.jpeg"))) $partes.="A";
else $partes.=' ';
if (is_file(("../../../docs/".$id_nie."/dni"."/".$id_nie."-R.jpeg"))) $partes.="R";
else $partes.=' ';
if (is_file(("../../../docs/fotos/".$id_nie.".jpeg"))) $partes.='F';
else $partes.=' ';
if (is_file(("../../../docs/".$id_nie."/seguro"."/".$curso."/".$id_nie.".jpeg"))) $partes.='S';
else $partes.=' ';
if (is_file(("../../../docs/".$id_nie."/certificado_notas"."/".$curso."/".$id_nie.".pdf"))) $partes.='C';
else $partes.=' ';
if (is_file(("../../../docs/".$id_nie."/nss/nss_".$id_nie."jpeg"))) $partes.="N";
else $partes.=' ';

exit ($partes);