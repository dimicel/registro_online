<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");

function generarFilaAlumno($r, $tabla) {
        $fila=["\t". $r["id_nie"],
                ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])),
                $r["sexo"]];

        if($tabla=="premat_2eso"){
            array_push($fila, $r["curso_actual"]);
            array_push($fila, $r["grupo_curso_actual"]);
            array_push($fila, $r["prog_ling"]);
            array_push($fila,$r["materia1"]);
            array_push($fila,$r["materia2"]);
            array_push($fila,$r["materia3"]);
            array_push($fila,$r["materia4"]);
            array_push($fila,$r["materia5"]);
            array_push($fila,$r["materia6"]);
        }
        elseif($tabla=="premat_3eso"){
            array_push($fila, $r["curso_actual"]);
            array_push($fila, $r["grupo_curso_actual"]);
            array_push($fila, $r["prog_ling"]);
            array_push($fila,$r["materia1"]);
            array_push($fila,$r["materia2"]);
            array_push($fila,$r["materia3"]);
            array_push($fila,$r["materia4"]);
            array_push($fila,$r["materia5"]);
            array_push($fila,$r["materia6"]);
        }
        elseif($tabla=="premat_4eso"){
            array_push($fila, $r["curso_actual"]);
            array_push($fila, $r["grupo_curso_actual"]);
            array_push($fila, $r["prog_ling"]);
            array_push($fila,$r["materia1"]);
            array_push($fila,$r["materia2"]);
            array_push($fila,$r["materia3"]);
            array_push($fila,$r["materia4"]);
            array_push($fila,$r["materia5"]);
            array_push($fila,$r["materia6"]);
            array_push($fila,$r["materia7"]);
            array_push($fila,$r["materia8"]);
            array_push($fila,$r["materia9"]);
            array_push($fila,$r["materia10"]);
            array_push($fila,$r["materia11"]);
            array_push($fila,$r["materia12"]);
            array_push($fila,$r["materia13"]);
            array_push($fila,$r["materia14"]);
        }
        elseif($tabla=="premat_3esodiv"){
            array_push($fila, $r["curso_actual"]);
            array_push($fila, $r["grupo_curso_actual"]);
            array_push($fila, $r["materia1"]);
            array_push($fila, $r["materia2"]);
            array_push($fila, $r["materia3"]);
            array_push($fila, $r["materia4"]);
        }
        elseif($tabla=="premat_4esodiv"){
            array_push($fila, $r["curso_actual"]);
            array_push($fila, $r["grupo_curso_actual"]);
            array_push($fila, $r["materia1"]);
            array_push($fila, $r["materia2"]);
            array_push($fila, $r["materia3"]);
            array_push($fila, $r["materia4"]);
            array_push($fila, $r["materia5"]);
            array_push($fila, $r["materia6"]);
            array_push($fila, $r["materia7"]);
            array_push($fila, $r["materia8"]);
            array_push($fila, $r["materia9"]);
            array_push($fila, $r["materia10"]);
            array_push($fila, $r["materia11"]);
        }
        elseif($tabla=="premat_1bach_h" || $tabla=="premat_1bach_c"){
            array_push($fila, $r["modalidad"]);
            array_push($fila, $r["materia1"]);
            array_push($fila, $r["materia2"]);
            array_push($fila, $r["materia3"]);
            array_push($fila, $r["materia4"]);
            array_push($fila, $r["materia5"]);
            array_push($fila, $r["materia6"]);
            array_push($fila, $r["materia7"]);
            array_push($fila, $r["materia8"]);
            array_push($fila, $r["materia9"]);
            array_push($fila, $r["materia10"]);
            array_push($fila, $r["materia11"]);
            array_push($fila, $r["materia12"]);
            array_push($fila, $r["materia13"]);
            array_push($fila, $r["materia14"]);
            array_push($fila, $r["materia15"]);
            array_push($fila, $r["materia16"]);
            array_push($fila, $r["materia17"]);
            array_push($fila, $r["materia18"]);
            array_push($fila, $r["materia19"]);
            array_push($fila, $r["materia20"]);
            array_push($fila, $r["materia21"]);
        }
        elseif($tabla=="premat_2bach_h"){
            array_push($fila, $r["materia1"]);
            array_push($fila, $r["materia2"]);
            array_push($fila, $r["materia3"]);
            array_push($fila, $r["materia4"]);
            array_push($fila, $r["materia5"]);
            array_push($fila, $r["materia6"]);
            array_push($fila, $r["materia7"]);
            array_push($fila, $r["materia8"]);
            array_push($fila, $r["materia9"]);
            array_push($fila, $r["materia10"]);
            array_push($fila, $r["materia11"]);
            array_push($fila, $r["materia12"]);
            array_push($fila, $r["materia13"]);
            array_push($fila, $r["materia14"]);
            array_push($fila, $r["materia15"]);
            array_push($fila, $r["materia16"]);
            array_push($fila, $r["materia17"]);
            array_push($fila, $r["materia18"]);
            array_push($fila, $r["materia19"]);
            array_push($fila, $r["materia20"]);
            array_push($fila, $r["materia21"]);
        }
        elseif($tabla=="premat_2bach_c"){
            array_push($fila, $r["materia1"]);
            array_push($fila, $r["materia2"]);
            array_push($fila, $r["materia3"]);
            array_push($fila, $r["materia4"]);
            array_push($fila, $r["materia5"]);
            array_push($fila, $r["materia6"]);
            array_push($fila, $r["materia7"]);
            array_push($fila, $r["materia8"]);
            array_push($fila, $r["materia9"]);
            array_push($fila, $r["materia10"]);
            array_push($fila, $r["materia11"]);
            array_push($fila, $r["materia12"]);
            array_push($fila, $r["materia13"]);
            array_push($fila, $r["materia14"]);
            array_push($fila, $r["materia15"]);
            array_push($fila, $r["materia16"]);
            array_push($fila, $r["materia17"]);
            array_push($fila, $r["materia18"]);
            array_push($fila, $r["materia19"]);
            array_push($fila, $r["materia20"]);
        }

        return $fila;
}


