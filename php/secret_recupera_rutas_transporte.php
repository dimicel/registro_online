<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");

$data=array();
if ($mysqli->errno>0) {
    $data["error"]="server";
    exit(json_encode($data));
}

if($_POST["ruta"]!=""){
    $ruta = $mysqli->real_escape_string($_POST["ruta"]);

    // Consultamos la ruta y sus paradas en un solo paso
    $consulta = "SELECT r.*, p.* FROM transporte_rutas r
                LEFT JOIN transporte_paradas p ON r.id_ruta = p.id_ruta
                WHERE r.ruta = '$ruta'";

    $res = $mysqli->query($consulta);

    if ($res->num_rows == 0) {
        $data["error"] = "ruta_no_existe";
        exit(json_encode($data));
    }

    $paradas = [];
    $info_ruta = null;

    while ($fila = $res->fetch_assoc()) {
        // Guardamos los datos de la ruta solo una vez
        if (!$info_ruta) {
            $info_ruta = $fila; 
        }
        
        // Si hay una parada (el id_parada no es null), la añadimos al array
        if ($fila['id_parada'] !== null) {
            $paradas[] = $fila;
        }
    }

    $data["error"] = "ok";
    $data["ruta"] = $info_ruta;
    $data["paradas"] = $paradas;

    exit(json_encode($data));
}
else{
    $consulta="select * from transporte_rutas order by ruta";
    $res=$mysqli->query($consulta);
    if ($res->num_rows==0){
        $data["error"]="sin_rutas";
        exit(json_encode($data));
    }
    $data["error"]="ok";
    $data["rutas"]=array();
    while ($reg=$res->fetch_assoc()){
        $data["rutas"][]=$reg;
    }
    exit(json_encode($data));
}

