/////////////// DEVUELVE EL NÚMERO DE REGISTROS NO REVISADOS DE CADA TABLA

<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) {
    $data["error"]="server";
    exit(json_encode($data));
}

// Consulta SQL para obtener el número de registros de cada tabla con revisado=false
$sql = "SELECT COUNT(*) AS num_registros, table_name FROM information_schema.columns WHERE table_schema = '$dbname' AND column_name = 'procesado' AND column_default = '0' GROUP BY table_name";

$resultado = $conn->query($sql);

// Creación del array asociativo
$registros_no_revisados = array();
while($fila = $resultado->fetch_assoc()) {
    $registros_no_revisados[$fila['table_name']] = $fila['num_registros'];
}

// Cierre de la conexión
$conn->close();

$data["datos"]=$registros_no_revisados;
exit(json_encode($data));