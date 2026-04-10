<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 0);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$error="";

include("conexion.php");
if ($mysqli->errno>0) exit("Error en servidor.");


$curso=$_POST["curso_csv_prog_ling"];
$formato = isset($_POST["formato"]) ? $_POST["formato"] : 'csv';
$res=null;

$sql="select id_nie,apellidos,nombre,curso,prog_ling,grupo from mat_eso where curso=? order by apellidos,nombre,grupo";
$stmt=$mysqli->prepare($sql);
if($stmt){
    $stmt->bind_param("s",$curso);
    $stmt->execute();
    $res=$stmt->get_result();
    $stmt->close();
}
else {
    // Manejo de error en la preparación
    $error="Error en la consulta: " . $mysqli->error;
}

if (!$res ||$res->num_rows==0){
    if ($error=="")$error="No hay datos que listar.";
}

$Name = 'eso_programa_ling_'.$curso;

$encabezamiento=['NIE','APELLIDOS','NOMBRE','GRUPO','PROGRAMA LINGÜÍSTICO'];

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
    $sheet->fromArray($encabezamiento, NULL, 'A1');
    $sheet->freezePane('A2'); //Inmoviliza la priemra fila
    $estiloCabecera = [
        'font' => ['bold' => true],
        'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
        ],
    ];
    $sheet->getStyle('1:1')->applyFromArray($estiloCabecera);

    //Llenado de datos
    $row = 2;
    $res->data_seek(0); // Reiniciamos el puntero por si acaso
    while ($r = $res->fetch_assoc()) {
        if (substr(strtoupper($r["id_nie"]),0,1)== "P") continue;

        $filaFinal = generarFilaAlumno($r);
        
        // Insertar toda la fila de golpe (mucho más rápido)
        $sheet->fromArray($filaFinal, NULL, "A$row");
        if (strtolower($r["prog_ling"])!="no"){
            $sheet->getStyle("E$row")->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        }
        $sheet->getStyle("D$row:E$row")->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\AlignmentAlignment::HORIZONTAL_CENTER);
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
}
else {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="'.$Name.'.csv"');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    if ($error!="") {
        fputcsv($output, [$error], ';');
        fclose($output);
        exit();
    }
    fputcsv($output, $encabezamiento, ";");

    while($r=$res->fetch_array(MYSQLI_ASSOC)){
        if(substr(strtoupper($r["id_nie"]),0,1)== "P") continue;

        $filaFinal = generarFilaAlumno($r);
        fputcsv($output, $filaFinal, ";");
    }
    fclose($output);
    exit();
}

function generarFilaAlumno($r) {
    return [
        "\t".$r["id_nie"],
        ucwords(strtolower($r["apellidos"])),
        ucwords(strtolower($r["nombre"])),
        ucwords(strtolower($r["grupo"])),
        ucwords(strtolower($r["prog_ling"]))
    ];
}

