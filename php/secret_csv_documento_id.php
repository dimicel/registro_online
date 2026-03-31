<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado'] !== "correcto") {
    exit("Acceso denegado");
}

include("conexion.php");


$curso = isset($_POST["curso_csv_doc_id"]) ? $_POST["curso_csv_doc_id"] : null;
$formato = isset($_POST["formato"]) ? $_POST["formato"] : 'csv';

if (!$curso || $mysqli->connect_error) {
    exit("Error: Parámetros insuficientes o fallo de conexión.");
}

$curso_safe = $mysqli->real_escape_string($curso);
$query = "
SELECT 
    u.apellidos, 
    u.nombre, 
    u.id_nie, 
    u.fecha_caducidad_id_nif,
    u.pais,
    u.id_nif,
    u.es_pasaporte,
    COALESCE(me.grupo, mb.grupo) AS grupo,
    COALESCE(mf.curso_ciclo, mc.curso_ciclo) AS curso_ciclo,
    COALESCE(mf.ciclo, mc.ciclo) AS ciclo,
    mc.turno AS turno,
    DATE_FORMAT(MAX(CASE WHEN doc.documento = 'dni_anverso' THEN doc.fecha END), '%d/%m/%Y - %H:%i:%s') AS ultima_fecha_anverso_dni,
    DATE_FORMAT(MAX(CASE WHEN doc.documento = 'dni_reverso' THEN doc.fecha END), '%d/%m/%Y - %H:%i:%s') AS ultima_fecha_reverso_dni,
    DATE_FORMAT(MAX(CASE WHEN doc.documento = 'pasaporte' THEN doc.fecha END), '%d/%m/%Y - %H:%i:%s') AS ultima_fecha_pasaporte,
    DATE_FORMAT(MAX(CASE WHEN doc.documento = 'seguro_escolar' THEN doc.fecha END), '%d/%m/%Y - %H:%i:%s') AS ultima_fecha_seguro_escolar
FROM usuarios u
INNER JOIN (
    SELECT id_nie FROM mat_ciclos WHERE curso = '$curso_safe'
    UNION
    SELECT id_nie FROM mat_fpb WHERE curso = '$curso_safe'
    UNION
    SELECT id_nie FROM mat_eso WHERE curso = '$curso_safe'
    UNION
    SELECT id_nie FROM mat_bach WHERE curso = '$curso_safe'
) AS m ON m.id_nie = u.id_nie
LEFT JOIN mat_ciclos mc ON mc.id_nie = u.id_nie AND mc.curso = '$curso_safe'
LEFT JOIN mat_fpb mf    ON mf.id_nie = u.id_nie AND mf.curso = '$curso_safe'
LEFT JOIN mat_eso me    ON me.id_nie = u.id_nie AND me.curso = '$curso_safe'
LEFT JOIN mat_bach mb   ON mb.id_nie = u.id_nie AND mb.curso = '$curso_safe'
LEFT JOIN fechas_subidas_docs doc ON doc.id_nie = u.id_nie
GROUP BY 
    u.id_nie, u.apellidos, u.nombre, u.fecha_caducidad_id_nif, 
    u.pais, u.id_nif, u.es_pasaporte, grupo, curso_ciclo, ciclo, turno
ORDER BY u.apellidos ASC, u.nombre ASC";

$res = $mysqli->query($query);

if (!$res || $res->num_rows == 0) {
    exit("No hay registros que listar.");
}

$fechaHoy = new DateTime(); 
$fechaHoy->setTime(0, 0, 0);

// --- LÓGICA DE EXPORTACIÓN ---

