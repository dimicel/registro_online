<?php

session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) {
    exit("server");
}

if ($_POST["procedimiento"]=="datos"){
    $nombre=$_POST["nombre"];
    $apellidos=$_POST["apellidos"];
    $email=$_POST["email"];
    $id_nie=$_POST["id_nie"];
    $nif_nie=$_POST["nif_nie"];
    $consulta="update usuarios set nombre='$nombre',apellidos='$apellidos',email='$email',id_nif='$nif_nie' where id_nie='$id_nie'";
}
elseif ($_POST["procedimiento"]=="password"){
    $password=password_hash($_POST["password"],PASSWORD_BCRYPT);
    $id_nie=$_POST["id_nie"];
    $consulta="update usuarios set password='$password' where id_nie='$id_nie'";
}
else exit("Error de acceso");

if($mysqli->query($consulta)==TRUE) exit("ok");
else exit("Fallo:".$mysqli->error);

$mysqli->close();