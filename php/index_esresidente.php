<?php
session_start();
include("conexion.php");
include("funciones.php");
header("Content-Type: text/html;charset=utf-8");
header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");	

$respuesta=array("error"=>"", "esresidente"=>"","mes"=>date("n"),"anno_inicio"=>calculaCurso_ini());
if ($mysqli->errno>0) {
    $respuesta["error"]="server";
    exit(json_encode($respuesta));
}

$id_nie=$_POST["usuario"];
$curso=calculaCurso_ini();
$curso=(string)$curso . "-" . (string)($curso+1);

$consulta=$mysqli->query("select * from residentes where id_nie='$id_nie' and curso='$curso'");
if ($consulta->num_rows>0){
    $res=$consulta->fetch_assoc(MYSQLI_ASSOC);
    /*$baja=$res["baja"];
    if ($baja==0) {
        $respuesta["esresidente"]="si";
        $respuesta["error"]="ok";
    } 
    else if ($baja==1) {
        $respuesta["esresidente"]="no";
        $respuesta["error"]="ok";
    }
    $consulta->free();
    exit(json_encode($respuesta));*/
} 
else{
    $consulta->free();
    $respuesta["esresidente"]="no";
    $respuesta["error"]="ok";
    exit(json_encode($respuesta));
}


