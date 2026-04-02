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
$mes_anno = $array_meses[$mes_num - 1] . "_" . $anio_actual;
$Name = 'informe_no_asistencia_comedor_' . $mes_anno;

// Consulta SQL
$sql = "
    SELECT r.curso, r.id_nie, r.apellidos, r.nombre,r.edificio,r.bonificado, rc.fecha_comedor
    FROM residentes r
    INNER JOIN residentes_comedor rc ON r.id_nie = rc.id_nie
    WHERE rc.fecha_comedor BETWEEN ? AND ?
      AND rc.desayuno = 0
      AND rc.comida = 0
      AND rc.cena = 0
      AND NOT EXISTS (
          SELECT 1
          FROM residentes_comedor rc2
          WHERE rc2.id_nie = rc.id_nie
            AND rc2.fecha_no_comedor = rc.fecha_comedor
      )
      AND r.curso = ?
    ORDER BY r.apellidos, r.nombre, rc.fecha_comedor
";

$stmt = $mysqli->prepare($sql);

if($stmt){
    $stmt->bind_param("sss", $fecha_inicio, $fecha_fin, $curso);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
}else{
    $error="Error en la consulta: " . $mysqli->error;
}

if (!$result ||$result->num_rows==0){
    if ($error=="")$error="No hay datos que listar.";
}

$primera_fila= ["","INFORME FALTAS DE ASISTENCIA AL COMEDOR NO COMUNICADAS - " . strtoupper($mes_anno),"","",""];
$encabezamiento= ["NIE","RESIDENTE","EDIFICIO","BONIFICADO","FECHA "];

if ($formato=="excel"){
    // 1. Recursos y Librería
    ini_set('memory_limit', '512M'); 
    set_time_limit(300);

    require_once __DIR__ . '/vendor/autoload.php';

    // 2. Crear Objeto
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Listado');
    if ($error!="") {
        if (ob_get_length()) ob_end_clean();
        $sheet->setCellValue('A1', $error);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $Name. '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
    $sheet->fromArray($primera_fila, NULL, 'A1');
    $sheet->fromArray($encabezamiento, NULL, 'A2');
    $sheet->freezePane('A3'); //Inmoviliza la priemra fila
    $estiloCabecera = [
        'font' => ['bold' => true],
        'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
        ],
    ];
    $sheet->getStyle('1:2')->applyFromArray($estiloCabecera);

    //Llenado de datos
    $row = 3;
    $result->data_seek(0); // Reiniciamos el puntero por si acaso
    while ($row = $result->fetch_assoc()) {
        if (substr(strtoupper($row["id_nie"]),0,1)== "P") continue;

        $filaFinal = generarFilaAlumno($row);
        
        // Insertar toda la fila de golpe (mucho más rápido)
        $sheet->fromArray($filaFinal, NULL, "A$row");
        $row++;
    }
    // Ajustar el ancho de las columnas automáticamente
    foreach (range('A', 'E') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    // 1. Borramos cualquier salida previa (espacios, errores ocultos)
    if (ob_get_length()) ob_end_clean();

    // 2. Cabeceras oficiales
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $Name. '.xlsx"');
    header('Cache-Control: max-age=0');
    header('Pragma: public');

    // 3. Generar archivo
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    
    // 4. Salida limpia
    exit();
}else{
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="'.$Name.'.csv"');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    if ($error!="") {
        fputcsv($output, [$error], ';');
        fclose($output);
        exit();
    }
    fputcsv($output, $primera_fila, ";");
    fputcsv($output, $encabezamiento, ";");

    while ($row = $result->fetch_assoc()) {
        if (substr(strtoupper($row["id_nie"]),0,1)== "P") continue;
        $filaFinal = generarFilaAlumno($row);
        fputcsv($output, $filaFinal, ";");
    }
    fclose($output);
    exit();
}


function generarFilaAlumno($r) {
    if ($r['bonificado'] == 1) {
            $bonificado = 'Sí';
        } else {
            $bonificado = 'No';
        }
    return [
            $r['id_nie'],
            '"'.$row['apellidos'].", ".$row['nombre'].'"',
            $r['edificio'],
            $bonificado,
            $r['fecha_comedor']
    ];
}
