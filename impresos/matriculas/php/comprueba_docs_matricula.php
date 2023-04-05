<?php
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

exit ($partes);