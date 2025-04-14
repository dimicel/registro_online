<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

$data=array();
if ($mysqli->errno>0) {
    exit("server");
}

$id_nie=$_POST["id_nie"];
$curso=$_POST["curso"];
$departamento=$_POST["departamento"];
$apellidos=$_POST["apellidos"];
$nombre=$_POST["nombre"];
$id_nif=$_POST["id_nif"];
$curso_ciclo=$_POST["curso_ciclo"];
$grado=$_POST["grado"];
$ciclo=$_POST["ciclo"];
$valoracion=$_POST["valoracion"];
$motivo=$_POST["motivo"];
$registro=$_POST["registro"];
$dirRegistro=$_POST["dirRegistro"];

if (isset($_POST['firma'])){
    $imageData = urldecode($_POST['firma']);
    if (!is_dir(__DIR__."/../../../docs/tmp")) mkdir(__DIR__."/../../../docs/tmp", 0777);
    
    // Generar el archivo temporal
    $tempFile = tempnam(__DIR__."/../../../docs/tmp", 'canvas_' . session_id());
    
    // Asegurarse de que la extensión sea '.png' y no haya caracteres extra
    $tempFile = pathinfo($tempFile, PATHINFO_DIRNAME) . '/' . basename($tempFile, '.tmp') . '.png';
    
    // Guardar el archivo de imagen
    file_put_contents($tempFile, base64_decode(str_replace('data:image/png;base64,', '', $imageData)));
    $firma = $tempFile;
}


$consulta="SELECT * FROM exencion_fct  where curso='$curso' and departamento='$departamento' ";

$data["consulta"]=$consulta;

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
    exit("sin_registro");
}




