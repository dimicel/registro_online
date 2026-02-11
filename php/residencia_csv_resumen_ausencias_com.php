<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado'] !== "correcto") exit("Acceso denegado");

$error = "";
$Datos = "\xEF\xBB\xBF"; // Añadir BOM para UTF-8 para que Excel lo reconozca

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

$Name = 'informe_resumen_ausencias_' . $mes_anno . '.csv';

$Datos .= "INFORME RESUMEN DE FALTAS DE ASISTENCIA AL COMEDOR NO COMUNICADAS POR RESIDENTE - " . strtoupper($mes_anno) . PHP_EOL;
$Datos .= 'NIE;RESIDENTE;BONIFICADO;NUM_FALTAS' . PHP_EOL;

// Consulta SQL
$sql = "
    SELECT 
        r.curso, r.id_nie,r.bonificado, 
        CONCAT(r.apellidos, ', ', r.nombre) AS nombre_completo,
        IFNULL(f.faltas_injustificadas, 0) AS faltas_injustificadas
    FROM residentes r
    LEFT JOIN (
        SELECT 
            rc.id_nie,
            COUNT(*) AS faltas_injustificadas
        FROM residentes_comedor rc
        LEFT JOIN residentes_comedor rnc 
            ON rc.id_nie = rnc.id_nie 
            AND rc.fecha_comedor = rnc.fecha_no_comedor
        WHERE 
            rc.fecha_comedor BETWEEN ? AND ?
            AND rc.desayuno = 0 
            AND rc.comida = 0 
            AND rc.cena = 0
            AND rnc.id_nie IS NULL
            AND r.curso = ?
        GROUP BY rc.id_nie
    ) f ON r.id_nie = f.id_nie
    WHERE 
        (f.faltas_injustificadas > 0 OR r.baja = 0)
    ORDER BY r.apellidos, r.nombre
";

$stmt = $mysqli->prepare($sql);
if ($stmt === false) {
    http_response_code(500);
    echo "Error en la preparación de la consulta.";
    exit;
}

$stmt->bind_param("sss", $fecha_inicio, $fecha_fin, $curso);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    if ($row["bonificado"] == 1) {
        $bonificado="Sí";
    }
    else {
        $bonificado="No";
    }
    $linea = [
        $row['id_nie'],
        $row['nombre_completo'],
        $bonificado,
        $row['faltas_injustificadas']
    ];
    $Datos .= implode(';', $linea) . PHP_EOL;
}

$stmt->close();
$mysqli->close();

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
