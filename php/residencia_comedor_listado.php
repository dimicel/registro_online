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

$lista_dia="select * from residentes_comedor where fecha_comedor='$fecha_mysql'";
$con_dia=$mysqli->query($lista_dia);
$list_dia=array();
if ($con_dia->num_rows>0){
    while($d=$con_dia->fetch_assoc(MYSQLI_ASSOC)){
        $list_dia[]=[$d["id_nie"],$d["desayuno"],$d["comida"],$d["cena"]];
    }
}
$con_dia->free();

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
    $indice = false;
    $data["test"] = ""; // Initialize test variable
    $data["long"] =count($list_dia); 
    for ($i = 0; $i < count($list_dia); $i++) {
        $data["test"].= $list_dia[$i][0];
        if (isset($list_dia[$i][0]) && $list_dia[$i][0] == $reg["id_nie"]) {
            $indice = $i;
            break;
        }
    }
    $data["indice"] = $indice; // Store the index for debugging 
    //foreach ($list_dia as $i => $subarray) {
        
    //    if (isset($subarray[0]) && $subarray[0] == $reg["id_nie"]) {
    //        $indice = $i;
    //        break;
    //    }
    //}
    $data["registros"][$contador]["id_nie"]= $reg["id_nie"];
    $data["registros"][$contador]["nombre"]=ucwords(strtolower($reg["apellidos"])).", ".ucwords(strtolower($reg["nombre"]));
    if($indice!==false){
        $data["registros"][$contador]["desayuno"]=$list_dia[$indice][1];
        $data["registros"][$contador]["comida"]=$list_dia[$indice][2];
        $data["registros"][$contador]["cena"]=$list_dia[$indice][3];
    }
    else{
        $data["registros"][$contador]["desayuno"]=0;
        $data["registros"][$contador]["comida"]=0;
        $data["registros"][$contador]["cena"]=0;
    }
    $contador++;
}
$res->free();
exit(json_encode($data));

