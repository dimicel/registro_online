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

$anno_calculo=substr($curso,0,4);

$sql="SELECT * 
        FROM mat_ciclos 
        WHERE curso=? 
        ORDER BY turno,grado,ciclo,curso_ciclo,apellidos,nombre";

$stmt=$mysqli->prepare($sql);
if ($stmt){
    $stmt->bind_param("s",$curso);
    $stmt->execute();
    $res=$stmt->get_result();
    $stmt->close();
}else{  
    $error="Error en la consulta: " . $mysqli->error;
}    

if (!$res ||$res->num_rows==0){
    if ($error=="")$error="No hay datos que listar.";
}

$Name = 'seguro_escolar_ciclos_'.$curso;

$encabezamiento=["NIE","ALUMNO","CURSO_ACTUAL","TURNO","GRADO","CICLO","CURSO","FECHA_NAC","EDAD","PAGA_SEGURO"];

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

    while($r=$res->fetch_assoc()){
        if(substr(strtoupper($r["id_nie"]),0,1)== "P") continue;

        $filaFinal = generarFilaAlumno($r);
        fputcsv($output, $filaFinal, ";");
    }
    fclose($output);
    exit();
}


function generarFilaAlumno($r) {
    global $curso; // Traemos la variable $curso que está "arriba" en el script

    // 1. Extraemos el año de inicio del curso escolar (ej: de "2022-2023" sacamos 2022)
    $partes_curso = explode("-", $curso);
    $anno_inicio_curso = intval($partes_curso[0]);

    // 2. Procesamos la fecha de nacimiento (yyyy-mm-dd)
    $f_n = explode("-", $r["fecha_nac"]);
    $a_nac = intval($f_n[0]);
    $m_nac = intval($f_n[1]);
    $d_nac = intval($f_n[2]);

    // 3. Calculamos en qué año el alumno cumple los 28
    $anno_cumple_28 = $a_nac + 28;

    // 4. Lógica del Seguro Escolar:
    // Por defecto pagan todos
    $paga_seguro = "SI";

    // Si el año en que cumple 28 es ANTERIOR al inicio del curso, ya es mayor -> NO PAGA
    if ($anno_cumple_28 < $anno_inicio_curso) {
        $paga_seguro = "NO";
    } 
    // Si cumple los 28 en el MISMO año que empieza el curso...
    elseif ($anno_cumple_28 == $anno_inicio_curso) {
        // ...pero los cumple entre Septiembre (9) y Diciembre (12) -> NO PAGA (según tu regla)
        if ($m_nac >= 9) {
            $paga_seguro = "NO";
        } else {
            // Si los cumplió entre Enero y Agosto de ese mismo año, ya tiene 28 al empezar -> SI PAGA (o NO, según criterio, pero aquí aplicamos "si no, si lo paga")
            $paga_seguro = "SI"; 
        }
    }
    // Si cumple los 28 en el futuro (año siguiente o más), es menor de 28 -> SI PAGA
    else {
        $paga_seguro = "SI";
    }

    // 5. Calculamos la edad real actual para mostrarla (opcional)
    $edad = $anno_inicio_curso - $a_nac;

    // Formateamos fecha para mostrar (dd-mm-yyyy)
    $fecha_mostrar = $d_nac . "-" . $m_nac . "-" . $f_n[0];

    return [
        "\t" . $r["id_nie"],
        ucwords(strtolower($r["apellidos"])) . ", " . ucwords(strtolower($r["nombre"])),
        $r["curso"],
        $r["turno"],
        $r["grado"],
        $r["ciclo"],
        $r["curso_ciclo"],
        $fecha_mostrar,
        $edad,
        $paga_seguro
    ];
}



