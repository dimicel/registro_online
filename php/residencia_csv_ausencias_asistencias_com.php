<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado'] !== "correcto") exit("Acceso denegado");

$error = "";

include("conexion.php");
if ($mysqli->errno > 0) {
    http_response_code(500);
    echo "Error en servidor.";
    exit;
}

$curso = $_POST["comedor_curso"] ?? "";
$mes = $_POST["mes_informe"] ?? "";
$formato = $_POST["seleccion_csv_excel"] ?? "";

if ($curso === "" || $mes === "" || $formato === "") {
    http_response_code(500);
    echo "Faltan datos del curso o mes.";
    exit;
}

$anno_1 = substr($curso, 0, 4);
$anno_2 = substr($curso, -4);
$array_meses = ["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"];

$mes_num = (int)$mes;

if ($mes_num < 1 || $mes_num > 12) {
    http_response_code(500);
    exit("Mes no válido.");
}

// 1. Elegimos el año según el mes (Julio-Diciembre -> Año 1, Enero-Junio -> Año 2)
$anio_actual = ($mes_num >= 7 && $mes_num <= 12) ? $anno_1 : $anno_2;

// 2. Calculamos las fechas automáticamente
$fecha_inicio = $anio_actual . "-" . str_pad($mes_num, 2, "0", STR_PAD_LEFT) . "-01";
$ultimo_dia = date("t", strtotime($fecha_inicio)); // "t" saca el último día real del mes
$fecha_fin = $anio_actual . "-" . str_pad($mes_num, 2, "0", STR_PAD_LEFT) . "-" . $ultimo_dia;

// 3. Formateamos el texto para el informe
$mes_anno = $array_meses[$mes_num - 1] . "/" . $anio_actual;

$Name = 'informe_asistencias_ausencias_comedor_' . $mes_anno;



$sql_asistencias = "
    SELECT r.curso, r.id_nie, r.apellidos, r.nombre,r.bonificado, r.edificio, rc.fecha_comedor, rc.desayuno, rc.comida, rc.cena
    FROM residentes r
    JOIN residentes_comedor rc ON r.id_nie = rc.id_nie
    WHERE (rc.desayuno = 1 OR rc.comida = 1 OR rc.cena = 1) AND (rc.fecha_comedor BETWEEN ? AND ?) AND r.curso = ?
    ORDER BY r.apellidos, r.nombre, rc.fecha_comedor
";

$stmt = $mysqli->prepare($sql_asistencias);

if($stmt){
    $stmt->bind_param("sss", $fecha_inicio, $fecha_fin, $curso);
    $stmt->execute();
    $asistencia = $stmt->get_result();
    $stmt->close();
}else{
    $error="Error en la consulta: " . $mysqli->error;
}

if (!$asistencia ||$asistencia->num_rows==0){
    if ($error=="")$error="No hay datos de ASISTENCIA que listar. ";
}

$sql_ausencias = "
    SELECT DISTINCT 
        r.id_nie, r.apellidos, r.nombre,r.bonificado, r.edificio, rc.fecha_comedor
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
    ORDER BY r.apellidos, r.nombre, rc.fecha_comedor
";

$stmt = $mysqli->prepare($sql_ausencias);

if($stmt){
    $stmt->bind_param("sss", $fecha_inicio, $fecha_fin, $curso);
    $stmt->execute();
    $ausencia = $stmt->get_result();
    $stmt->close();
}else{
    $error="Error en la consulta: " . $mysqli->error;
}

if (!$ausencia ||$ausencia->num_rows==0){
    $error+="No hay datos de AUSENCIAS INJUSTIFICADAS que listar. ";
}

if ($formato=="excel"){
   
} else {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="'.$Name.'.csv"');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    if ($error!="") {
        fputcsv($output, [$error], ';');
        fclose($output);
        exit();
    }
    fputcsv($output, ["INFORME DE ASISTENCIAS Y AUSENCIAS AL COMEDOR POR ALUMNO Y FECHA - " . strtoupper($mes_anno),"","","","","","",""], ";");
    fputcsv($output, ["","","","","","","",""], ";");
    fputcsv($output, ["","","","","","","",""], ";");
    fputcsv($output, ["ASISTENCIAS","","","","","","",""], ";");
    fputcsv($output, ["","","","","","","",""], ";");
    $encabezamiento= ["NIE","RESIDENTE","EDIFICIO","BONIFICADO","FECHA","DESAYUNO","COMIDA","CENA"];
    fputcsv($output, $encabezamiento, ";");
}




$Datos .= $eol;

// --- AUSENCIAS ---
$Datos .= "AUSENCIAS INJUSTIFICADAS" . $eol;
$Datos .= "NIE;RESIDENTE;EDIFICIO;BONIFICADO;FECHA" . $eol;




function generarFilaAlumno($r,$asis_aus) {
    if ($r['bonificado'] == 1) {
            $bonificado = 'Sí';
        } else {
            $bonificado = 'No';
        }
    if ($asis_aus=="ausencia"){
        return [
                $row['id_nie'],
                '"'.$row['apellidos'].", ".$row['nombre'].'"',
                $row['edificio'],
                $bonificado,
                date("d/m/Y", strtotime($row['fecha_comedor']))
            ];
    }else{
        return[
            $row['id_nie'],
            '"'.$row['apellidos'].", ".$row['nombre'].'"',
            $row['edificio'],
            $bonificado,
            date("d/m/Y", strtotime($row['fecha_comedor'])),
            $row['desayuno'],
            $row['comida'],
            $row['cena']
        ];
    }
}