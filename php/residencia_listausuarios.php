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
$curso=$_POST["curso"];
$buscar=$_POST["buscar"];

$offset=($pagina-1)*$num_reg_pagina;
if (trim($buscar)==""){
    $consulta="SELECT * FROM residentes r JOIN usuarios u where u.id_nie=r.id_nie AND r.curso=$curso ORDER BY u.apellidos $orden_direccion LIMIT $num_reg_pagina OFFSET $offset";
}
else {
    $consulta="SELECT * FROM residentes r JOIN usuarios u where u.id_nie=r.id_nie AND r.curso=$curso and (u.apellidos LIKE '%$buscar%' OR u.nombre  LIKE '%$buscar%' OR u.id_nie  LIKE '%$buscar%') ORDER BY u.apellidos $orden_direccion LIMIT $num_reg_pagina OFFSET $offset";
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
    $data["registros"][$contador]["id_nie"]= $reg["u.id_nie"];
    $data["registros"][$contador]["nombre"]=ucwords(strtolower($reg["u.apellidos"])).", ".ucwords(strtolower($reg["u.nombre"]));
    $data["registros"][$contador]["email"]= $reg["u.email"];
    $data["registros"][$contador]["habilitado"]= $reg["u.habilitado"];
    $data["registros"][$contador]["bonificado"]= $reg["r.bonificado"];
    $data["registros"][$contador]["devolucion_fianza"]= $reg["r.devolucion_fianza"];
    $contador++;
}
$res->free();
exit(json_encode($data));

