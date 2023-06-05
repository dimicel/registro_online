<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
$mysqli->set_charset("utf8");
$respuesta=array();

if ($mysqli->errno>0){
    $respuesta["error"]="server";
    exit(json_encode($respuesta));
} 

$id_nie=$_POST["id_nie"];
$curso=$_POST["curso"];
$cursos=array(
    "pm2eso"=>"premat_eso",
    "pm3eso"=>"premat_eso",
    "pm4eso"=>"premat_eso",
    "pm3esd"=>"premat_eso",
    "pm4esd"=>"premat_eso",
    "pm1bac"=>"premat_bach",
    "pm1bah"=>"premat_bach",
    "pm1bag"=>"premat_bach",
    "pm2bac"=>"premat_bach",
    "pm2bah"=>"premat_bach",
);

$cursos2=array(
    "pm2eso"=>"2º ESO",
    "pm3eso"=>"3º ESO",
    "pm4eso"=>"4º ESO",
    "pm3esd"=>"3º ESO DIV",
    "pm4esd"=>"4º ESO DIV",
    "pm1bac"=>"1º Bach. Ciencias",
    "pm1bah"=>"1º Bach. HH.CC.SS.",
    "pm1bag"=>"1º Bach. General",
    "pm2bac"=>"2º Bach. Ciencias y Tecnología",
    "pm2bah"=>"2º Bach. HH.CC.SS.",
);


$campos="apellidos,nombre";
$consulta="(select $campos from premat_eso where id_nie='$id_nie' and curso='$curso') union all";
$consulta.="(select $campos from premat_bach where id_nie='$id_nie' and curso='$curso')";

$res=$mysqli->query($consulta);

if ($res->num_rows>0) {
    $reg=$res->fetch_array(MYSQLI_ASSOC);
    $respuesta["error"]="ok";
    $respuesta["apellidos"]=$reg["apellidos"];
    $respuesta["nombre"]=$reg["nombre"];
    $res->free();
    exit(json_encode($respuesta));
}

else {
    $res->free();
    $respuesta["error"]="noexiste";
    exit(json_encode($respuesta));
}

