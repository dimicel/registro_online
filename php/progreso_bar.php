<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$sesion_id=$_SESSION['ID'];
/*
include("conexion.php");

if ($mysqli->errno>0) exit("0/0");


$rec=$mysqli->query("select * from progreso where id='$sesion_id'");
if ($rec->num_rows>0){
    $prog=$rec->fetch_assoc();
    exit(strval($prog["procesados"])."/".strval($prog["total"]));
}
else {
    exit("0/0");
}
*/
$resp=Array();

if(file_exists("excel/".$sesion_id.".csv")){
    $pBar=fopen("excel/".$sesion_id.".csv",'r');
    $dato=Array();
    $dato=fgetcsv($pBar,0);
    $resp["procesado"]=$dato[0];
    $resp["total"]=$dato[1];
    fclose($pBar);
    //exit($dato[0]."/".$dato[1]);
    exit (json_encode($resp));
}
