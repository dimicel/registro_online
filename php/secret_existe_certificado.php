<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");

$id_nie=$_POST["id_nie"];
$curso=$_POST["curso"];


if (is_file(("../docs/".$id_nie."/certificado_notas"."/".$curso."/".$id_nie.".pdf"))) exit("ok");
else exit ("no");

