<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$ruta=$_POST["ruta"];
if (unlink($ruta)) exit("ok");
else exit("error");