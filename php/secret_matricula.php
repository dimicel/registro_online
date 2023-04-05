<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");

if ($mysqli->errno>0) exit("server");

if (isset($_POST["matricula"]))$matricula=$_POST["matricula"];
if (isset($_POST["estado"])) $estado=$_POST["estado"];
$peticion=$_POST["peticion"];

if($peticion=="read"){
    $res=$mysqli->query("select * from matricula");
    $reg=$res->fetch_array(MYSQLI_ASSOC);
    echo json_encode($reg);
}
else if($peticion=="write") {
    $mysqli->query("update matricula set $matricula=$estado");
}
