<?php
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