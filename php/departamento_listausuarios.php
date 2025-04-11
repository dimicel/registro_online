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

$pagina=$_POST["res_pagina"];
$num_reg_pagina=$_POST["num_reg_pagina"];//Número de registros por página
$orden_direccion=$_POST["res_orden_direccion_usu"];
$curso=$_POST["curso"];
$buscar=$_POST["buscar"];
$departamento=$_POST["departamento"];


$offset=($pagina-1)*$num_reg_pagina;

$consulta="SELECT * FROM residentes  where curso='$curso' and departamento='$departamento' ";
$sql = "SELECT COUNT(*) AS total FROM residentes where curso='$curso'  and departamento='$departamento' ";



if (trim($buscar)==""){
    //$consulta="SELECT * FROM residentes r JOIN usuarios u where u.id_nie=r.id_nie AND r.curso=$curso ORDER BY u.apellidos $orden_direccion LIMIT $num_reg_pagina OFFSET $offset";
    $consulta.=" ORDER BY apellidos $orden_direccion LIMIT $num_reg_pagina OFFSET $offset";
}
else {
    //$consulta="SELECT * FROM residentes r JOIN usuarios u where u.id_nie=r.id_nie AND r.curso=$curso and (u.apellidos LIKE '%$buscar%' OR u.nombre  LIKE '%$buscar%' OR u.id_nie  LIKE '%$buscar%') ORDER BY u.apellidos $orden_direccion LIMIT $num_reg_pagina OFFSET $offset";
    $consulta.=" and (apellidos LIKE '%$buscar%' OR nombre  LIKE '%$buscar%' OR id_nie  LIKE '%$buscar%') ORDER BY apellidos $orden_direccion LIMIT $num_reg_pagina OFFSET $offset";
    $sql.=" and (apellidos LIKE '%$buscar%' OR nombre  LIKE '%$buscar%' OR id_nie  LIKE '%$buscar%')";
}

$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    // Obtener el resultado
    $resultado=$result->fetch_assoc();
    $data["num_registros"] = $resultado['total'];
} else {
    $data["num_registros"] = 0;
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
    $data["registros"][$contador]["registro"]= $reg["registro"];
    
    if (is_file(("../docs/".$reg["id_nie"]."/residencia/sepa_".$reg["id_nie"].".pdf"))){
        $data["registros"][$contador]["sepa"]="docs/".$reg["id_nie"]."/residencia/sepa_".$reg["id_nie"].".pdf";
    }
    else{
        $data["registros"][$contador]["sepa"]="";
    }
    $contador++;
}
$res->free();
exit(json_encode($data));

