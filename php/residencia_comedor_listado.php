<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

$data=array();
if ($mysqli->errno>0) {
    $data["error"]="server";
    exit(json_encode($data));
}


$curso=$_POST["curso"];
$fecha=DateTime::createFromFormat('d/m/Y', $_POST["fecha"]);
$fecha_mysql = $fecha->format('Y-m-d');

$lista_avisos="select * from residentes_comedor where fecha_no_comedor='$fecha_mysql'";
$con_avisos=$mysqli->query($lista_avisos);
$list_avisos=array();
if ($con_avisos->num_rows>0){
    while($d=$con_avisos->fetch_assoc(MYSQLI_ASSOC)){
        $list_avisos[]=$d["id_nie"];
    }
}
$con_avisos->free();

$consulta="SELECT * FROM residentes  where curso='$curso' and baja=0 ";
$res=$mysqli->query($consulta);

if ($res->num_rows==0){
    $data["error"]="sin_registros";
    exit(json_encode($data));
}
$data["error"]="ok";
$contador=0;
$data["registros"]=array();

while ($reg=$res->fetch_assoc()){
    if (in_array($reg["id_nie"],$list_avisos)) $data["registros"][$contador]["avisado"]=1;
    else $data["registros"][$contador]["avisado"]=0;
    $data["registros"][$contador]["id_nie"]= $reg["id_nie"];
    $data["registros"][$contador]["nombre"]=ucwords(strtolower($reg["apellidos"])).", ".ucwords(strtolower($reg["nombre"]));
    $contador++;
}
$res->free();
exit(json_encode($data));

