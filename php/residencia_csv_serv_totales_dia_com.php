<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado'] !== "correcto") exit("Acceso denegado");

$Datos = "\xEF\xBB\xBF"; // BOM UTF-8 para Excel

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

$Name = 'informe_resumen_servicios_dia_' . $mes_anno;


// Consulta SQL extendida para bonificados y no bonificados
$sql = "
    SELECT 
        r.curso, 
        rc.fecha_comedor AS fecha,
        CASE DAYOFWEEK(rc.fecha_comedor)
            WHEN 2 THEN 'Lunes'
            WHEN 3 THEN 'Martes'
            WHEN 4 THEN 'Miércoles'
            WHEN 5 THEN 'Jueves'
            WHEN 6 THEN 'Viernes'
        END AS dia_semana,
        r.bonificado,
        SUM(CASE WHEN rc.desayuno = 1 THEN 1 ELSE 0 END) AS desayuno,
        SUM(CASE WHEN rc.comida = 1 THEN 1 ELSE 0 END) AS comida,
        SUM(CASE WHEN rc.cena = 1 THEN 1 ELSE 0 END) AS cena,
        COUNT(CASE WHEN rc.desayuno = 1 OR rc.comida = 1 OR rc.cena = 1 THEN 1 END) AS total
    FROM residentes_comedor rc
    INNER JOIN residentes r ON rc.id_nie = r.id_nie
    WHERE 
        r.curso = ?
        AND rc.fecha_comedor BETWEEN ? AND ?
        AND DAYOFWEEK(rc.fecha_comedor) BETWEEN 2 AND 6
    GROUP BY rc.fecha_comedor, r.bonificado
    HAVING desayuno > 0 OR comida > 0 OR cena > 0
    ORDER BY rc.fecha_comedor, r.bonificado
";

$stmt = $mysqli->prepare($sql);

if($stmt){
    $stmt->bind_param("sss", "sss", $curso, $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
}else{
    $error="Error en la consulta: " . $mysqli->error;
}

// Agrupar resultados por fecha
$datos_por_fecha = [];

while ($row = $result->fetch_assoc()) {
    $fecha = $row['fecha'];
    $dia_semana = $row['dia_semana'];
    $bonificado = (int)$row['bonificado'];

    if (!isset($datos_por_fecha[$fecha])) {
        $datos_por_fecha[$fecha] = [
            'dia' => $dia_semana,
            'desayuno_b' => 0, 'desayuno_nb' => 0,
            'comida_b' => 0, 'comida_nb' => 0,
            'cena_b' => 0, 'cena_nb' => 0,
            'total_b' => 0, 'total_nb' => 0
        ];
    }

    if ($bonificado === 1) {
        $datos_por_fecha[$fecha]['desayuno_b'] = (int)$row['desayuno'];
        $datos_por_fecha[$fecha]['comida_b'] = (int)$row['comida'];
        $datos_por_fecha[$fecha]['cena_b'] = (int)$row['cena'];
        $datos_por_fecha[$fecha]['total_b'] = (int)$row['total'];
    } else {
        $datos_por_fecha[$fecha]['desayuno_nb'] = (int)$row['desayuno'];
        $datos_por_fecha[$fecha]['comida_nb'] = (int)$row['comida'];
        $datos_por_fecha[$fecha]['cena_nb'] = (int)$row['cena'];
        $datos_por_fecha[$fecha]['total_nb'] = (int)$row['total'];
    }
}


$encabezamiento= ["FECHA","DÍA_SEMANA","DESAYUNO_BONIFICADOS","DESAYUNO_NO_BONIFICADOS","COMIDA_BONIFICADOS","COMIDA_NO_BONIFICADOS","CENA_BONIFICADOS","CENA_NO_BONIFICADOS","ASISTENTES_BONIFICADOS","ASISTENTES_NO_BONIFICADOS"];


if ($formato=="excel"){
    // 1. Recursos y Librería
    ini_set('memory_limit', '512M'); 
    set_time_limit(300);

    $primera_fila= ["INFORME RESUMEN DE SERVICIOS PARCIALES Y TOTALES POR DÍA - " . strtoupper($mes_anno)];
    $segunda_fila= ["La columna ASISTENTES cuenta el número de residentes que han hecho desayuno, comida o cena en ese día."];
    $tercera_fila= ["El valor de esta columna NO tiene por qué coincidir con la suma de desayunos+comidas+cenas de ese día."];
    $cuarta_fila= ["Desglose por residentes bonificados y no bonificados."];

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
    $sheet->fromArray($segunda_fila, NULL, 'A2');
    $sheet->fromArray($tercera_fila, NULL, 'A3');
    $sheet->fromArray($cuarta_fila, NULL, 'A4');
    $sheet->fromArray($encabezamiento, NULL, 'A5');
    $sheet->freezePane('A6'); 
    $estiloCabecera = [
        'font' => ['bold' => true],
        'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
        ],
    ];
    $sheet->getStyle('1:2')->applyFromArray($estiloCabecera);

    //Llenado de datos
    $row = 6;
    foreach ($datos_por_fecha as $fecha => $data) {
        $filaFinal = [
                $fecha,
                $data['dia'],
                $data['desayuno_b'],
                $data['desayuno_nb'],
                $data['comida_b'],
                $data['comida_nb'],
                $data['cena_b'],
                $data['cena_nb'],
                $data['total_b'],
                $data['total_nb']
            ];
        
        // Insertar toda la fila de golpe (mucho más rápido)
        $sheet->fromArray($filaFinal, NULL, "A$row");
        $row++;
    }
    // Combinar de la A a la K para cada fila individualmente
    $sheet->mergeCells('A1:K1');
    $sheet->mergeCells('A2:K2');
    $sheet->mergeCells('A3:K3');
    $sheet->mergeCells('A4:K4');

    // Aplicar la alineación a la izquierda a todo el bloque
    $sheet->getStyle('A1:K4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    // Ajustar el ancho de las columnas automáticamente
    foreach (range('A', 'j') as $col) {
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
} else {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="'.$Name.'.csv"');

    $primera_fila= ["INFORME RESUMEN DE SERVICIOS PARCIALES Y TOTALES POR DÍA - " . strtoupper($mes_anno),"","","","","","","","",""];
    $segunda_fila= ["La columna ASISTENTES cuenta el número de residentes que han hecho desayuno, comida o cena en ese día.","","","","","","","","",""];
    $tercera_fila= ["El valor de esta columna NO tiene por qué coincidir con la suma de desayunos+comidas+cenas de ese día.","","","","","","","","",""];
    $cuarta_fila= ["Desglose por residentes bonificados y no bonificados.","","","","","","","","",""];

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    if ($error!="") {
        fputcsv($output, [$error], ';');
        fclose($output);
        exit();
    }
    fputcsv($output, $primera_fila, ";");
    fputcsv($output, $segunda_fila, ";");
    fputcsv($output, $tercera_fila, ";");
    fputcsv($output, $cuarta_fila, ";");
    fputcsv($output, $encabezamiento, ";");

    foreach ($datos_por_fecha as $fecha => $data) {
        $filaFinal = [
                $fecha,
                $data['dia'],
                $data['desayuno_b'],
                $data['desayuno_nb'],
                $data['comida_b'],
                $data['comida_nb'],
                $data['cena_b'],
                $data['cena_nb'],
                $data['total_b'],
                $data['total_nb']
            ];
        fputcsv($output, $filaFinal, ";");
    }
    fclose($output);
    exit();    
}



