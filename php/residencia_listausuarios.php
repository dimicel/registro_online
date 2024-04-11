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

$pagina=$_POST["pagina"];
$num_reg_pagina=$_POST["num_reg_pagina"];//Número de registros por página
$orden_direccion=$_POST["orden_direccion_usu"];
$tipo_residente=$_POST["tipo_residente"];
$buscar=$_POST["buscar"];

if (trim($buscar)==""){
    $consulta="SELECT * FROM usuarios ";
}
else {
    $consulta="SELECT * FROM usuarios WHERE apellidos LIKE '%$buscar%' OR nombre  LIKE '%$buscar%' OR id_nie  LIKE '%$buscar%'";
}
$res=$mysqli->query($consulta);
$data["num_registros"]=$res->num_rows;
$res->free();

$offset=($pagina-1)*$num_reg_pagina;
if (trim($buscar)==""){
    $consulta="SELECT * FROM usuarios ORDER BY apellidos $orden_direccion LIMIT $num_reg_pagina OFFSET $offset";
}
else {
    $consulta="SELECT * FROM usuarios WHERE apellidos LIKE '%$buscar%' OR nombre  LIKE '%$buscar%' OR id_nie  LIKE '%$buscar%' ORDER BY apellidos $orden_direccion LIMIT $num_reg_pagina OFFSET $offset";
}

$res=$mysqli->query($consulta);

if ($res->num_rows==0){
    $data["error"]="sin_registros";
    exit(json_encode($data));
}
$data["error"]="ok";
$contador=0;
$data["registros"]=array();

while ($reg=$res->fetch_assoc()){
    $data["registros"][$contador]["id_nie"]= $reg["id_nie"];
    $data["registros"][$contador]["nombre"]=ucwords(strtolower($reg["apellidos"])).", ".ucwords(strtolower($reg["nombre"]));
    $data["registros"][$contador]["email"]= $reg["email"];
    $data["registros"][$contador]["habilitado"]= $reg["habilitado"];
    $contador++;
}
$res->free();
exit(json_encode($data));

