<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");

$id_nie=$_POST["id_nie"];
$curso=$_POST["curso"];


if (is_file(("../docs/".$id_nie."/certificado_notas"."/".$curso."/".$id_nie.".pdf"))) exit("ok");
else exit ("no");

