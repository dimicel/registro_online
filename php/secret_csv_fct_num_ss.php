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


$curso=$_POST["curso_csv_seguro"];
$formato = isset($_POST["formato"]) ? $_POST["formato"] : 'csv';
$res=null;

$sql="SELECT 
        u.apellidos, 
        u.nombre, 
        u.id_nie, 
        ud.num_ss, 
        ud.fecha_mod_nuss
    FROM usuarios u
    INNER JOIN usuarios_dat ud ON u.id_nie = ud.id_nie
    WHERE ud.num_ss IS NOT NULL 
      AND ud.num_ss <> ''
      AND (
          EXISTS (
              SELECT 1 
              FROM mat_ciclos mc 
              WHERE mc.id_nie = u.id_nie 
                AND mc.curso = ?
          )
          OR
          EXISTS (
              SELECT 1 
              FROM mat_fpb mf 
              WHERE mf.id_nie = u.id_nie 
                AND mf.curso = ?
          )
      )
    ORDER BY u.apellidos ASC, u.nombre ASC
";

$stmt=$mysqli->prepare($sql);
if($stmt){
    $stmt->bind_param("ss",$curso,$curso);
    $stmt->execute();
    $res=$stmt->get_result();
    $stmt->close();
}else{
    $error="Error en la consulta: " . $mysqli->error;
}

if (!$res ||$res->num_rows==0){
    if ($error=="")$error="No hay datos que listar.";
}

$Name = 'listado_num_ss_'.$curso;

$encabezamiento=["NIE","ALUMNO","Nº SEGURIDAD SOCIAL","ULTIMA MODIFICACION NUSS"];

'NIE;ALUMNO;Nº SEGURIDAD SOCIAL;ULTIMA MODIFICACION NUSS'.PHP_EOL;

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
    // Ajustar el ancho de las columnas automáticamente
    foreach (range('A', 'D') as $col) {
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

    while($r=$res->fetch_assoc()){
        if(substr(strtoupper($r["id_nie"]),0,1)== "P") continue;

        $filaFinal = generarFilaAlumno($r);
        fputcsv($output, $filaFinal, ";");
    }
    fclose($output);
    exit();
}


function generarFilaAlumno($r) {
    // Convertimos la fecha de MySQL (aaaa-mm-dd) al formato español (dd/mm/aaaa)
    $fecha_es = "";
    if (!empty($r["fecha_mod_nuss"]) && $r["fecha_mod_nuss"] !== "0000-00-00") {
        $date = new DateTime($r["fecha_mod_nuss"]);
        $fecha_es = $date->format('d/m/Y');
    }

    return [
        "\t" . $r["id_nie"],
        ucwords(strtolower($r["apellidos"])) . ", " . ucwords(strtolower($r["nombre"])),
        "\t" . $r["num_ss"],
        $fecha_es
    ];
}



