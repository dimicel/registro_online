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
$curso=calculaCurso_ini();
$curso=$curso . "-" . $curso+1;

$consulta=$mysqli->query("select * from residentes where id_nie='$id_nie' and curso='$curso'");
if ($consulta->num_rows>0){
    $consulta->free();
    exit("si");
} 
else{
    $consulta->free();
    exit("no");
}

function calculaCurso_ini(){
    $mes=(int)date("n");
    $anno=(int)date("Y");
    if ($mes>=7 && $mes<=12) 
        return $anno;
    else
        return $anno-1;
}
