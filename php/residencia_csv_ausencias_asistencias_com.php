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
$mes_anno = $array_meses[$mes_num - 1] . "-" . $anio_actual;

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
        AND r.curso = ?
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


if ($formato=="excel"){
    // 1. Recursos y Librería
    ini_set('memory_limit', '512M'); 
    set_time_limit(300);

    require_once __DIR__ . '/vendor/autoload.php';

    // 2. Crear Objeto
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('ASISTENCIAS_' . strtoupper($mes_anno));
    if (!$asistencia ||$asistencia->num_rows==0){
        if (ob_get_length()) ob_end_clean();
        $sheet->setCellValue('A1', "No hay datos de ASISTENCIA que mostrar.");
    }
    else {
        $encabezamiento= ["NIE","RESIDENTE","EDIFICIO","BONIFICADO","FECHA","DESAYUNO","COMIDA","CENA"];
        $sheet->fromArray($encabezamiento, NULL, 'A1');
        $sheet->freezePane('A2'); //Inmoviliza la priemra fila
        $estiloCabecera = [
            'font' => ['bold' => true],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
        ];
        $sheet->getStyle('1:1')->applyFromArray($estiloCabecera);

        $row = 2;
        $asistencia->data_seek(0); // Reiniciamos el puntero por si acaso
        while ($registro = $asistencia->fetch_assoc()) {
            if (substr(strtoupper($registro["id_nie"]),0,1)== "P") continue;
            $filaFinal = generarFilaAlumno($registro,"asistencia","excel");
            $sheet->fromArray($filaFinal, NULL, "A$row");
            $sheet->getStyle("C$row:H$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            if ($filaFinal[3] != "No"){
                $sheet->getStyle("D$row")->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
            }
            $row++;
        }
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
    // Crear nueva hoja
    $nuevaHoja = $spreadsheet->createSheet();
    $nuevaHoja->setTitle('AUSENCIAS INJUST._' . strtoupper($mes_anno));

    //Activar la hoja para empezar a meter datos
    $spreadsheet->setActiveSheetIndex($spreadsheet->getSheetCount() - 1);
    $sheet = $spreadsheet->getActiveSheet();
    if (!$ausencia ||$ausencia->num_rows==0){
        if (ob_get_length()) ob_end_clean();
        $sheet->setCellValue('A1', "No hay datos de AUSENCIAS INJUSTIFICADAS que mostrar.");
    }
    else {
        $encabezamiento= ["NIE","RESIDENTE","EDIFICIO","BONIFICADO","FECHA"];
        $sheet->fromArray($encabezamiento, NULL, 'A1');
        $sheet->freezePane('A2'); //Inmoviliza la priemra fila
        $estiloCabecera = [
            'font' => ['bold' => true],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
        ];
        $sheet->getStyle('1:1')->applyFromArray($estiloCabecera);

        $row = 2;
        $ausencia->data_seek(0); // Reiniciamos el puntero por si acaso
        while ($registro = $ausencia->fetch_assoc()) {
            if (substr(strtoupper($registro["id_nie"]),0,1)== "P") continue;
            $filaFinal = generarFilaAlumno($registro,"ausencia","excel");
            $sheet->fromArray($filaFinal, NULL, "A$row");
            $sheet->getStyle("C$row:E$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            if ($filaFinal[3] != "No"){
                $sheet->getStyle("D$row")->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
            }
            $row++;
        }
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
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
    if (!$asistencia ||$asistencia->num_rows==0){
        fputcsv($output, ["No hay datos de ASISTENCIA que mostrar."], ';');
    }
    else{
        fputcsv($output, ["","","","","","","",""], ";");
        $encabezamiento= ["NIE","RESIDENTE","EDIFICIO","BONIFICADO","FECHA","DESAYUNO","COMIDA","CENA"];
        fputcsv($output, $encabezamiento, ";");
        while ($registro = $asistencia->fetch_assoc()) {
            if (substr(strtoupper($registro["id_nie"]),0,1)== "P") continue;
            $filaFinal = generarFilaAlumno($registro,"asistencia","csv");
            fputcsv($output, $filaFinal, ";");
        }
    }
    fputcsv($output, ["","","","","","","",""], ";");
    fputcsv($output, ["","","","","","","",""], ";");
    fputcsv($output, ["AUSENCIAS INJUSTIFICADAS","","","","","","",""], ";");
    if (!$ausencia ||$ausencia->num_rows==0){
        fputcsv($output, ["No hay datos de AUSENCIAS INJUSTIFICADAS que listar."], ';');
        fclose($output);
        exit();
    }
    fputcsv($output, ["","","","","","","",""], ";");
    fputcsv($output, ["NIE","RESIDENTE","EDIFICIO","BONIFICADO","FECHA","","",""], ";");
    while ($registro = $ausencia->fetch_assoc()) {
        if (substr(strtoupper($registro["id_nie"]),0,1)== "P") continue;
        $filaFinal = generarFilaAlumno($registro,"ausencia","csv");
        fputcsv($output, $filaFinal, ";");
    }
    fclose($output);
    exit();
}


function generarFilaAlumno($r, $asis_aus, $for) {
    $bonificado = ($r['bonificado'] == 1) ? 'Sí' : 'No';
    $nombre = $r['apellidos'] . ", " . $r['nombre'];
    
    // Solo ponemos comillas si es CSV
    if ($for == "csv") {
        $nombre = '"' . $nombre . '"';
    }

    $fila = [
        $r['id_nie'],
        $nombre,
        $r['edificio'],
        $bonificado,
        date("d/m/Y", strtotime($r['fecha_comedor']))
    ];

    if ($asis_aus == "asistencia") {
        $fila[] = $r['desayuno'];
        $fila[] = $r['comida'];
        $fila[] = $r['cena'];
    } elseif ($for == "csv") {
        // Para que el CSV mantenga la estructura de 8 columnas aunque sea ausencia
        return array_merge($fila, ["", "", ""]);
    }

    return $fila;
}