$error="";
$encabezamiento="";

include("conexion.php");
if ($mysqli->errno>0) exit("Error en servidor.");


$tabla=$_POST["premat_csv"];
$tabla_db=$tabla;
$curso=$_POST["curso_csv"];
$formato = isset($_POST["formato"]) ? $_POST["formato"] : 'csv';
$registros_obtenidos = true;

$grupos=Array(
    "premat_2eso" => "2º ESO",
    "premat_3eso" => "3º ESO",
    "premat_4eso" => "4º ESO",
    "premat_3esodiv" => "3º ESO DIV",
    "premat_4esodiv" => "4º ESO DIV",
    "premat_1bach_c" => "1º Bachillerato",
    "premat_1bach_h" => "1º Bachillerato",
    "premat_2bach_c" => "2º Bach. Ciencias y Tecnología",
    "premat_2bach_h" => "2º Bach. HH.CC.SS."
);

if (!isset($grupos[$tabla])) {
    // Si la tabla no está en nuestra lista, cortamos el grifo
    exit("Error: El grupo solicitado no es válido.");
}

if(strpos($tabla_db,"premat_")!==false){
    if (strpos($tabla_db,"eso")!==false) $tabla_db="premat_eso";
    else $tabla_db="premat_bach";
}

if ($tabla == "premat_1bach_h") {
    $sql = "SELECT * FROM $tabla_db WHERE curso = ? AND modalidad = 'Humanidades y Ciencias Sociales' ORDER BY apellidos, nombre";
} elseif ($tabla == "premat_1bach_c") {
    $sql = "SELECT * FROM $tabla_db WHERE curso = ? AND modalidad = 'Ciencias y Tecnología' ORDER BY apellidos, nombre";
} else {
    $sql = "SELECT * FROM $tabla_db WHERE curso = ? AND grupo = ? ORDER BY apellidos, nombre";
}

// 2. Preparamos la sentencia en el servidor
$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    exit("Error en la preparación: " . $mysqli->error);
}

// 3. Vinculamos los datos (Bind)
// "s" significa string. Si hay dos "?", ponemos "ss"
if ($tabla == "premat_1bach_h" || $tabla == "premat_1bach_c") {
    $stmt->bind_param("s", $curso);
} else {
    $grupo_nombre = $grupos[$tabla];
    $stmt->bind_param("ss", $curso, $grupo_nombre);
}

// 4. Ejecutamos y obtenemos el resultado
$stmt->execute();
$res = $stmt->get_result();

if (!$res || $res->num_rows==0){
    $error="No hay registros que listar.";
}

$Name = $tabla.'.csv';

