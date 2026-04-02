<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");

$error = "";
$result = null;

include("conexion.php");
// Es preferible usar connect_errno para verificar la conexión inicial
if ($mysqli->connect_errno) {
    http_response_code(500);
    exit("Error en servidor.");
}

$curso = $_POST["curso_csv_remesas"] ?? "";
$formato = $_POST["formato"] ?? "csv";

$sql = "SELECT * FROM residentes WHERE curso=? ORDER BY apellidos, nombre";
$stmt = $mysqli->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $curso);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $error = "Error en la consulta: " . $mysqli->error;
}

if (!$result || $result->num_rows == 0) {
    $error = "No hay datos que listar.";
}

$Name = 'remesas_' . date('d-m-Y');
$encabezamiento = ["NIE", "APELLIDOS", "NOMBRE", "EDIFICIO", "CURSO", "DIRECCION", "CP", "LOCALIDAD", "PROVINCIA", "TITULAR_CUENTA", "IBAN", "BAJA", "FECHA_BAJA", "BONIFICADO", "FIANZA"];
$primera_fila = ["Fecha y hora: " . date("d/m/Y H:i:s")];

if ($formato == "excel") {
    ini_set('memory_limit', '512M');
    set_time_limit(300);

    require_once __DIR__ . '/vendor/autoload.php';

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Listado');

    if ($error != "") {
        if (ob_get_length()) ob_end_clean();
        $sheet->setCellValue('A1', $error);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $Name . '.xlsx"');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    $sheet->fromArray($primera_fila, NULL, 'A1');
    $sheet->fromArray($encabezamiento, NULL, 'A2');
    $sheet->freezePane('A3');

    $estiloCabecera = [
        'font' => ['bold' => true],
        'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        ],
    ];
    // Aplicamos estilo solo a la fila de encabezados (A2 hasta O2)
    $sheet->getStyle('A2:O2')->applyFromArray($estiloCabecera);

    $row = 3;
    $result->data_seek(0);
    while ($registro = $result->fetch_assoc()) {
        if (substr(strtoupper($registro["id_nie"] ?? ''), 0, 1) == "P") continue;

        $filaFinal = generarFilaAlumno($registro);
        $sheet->fromArray($filaFinal, NULL, "A$row");

        // Alineaciones específicas
        $sheet->getStyle("D$row")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("G$row")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("L$row:O$row")->getAlignment()->setHorizontal('center');

        // Colores condicionales
        if ($filaFinal[11] === "SI") {
            $sheet->getStyle("L$row:M$row")->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        }
        if ($filaFinal[13] === "SI") {
            $sheet->getStyle("N$row")->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        }
        $row++;
    }

    foreach (range('A', 'O') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    if (ob_get_length()) ob_end_clean();
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $Name . '.xlsx"');
    header('Cache-Control: max-age=0');
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit();

} else {
    // SECCIÓN CSV
    if (ob_get_length()) ob_end_clean(); // CRÍTICO: Limpiar cualquier eco previo o espacio
    
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $Name . '.csv"');

    $output = fopen('php://output', 'w');
    // BOM UTF-8 para que Excel reconozca tildes y Ñ
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    if ($error != "") {
        fputcsv($output, [$error], ';');
        fclose($output);
        exit();
    }

    fputcsv($output, $primera_fila, ";");
    fputcsv($output, $encabezamiento, ";");

    $result->data_seek(0);
    while ($registro = $result->fetch_assoc()) {
        if (substr(strtoupper($registro["id_nie"] ?? ''), 0, 1) == "P") continue;
        $filaFinal = generarFilaAlumno($registro);
        fputcsv($output, $filaFinal, ";");
    }
    fclose($output);
    exit();
}

function generarFilaAlumno($r) {
    return [
        "\t" . ($r["id_nie"] ?? ''),
        ucwords(strtolower($r["apellidos"] ?? '')),
        ucwords(strtolower($r["nombre"] ?? '')),
        $r['edificio'] ?? '',
        $r["curso"] ?? '',
        $r["direccion"] ?? '',
        $r["cp"] ?? '',
        $r["localidad"] ?? '',
        $r["provincia"] ?? '',
        $r["titular_cuenta"] ?? '',
        $r["iban"] ?? '',
        (($r["baja"] ?? 0) == 1) ? "SI" : "NO",
        $r["fecha_baja"] ?? '',
        (($r["bonificado"] ?? 0) == 1) ? "SI" : "NO",
        $r["fianza"] ?? ''
    ];
}