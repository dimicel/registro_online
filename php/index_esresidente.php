<?php
session_start();
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");
header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");	

if ($mysqli->errno>0) {
    exit("server");
}

$id_nie=$_POST["usuario"];

$consulta=$mysqli->query("select * from residentes where id_nie='$id_nie'");
if ($consulta->num_rows>0){
    $consulta->free();
    exit("si");
} 
else{
    $consulta->free();
    exit("no");
}


