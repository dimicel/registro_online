<?php
/*session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) {
    $data["error"]="server";
    exit(json_encode($data));
}
$curso=$_POST["curso"];

$sql = "SELECT DISTINCT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'ulaboral_imp_sec_online' AND COLUMN_NAME = 'procesado'";
$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_array()) {
        $table = $row[0];

        // Verificar si la tabla cumple con el criterio
        $count_sql = "SELECT COUNT(*) FROM $table WHERE procesado = 0 and curso='$curso'";
        $count_result = $mysqli->query($count_sql);
        $count_row = $count_result->fetch_array();
        $count = $count_row[0];

        $data["datos"][$table]=$count;
    }
}

// Cierre de la conexión
$mysqli->close();
$data["error"]="ok";
exit(json_encode($data));
*/
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado'] !== "correcto") {
    exit("Acceso denegado");
}

include("conexion.php");
header("Content-Type: application/json; charset=utf-8"); // Cambiado a JSON ya que devuelves un json_encode

if ($mysqli->connect_errno) {
    $data["error"] = "server";
    exit(json_encode($data));
}

$curso = $_POST["curso"] ?? ''; // Usamos null coalescing para evitar avisos si no llega el post

// 1. Obtenemos las tablas y, de paso, verificamos si tienen la columna 'pasado_delphos'
$sql = "SELECT TABLE_NAME, 
               MAX(CASE WHEN COLUMN_NAME = 'pasado_delphos' THEN 1 ELSE 0 END) as tiene_delphos
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = 'ulaboral_imp_sec_online' 
          AND COLUMN_NAME IN ('procesado', 'pasado_delphos')
        GROUP BY TABLE_NAME
        HAVING MAX(CASE WHEN COLUMN_NAME = 'procesado' THEN 1 ELSE 0 END) = 1";

$result = $mysqli->query($sql);

$data["datos"] = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $table = $row['TABLE_NAME'];
        $tieneDelphos = $row['tiene_delphos'];

        // Conteo estándar de 'procesado'
        // Nota: He usado sentencias preparadas idealmente, pero mantengo tu estilo escapando la variable por seguridad
        $cursoEscaped = $mysqli->real_escape_string($curso);
        
        $count_sql = "SELECT COUNT(*) as total FROM '$table' WHERE procesado = 0 AND curso = '$cursoEscaped'";
        $count_res = $mysqli->query($count_sql);
        $count_row = $count_res->fetch_assoc();
        
        $data["datos"][$table]["procesado"] = (int)$count_row['total'];

        // 2. Conteo condicional de 'pasado_delphos'
        if ($tieneDelphos) {
            $delphos_sql = "SELECT COUNT(*) as total FROM '$table' WHERE pasado_delphos = 0 AND curso = '$cursoEscaped'";
            $delphos_res = $mysqli->query($delphos_sql);
            $delphos_row = $delphos_res->fetch_assoc();
            $data["datos"][$table]["pasado_delphos"] = (int)$delphos_row['total'];
        } 
    }
}

$mysqli->close();
$data["error"] = "ok";
echo json_encode($data);
exit;