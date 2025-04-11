<?php
include("php/conexion.php");
header("Content-Type: text/html;charset=utf-8");
header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if ($mysqli->errno>0) {
    exit("server");
}
$mysqli->set_charset("utf8");

$pass=password_hash('#online@Ulab0ral#',PASSWORD_BCRYPT);
$conCadena="update usuarios set password='$pass' where id_nie='S4500175G'";
if ($mysqli->query($conCadena)===TRUE) echo "ok secretaría<br>";
else echo "error secretaría<br>";

$pass=password_hash('#Res@Ulab0ral#',PASSWORD_BCRYPT);
$conCadena="update usuarios set password='$pass' where id_nie='S4500175GRES'";
if ($mysqli->query($conCadena)===TRUE) echo "ok residencia<br>";
else echo "error residencia<br>";

$pass=password_hash('#Jef@Ulab0ral#',PASSWORD_BCRYPT);
$conCadena="update usuarios set password='$pass' where id_nie='S4500175GJEF'";
if ($mysqli->query($conCadena)===TRUE) echo "ok jefatura";
else echo "error jefatura";

//S4500175GJDE ES USUARIO COMÚN PARA TODOS LOS JEFES DE DPTO. LA CONTRASEÑA SE RESTAURA DESDE LA APLICACIÓN ENTRANDO COMO SECRETARIO
//MENU CONFIGURACIÓN->DEPARTAMENTOS


$mysqli->close();
