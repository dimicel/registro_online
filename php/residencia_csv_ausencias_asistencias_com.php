<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado'] !== "correcto") exit("Acceso denegado");

$error = "";
$Datos = "\xEF\xBB\xBF"; // Añadir BOM para UTF-8 para que Excel lo reconozca
$eol = PHP_EOL;

include("conexion.php");
if ($mysqli->errno > 0) {
    http_response_code(500);
    echo "Error en servidor.";
    exit;
}

$curso = $_POST["comedor_curso"] ?? "";
$mes = $_POST["mes_informe"] ?? "";

if ($curso === "" || $mes === "") {
    http_response_code(500);
    echo "Faltan datos del curso o mes.";
    exit;
}

$anno_1 = substr($curso, 0, 4);
$anno_2 = substr($curso, -4);
$array_meses = ["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"];
$array_dias_mes = [31,29,31,30,31,30,31,31,30,31,30,31];

$mes_anno = "";
$fecha_inicio = "";
$fecha_fin = "";
$mes_num = (int)$mes;

if ($mes_num >= 7 && $mes_num <= 12) {
    $mes_anno = $array_meses[$mes_num - 1] . "/" . $anno_1;
    $fecha_inicio = $anno_1 . "-" . str_pad($mes, 2, "0", STR_PAD_LEFT) . "-01";
    $fecha_fin = $anno_1 . "-" . str_pad($mes, 2, "0", STR_PAD_LEFT) . "-" . $array_dias_mes[$mes_num - 1];
} elseif ($mes_num >= 1 && $mes_num <= 6) {
    $mes_anno = $array_meses[$mes_num - 1] . "/" . $anno_2;
    $fecha_inicio = $anno_2 . "-" . str_pad($mes, 2, "0", STR_PAD_LEFT) . "-01";
    $fecha_fin = $anno_2 . "-" . str_pad($mes, 2, "0", STR_PAD_LEFT) . "-" . $array_dias_mes[$mes_num - 1];
} else {
    http_response_code(500);
    echo "Mes no válido.";
    exit;
}

$Name = 'informe_asistencias_ausencias_comedor_' . $mes_anno . '.csv';

// Cabecera del archivo
$Datos .= "INFORME DE ASISTENCIAS Y AUSENCIAS AL COMEDOR POR ALUMNO Y FECHA - " . strtoupper($mes_anno) . $eol . $eol;

// --- ASISTENCIAS ---
$Datos .= "ASISTENCIAS" . $eol;
$Datos .= "NIE;RESIDENTE;BONIFICADO;FECHA;DESAYUNO;COMIDA;CENA" . $eol;

$sql_asistencias = "
    SELECT r.curso, r.id_nie, r.apellidos, r.nombre,r.bonificado, rc.fecha_comedor, rc.desayuno, rc.comida, rc.cena
    FROM residentes r
    JOIN residentes_comedor rc ON r.id_nie = rc.id_nie
    WHERE (rc.desayuno = 1 OR rc.comida = 1 OR rc.cena = 1) AND (rc.fecha_comedor BETWEEN ? AND ?) AND r.curso = ?
    ORDER BY r.apellidos, r.nombre, rc.fecha_comedor
";

$stmt_asis = $mysqli->prepare($sql_asistencias);
if ($stmt_asis === false) {
    http_response_code(500);
    echo "Error en la preparación de la consulta ASISTENCIAS  1. " . $mysqli->error;
    exit;
}

$stmt_asis->bind_param("sss", $fecha_inicio, $fecha_fin, $curso);
$stmt_asis->execute();
$result = $stmt_asis->get_result();

while ($row = $result->fetch_assoc()) {
    if ($row['bonificado'] == 1) {
        $bonificado = 'Sí';
    } else {
        $bonificado = 'No';
    }
    $line = [
        $row['id_nie'],
        '"'.$row['apellidos'].", ".$row['nombre'].'"',
        $bonificado,
        date("d/m/Y", strtotime($row['fecha_comedor'])),
        $row['desayuno'],
        $row['comida'],
        $row['cena']
    ];
    $Datos .= implode(";", $line) . $eol;
}

// Separación
$Datos .= $eol;

// --- AUSENCIAS ---
$Datos .= "AUSENCIAS INJUSTIFICADAS" . $eol;
$Datos .= "NIE;RESIDENTE;BONIFICADO;FECHA" . $eol;

$sql_ausencias = "
    SELECT DISTINCT 
        r.id_nie, r.apellidos, r.nombre,r.bonificado, rc.fecha_comedor
    FROM residentes r
    JOIN residentes_comedor rc ON r.id_nie = rc.id_nie
    LEFT JOIN residentes_comedor just
        ON rc.id_nie = just.id_nie 
        AND rc.fecha_comedor = just.fecha_no_comedor
    WHERE 
        rc.fecha_comedor BETWEEN ? AND ?
        AND rc.desayuno = 0 AND rc.comida = 0 AND rc.cena = 0
        AND just.id_nie IS NULL
        AND rc.fecha_comedor IS NOT NULL
        AND rc.fecha_comedor != ''
    ORDER BY r.apellidos, r.nombre, rc.fecha_comedor
";

$stmt_aus = $mysqli->prepare($sql_ausencias);
if ($stmt_aus === false) {
    http_response_code(500);
    echo "Error en la preparación de la consulta AUSENCIAS. 2" . $mysqli->error;
    exit;
}

$stmt_aus->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmt_aus->execute();
$result = $stmt_aus->get_result();

while ($row = $result->fetch_assoc()) {
    if ($row['bonificado'] == 1) {
        $bonificado = 'Sí';
    } else {
        $bonificado = 'No';
    }
    $line = [
        $row['id_nie'],
        '"'.$row['apellidos'].", ".$row['nombre'].'"',
        $bonificado,
        date("d/m/Y", strtotime($row['fecha_comedor']))
    ];
    $Datos .= implode(";", $line) . $eol;
}

header('Expires: 0');
header('Cache-control: private');
header('Content-Type: application/octet-stream;charset=utf-8');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Content-Description: File Transfer');
header('Last-Modified: ' . date('D, d M Y H:i:s'));
header('Content-Disposition: attachment; filename="' . $Name . '"');
header("Content-Transfer-Encoding: binary");

echo $Datos;
exit;
