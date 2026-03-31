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
if ($mysqli->errno>0) exit("Error en servidor.");


$curso=$_POST["curso_csv_consolidaprematricula"];
$formato = isset($_POST["formato"]) ? $_POST["formato"] : 'csv';
$res = null;
// 1. Definimos la estructura de la consulta con los marcadores '?'
$sql = "SELECT id_nie, apellidos, nombre, grupo, consolida_premat, curso 
        FROM mat_eso 
        WHERE consolida_premat = 'Si' AND curso = ? 
        UNION ALL 
        SELECT id_nie, apellidos, nombre, grupo, consolida_premat, curso 
        FROM mat_bach 
        WHERE consolida_premat = 'Si' AND curso = ? 
        ORDER BY grupo, apellidos, nombre";

// 2. Preparamos la sentencia
$stmt = $mysqli->prepare($sql);

if ($stmt) {
    // 3. Vinculamos los parámetros
    // Usamos "ss" porque enviamos dos strings (uno para cada '?')
    $stmt->bind_param("ss", $curso, $curso);

    // 4. Ejecutamos
    $stmt->execute();

    // 5. Obtenemos el resultado
    $res = $stmt->get_result();

    // Ya puedes usar $res como lo hacías antes (fetch_assoc, num_rows, etc.)
    // while($r = $res->fetch_assoc()) { ... }

    $stmt->close();
} else {
    // Manejo de error en la preparación
    $error="Error en la consulta: " . $mysqli->error;
}

if (!$res ||$res->num_rows==0){
    if ($error=="")$error="No hay datos que listar.";
}

$Name = 'prematriculas_consolidadas_'.$curso;

$encabezamiento=["NIE","APELLIDOS","NOMBRE","GRUPO"];

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
        $row++;
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
        ucwords(strtolower($r["grupo"]))
    ];
}


