<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$url=$_POST["url"];
if(file_exists($url.".jpeg")||file_exists($url.".JPEG")) exit (".jpeg");
elseif(file_exists($url.".jpg")||file_exists($url.".JPG")) exit (".jpg");
else exit("no_existe");