if($tabla=="premat_2eso") $encabezamiento=["NIE","ALUMNO","SEXO","CURSO_ACTUAL","GRUPO","PROGRAMA_LING","REL/AT_EDUC","PRIMER_IDIOMA","OPT1","OPT2","OPT3","OPT4"];
elseif($tabla=="premat_3eso") $encabezamiento=["NIE","ALUMNO","SEXO","CURSO_ACTUAL","GRUPO","PROGRAMA_LING","REL/AT_EDUC","PRIMER_IDIOMA","OPT1","OPT2","OPT3","OPT4"];
elseif ($tabla == "premat_4eso") $encabezamiento = ["NIE", "ALUMNO", "SEXO", "CURSO_ACTUAL", "GRUPO", "PROGRAMA_LING", "PRIMER_IDIOMA", "REL/AT_EDUC", "MATEMATICAS", "OPC_BLOQUE", "OPC_BLOQUE1_1", "OPC_BLOQUE1_2", "OPC_BLOQUE1_3", "OPC_BLOQUE1_4", "OPC_BLOQUE1_5", "OPTATIVA1", "OPTATIVA2", "OPTATIVA3", "OPTATIVA4", "OPTATIVA5"];
elseif ($tabla == "premat_3esodiv") $encabezamiento = ["NIE", "ALUMNO", "SEXO", "CURSO_ACTUAL", "GRUPO", "REL/AT.EDUC", "OPTATIVA1", "OPTATIVA2", "OPTATIVA3"];
elseif ($tabla == "premat_4esodiv") $encabezamiento = ["NIE", "ALUMNO", "SEXO", "CURSO_ACTUAL", "GRUPO", "REL/AT.EDUC", "OPCION1", "OPCION2", "OPCION3", "OPCION4", "OPCION5", "OPTATIVA1", "OPTATIVA2", "OPTATIVA3", "OPTATIVA4", "OPTATIVA5"];
elseif($tabla=="premat_1bach_h" || $tabla=="premat_1bach_c")$encabezamiento = ["NIE", "ALUMNO", "SEXO", "MODALIDAD", "PRIMER_IDIOMA", "REL/AT_EDUC", "OBLIGATORIA1", "OBLIGATORIA2", "OBLIGATORIA3", "OPTATIVA1", "OPTATIVA2", "OPTATIVA3", "OPTATIVA4", "OPTATIVA5", "OPTATIVA6", "OPTATIVA7", "OPTATIVA8", "OPTATIVA9", "OPTATIVA10", "OPTATIVA11", "OPTATIVA12", "OPTATIVA13", "OPTATIVA14", "OPTATIVA15", "OPTATIVA16"];
elseif($tabla=="premat_2bach_h"){
    $encabezamiento = [
    "NIE", "ALUMNO", "SEXO", "PRIMER_IDIOMA", "MODALIDAD1", "MODALIDAD2", "MODALIDAD3", 
    "OPTATIVA1", "OPTATIVA2", "OPTATIVA3", "OPTATIVA4", "OPTATIVA5", "OPTATIVA6", "OPTATIVA7", "OPTATIVA8", 
    "OPTATIVA9", "OPTATIVA10", "OPTATIVA11", "OPTATIVA12", "OPTATIVA13", "OPTATIVA14", "OPTATIVA15", "OPTATIVA16", "OPTATIVA17"
    ];
} 
elseif($tabla=="premat_2bach_c"){
    $encabezamiento = [
    "NIE", "ALUMNO", "SEXO", "PRIMER_IDIOMA", "MODALIDAD1", "MODALIDAD2", "MODALIDAD3", 
    "OPTATIVA1", "OPTATIVA2", "OPTATIVA3", "OPTATIVA4", "OPTATIVA5", "OPTATIVA6", "OPTATIVA7", "OPTATIVA8", 
    "OPTATIVA9", "OPTATIVA10", "OPTATIVA11", "OPTATIVA12", "OPTATIVA13", "OPTATIVA14", "OPTATIVA15", "OPTATIVA16"
    ];
}

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
        header('Content-Disposition: attachment;filename="listado_' . $tabla . '_' . $curso . '.xlsx"');
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

        $filaFinal = generarFilaAlumno($r, $tabla);
        
        // Insertar toda la fila de golpe (mucho más rápido)
        $sheet->fromArray($filaFinal, NULL, "A$row");
        $row++;
    }
    $stmt->close();
    // 1. Borramos cualquier salida previa (espacios, errores ocultos)
    if (ob_get_length()) ob_end_clean();

    // 2. Cabeceras oficiales
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="listado_' . $tabla . '_' . $curso . '.xlsx"');
    header('Cache-Control: max-age=0');
    header('Pragma: public');

    // 3. Generar archivo
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    
    // 4. Salida limpia
    exit();

} else {
    header('Content-Type: text/csv; charset=latin1');
    header('Content-Disposition: attachment;filename="listado_' . $tabla . '_' . $curso . '.csv"');

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

        $filaFinal = generarFilaAlumno($r, $tabla);
        fputcsv($output, $filaFinal, ";");
    }
    $stmt->close();
    fclose($output);
    exit();
}
                
 


