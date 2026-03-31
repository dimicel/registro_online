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


$curso=$_POST["curso_csv_elearning_fctproyecto"];
$turno="E-Learning_f";
$formato = isset($_POST["formato"]) ? $_POST["formato"] : 'csv';

// 1. Preparamos la consulta con los marcadores '?'
$sql = "SELECT * FROM mat_ciclos WHERE turno = ? AND curso = ? ORDER BY apellidos, nombre";
$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    // Si falla la preparación (por ejemplo, error en nombre de columna)
    exit("Error en la preparación: " . $mysqli->error);
}

// 2. Vinculamos las variables
// "ss" indica que enviamos dos Strings (turno y curso)
$stmt->bind_param("ss", $turno, $curso);

// 3. Ejecutamos
$stmt->execute();

// 4. Obtenemos el resultado para poder usarlo como antes ($res->fetch_assoc, etc.)
$res = $stmt->get_result();

if (!$res || $res->num_rows==0){
    $error="No hay matrículas.";
}

$Name = 'matricula_elearning_fct_proyecto_' . $curso;
$encabezamiento = [
    "NIE", 
    "APELLIDOS", 
    "NOMBRE", 
    "NIF", 
    "REGISTRO", 
    "GRADO", 
    "CICLO", 
    "NUEVO_DE_OTRA_COMUNIDAD", 
    "EMAIL", 
    "TELEFONO", 
    "MAYOR_28_AÑOS", 
    "PROYECTO", 
    "FCT"
];

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
        $stmt->close();
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
    $stmt->close();
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
        $stmt->close();
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
    $stmt->close();
    fclose($output);
    exit();
}


function generarFilaAlumno($r) {
    $fila=[$r["id_nie"]];
    array_push($fila,ucwords(strtolower($r["apellidos"])));
    array_push($fila,ucwords(strtolower($r["nombre"])));
    array_push($fila,$r["id_nif"]);
    array_push($fila,$r["registro"]);
    array_push($fila,ucwords(strtolower($r["grado"])));
    array_push($fila,ucwords(strtolower($r["ciclo"])));
    array_push($fila,ucwords(strtolower($r["al_nuevo_otracomunidad"])));
    array_push($fila,$r["email"]);
    array_push($fila,$r["telefono"]);
    array_push($fila,ucwords(strtolower($r["mayor_28"])));
    array_push($fila,ucwords(strtolower($r["proyecto"])));
    array_push($fila,ucwords(strtolower($r["fct"])));
    return $fila;
}


