<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$ruta=$_POST["ruta"];
$nuevo_n=$_POST["nuevo_n"];

if (file_exists($nuevo_n)) exit ("duplicado");

if (rename($ruta, $nuevo_n)) {
    exit("ok");
  } else {
    exit("error");
  }