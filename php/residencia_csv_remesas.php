<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$error="";

include("conexion.php");
if ($mysqli->errno > 0) {
    http_response_code(500);
    echo "Error en servidor.";
    exit;
}

$curso=$_POST["curso_csv_remesas"];
$formato=$_POST["formato"] ?? "csv";
$result=null;

$sql="SELECT * FROM residentes WHERE curso=? ORDER BY apellidos,nombre";
$stmt = $mysqli->prepare($sql);

if($stmt){
    $stmt->bind_param("s", $curso);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
}else{
    $error="Error en la consulta: " . $mysqli->error;
}

if (!$result ||$result->num_rows==0){
    $error="No hay datos que listar.";
}



$Name = 'remesas_'.date('d-m-Y');
$encabezamiento=["NIE","APELLIDOS","NOMBRE","EDIFICIO","CURSO","DIRECCION","CP","LOCALIDAD","PROVINCIA","TITULAR_CUENTA","IBAN","BAJA","FECHA_BAJA","BONIFICADO","FIANZA"];

if ($formato=="excel"){
    // 1. Recursos y Librería
    ini_set('memory_limit', '512M'); 
    set_time_limit(300);

    $primera_fila=["Fecha y hora: ".date("d/m/Y H:i:s")];

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
    while ($registro = $result->fetch_assoc()) {
        if (substr(strtoupper($registro["id_nie"]),0,1)== "P") continue;

        $filaFinal = generarFilaAlumno($registro);
        
        // Insertar toda la fila de golpe (mucho más rápido)
        $sheet->fromArray($filaFinal, NULL, "A$row");
        $sheet->getStyle("D$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("G$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("L$row:O$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        if ($filaFinal[11] == "SI"){
            $sheet->getStyle("L$row")->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
            $sheet->getStyle("M$row")->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        }
        if ($filaFinal[13] == "SI"){
            $sheet->getStyle("N$row")->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        }
        $row++;
    }
    // Ajustar el ancho de las columnas automáticamente
    foreach (range('A', 'O') as $col) {
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
    $primera_file=["Fecha y hora: ".date("d/m/Y H:i:s"),"","","","","","","","","","","","","","","",""];
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    if ($error!="") {
        fputcsv($output, [$error], ';');
        fclose($output);
        exit();
    }
    fputcsv($output, $primera_fila, ";");
    fputcsv($output, $encabezamiento, ";");

    while ($registro = $result->fetch_assoc()) {
        if (substr(strtoupper($registro["id_nie"]),0,1)== "P") continue;
        $filaFinal = generarFilaAlumno($registro);
        fputcsv($output, $filaFinal, ";");
    }
    fclose($output);
    exit();
}


function generarFilaAlumno($r) {
    return [
            "\t" . $r["id_nie"],
            ucwords(strtolower($r["apellidos"])),
            ucwords(strtolower($r["nombre"])),
            $r['edificio'],
            $r["curso"],
            $r["direccion"],
            $r["cp"],
            $r["localidad"],
            $r["provincia"],
            $r["titular_cuenta"],
            $r["iban"],
            $r["baja"]==1?"SI":"NO",
            $r["fecha_baja"],
            $r["bonificado"]==1?"SI":"NO",
            $r["fianza"]
    ];
    
}