if ($formato === "excel") {
// 1. Cargamos la librería SOLO si entramos aquí
    ini_set('memory_limit', '512M'); // Aumenta el límite de memoria
    set_time_limit(300);   // Da 5 minutos de margen por si la consulta es lenta
    require_once __DIR__ . '/vendor/autoload.php';

    // 2. Creamos el objeto usando la ruta completa de la clase
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Listado');

    // 3. Encabezados (Tal cual los pediste)
    $headers = [
        "NIE", "ALUMNO", "Nº de DOCUMENTO", "ES PASAPORTE", "FECHA CADUCIDAD", 
        "CADUCADO", "DIAS HASTA CADUCIDAD DEL DOCUMENTO DE IDENTIDAD", "PAIS", 
        "CURSO", "TURNO", "Fecha última subida Anverso DNI/NIE", 
        "Fecha última subida Reverso DNI/NIE", "Fecha última subida Pasaporte", 
        "Fecha última subida Seguro Escolar"
    ];
    $sheet->fromArray($headers, NULL, 'A1');

    // 4. Estilos de cabecera: Fija, Negrita y Centrada
    $sheet->freezePane('A2');
    $estiloCabecera = [
        'font' => ['bold' => true],
        'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        ],
    ];
    $sheet->getStyle('A1:N1')->applyFromArray($estiloCabecera);

    // 5. Alineación de columnas (Centramos las que pediste)
    $columnasCentradas = ['D', 'E', 'F', 'G', 'K', 'L', 'M', 'N'];
    foreach ($columnasCentradas as $col) {
        $sheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    $row = 2;
    while ($r = $res->fetch_assoc()) {
        // Saltamos usuarios de prueba
        if (strpos(strtoupper($r["id_nie"]), 'P') === 0) continue;

        $alumno = ucwords(strtolower($r["apellidos"])) . ", " . ucwords(strtolower($r["nombre"]));
        
        // Lógica de fechas (Igual que en tu CSV)
        $fechaRaw = $r["fecha_caducidad_id_nif"];
        $esInvalida = (empty($fechaRaw) || $fechaRaw == "0000-00-00" || $fechaRaw == "1970-01-01");
        if ($esInvalida) {
            $fechaCad_ES = ''; $estaCaducado = 'X'; $diasFaltan = 0;
        } else {
            $fechaCaducidad = new DateTime($fechaRaw);
            $fechaCad_ES = $fechaCaducidad->format('d/m/Y');
            $estaCaducado = ($fechaCaducidad <= $fechaHoy) ? "Si" : "No";
            $diferencia = $fechaHoy->diff($fechaCaducidad);
            $diasRaw = (int)$diferencia->format("%r%a");
            $diasFaltan = ($diasRaw > 0) ? $diasRaw : 0;
        }

        $cursoTexto = $r['ciclo'] ? ($r['curso_ciclo'] . " - " . $r['ciclo']) : $r['grupo'];
        $turnoTexto = $r['turno'] ?? 'N/A';

        // Insertar datos en las celdas
        $sheet->setCellValue('A' . $row, $r["id_nie"]);
        $sheet->setCellValue('B' . $row, $alumno);
        $sheet->setCellValue('C' . $row, $r["id_nif"]);
        
        // Columna D: ES PASAPORTE (Rojo si es 1/Si)
        $valorPasaporte = ($r["es_pasaporte"] ? "Si" : "No");
        $sheet->setCellValue('D' . $row, $valorPasaporte);
        if ($r["es_pasaporte"]) {
            $sheet->getStyle('D' . $row)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        }

        $sheet->setCellValue('E' . $row, $fechaCad_ES);

        // Columna F: CADUCADO (Rojo si es Si)
        $sheet->setCellValue('F' . $row, $estaCaducado);
        if ($estaCaducado === "Si") {
            $sheet->getStyle('F' . $row)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        }

        $sheet->setCellValue('G' . $row, $diasFaltan);
        $sheet->setCellValue('H' . $row, $r["pais"]);
        $sheet->setCellValue('I' . $row, $cursoTexto);
        $sheet->setCellValue('J' . $row, $turnoTexto);
        $sheet->setCellValue('K' . $row, $r["ultima_fecha_anverso_dni"]);
        $sheet->setCellValue('L' . $row, $r["ultima_fecha_reverso_dni"]);
        $sheet->setCellValue('M' . $row, $r["ultima_fecha_pasaporte"]);
        $sheet->setCellValue('N' . $row, $r["ultima_fecha_seguro_escolar"]);

        $row++;
    }

    // 6. Autoajustar el ancho de todas las columnas (A hasta N)
    foreach (range('A', 'N') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    // 7. Cabeceras para descarga forzada del navegador
    if (ob_get_contents()) ob_end_clean();
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="listado_' . $curso . '.xlsx"');
    header('Cache-Control: max-age=0');
    echo extension_loaded('zip') ? "ZIP instalado" : "Falta la extensión ZIP";
    exit();
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit();

} else {
    // --- LÓGICA CSV (Tu código original) ---
    $Name = 'listado_num_doc_' . $curso . '.csv';
    header('Content-Type: text/csv; charset=latin1');
    header('Content-Disposition: attachment; filename="' . $Name . '"');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($output, ["NIE", "ALUMNO", "N_DOCUMENTO", "ES_PASAPORTE", "FECHA_CADUCIDAD", "CADUCADO", "DIAS_HASTA_CADUCIDAD", "PAIS", "CURSO", "TURNO", "SUBIDA_ANVERSO_DNI", "SUBIDA_REVERSO_DNI", "SUBIDA_PASAPORTE", "SUBIDA_SEGURO_ESCOLAR"], ";");
    
    while ($r = $res->fetch_assoc()) {
        if (strpos(strtoupper($r["id_nie"]), 'P') === 0) continue;
        
        $alumno = ucwords(strtolower($r["apellidos"])) . ", " . ucwords(strtolower($r["nombre"]));
        $fechaRaw = $r["fecha_caducidad_id_nif"];
        $esInvalida = (empty($fechaRaw) || $fechaRaw == "0000-00-00" || $fechaRaw == "1970-01-01");
        
        if ($esInvalida) {
            $fechaCad_ES = ''; $estaCaducado = 'X'; $diasFaltan = 0;
        } else {
            $fechaCaducidad = new DateTime($fechaRaw);
            $fechaCad_ES = $fechaCaducidad->format('d/m/Y');
            $estaCaducado = ($fechaCaducidad <= $fechaHoy) ? "Si" : "No";
            $diferencia = $fechaHoy->diff($fechaCaducidad);
            $diasRaw = (int)$diferencia->format("%r%a");
            $diasFaltan = ($diasRaw > 0) ? $diasRaw : 0;
        }
        
        $cursoTexto = $r['ciclo'] ? ($r['curso_ciclo'] . " - " . $r['ciclo']) : $r['grupo'];
        $turnoTexto = $r['turno'] ?? 'N/A';

        fputcsv($output, [
            "\t" . $r["id_nie"],
            $alumno,
            "\t" . $r["id_nif"],
            ($r["es_pasaporte"] ? "Si" : "No"),
            $fechaCad_ES,
            $estaCaducado,
            $diasFaltan,
            $r["pais"],
            $cursoTexto,
            $turnoTexto,
            $r["ultima_fecha_anverso_dni"]?:'',
            $r["ultima_fecha_reverso_dni"]?:'',
            $r["ultima_fecha_pasaporte"]?:'',
            $r["ultima_fecha_seguro_escolar"]?:''
        ], ';');
    }
    fclose($output);
    exit();
}