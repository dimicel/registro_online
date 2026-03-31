<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$error="";
$Datos="";

include("conexion.php");
if ($mysqli->errno>0) $error="Error en servidor.";


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


if(strpos($tabla_db,"premat_")>=0){
    if (strpos($tabla_db,"eso")) $tabla_db="premat_eso";
    else $tabla_db="premat_bach";
}

if ($tabla=="premat_1bach_h") $consulta="select * from $tabla_db where curso='$curso' and modalidad='Humanidades y Ciencias Sociales' order by apellidos,nombre";
elseif ($tabla=="premat_1bach_c") $consulta="select * from $tabla_db where curso='$curso' and modalidad='Ciencias y Tecnología' order by apellidos,nombre";
else$consulta="select * from $tabla_db where curso='$curso' and grupo='$grupos[$tabla]' order by apellidos,nombre";

$res=$mysqli->query($consulta);

if (!$res || $res->num_rows==0){
    $registros_obtenidos = false;
}

$Name = $tabla.'.csv';
$FileName = "./$Name";

if($tabla=="premat_2eso") $Datos=["NIE","ALUMNO","SEXO","CURSO_ACTUAL","GRUPO","PROGRAMA_LING","REL/AT_EDUC","PRIMER_IDIOMA","OPT1","OPT2","OPT3","OPT4"];
elseif($tabla=="premat_3eso") $Datos=["NIE","ALUMNO","SEXO","CURSO_ACTUAL","GRUPO","PROGRAMA_LING","REL/AT_EDUC","PRIMER_IDIOMA","OPT1","OPT2","OPT3","OPT4"];
elseif ($tabla == "premat_4eso") $Datos = ["NIE", "ALUMNO", "SEXO", "CURSO_ACTUAL", "GRUPO", "PROGRAMA_LING", "PRIMER_IDIOMA", "REL/AT_EDUC", "MATEMATICAS", "OPC_BLOQUE", "OPC_BLOQUE1_1", "OPC_BLOQUE1_2", "OPC_BLOQUE1_3", "OPC_BLOQUE1_4", "OPC_BLOQUE1_5", "OPTATIVA1", "OPTATIVA2", "OPTATIVA3", "OPTATIVA4", "OPTATIVA5"];
elseif ($tabla == "premat_3esodiv") $Datos = ["NIE", "ALUMNO", "SEXO", "CURSO_ACTUAL", "GRUPO", "REL/AT.EDUC", "OPTATIVA1", "OPTATIVA2", "OPTATIVA3"];
elseif ($tabla == "premat_4esodiv") $Datos = ["NIE", "ALUMNO", "SEXO", "CURSO_ACTUAL", "GRUPO", "REL/AT.EDUC", "OPCION1", "OPCION2", "OPCION3", "OPCION4", "OPCION5", "OPTATIVA1", "OPTATIVA2", "OPTATIVA3", "OPTATIVA4", "OPTATIVA5"];
elseif($tabla=="premat_1bach_h" || $tabla=="premat_1bach_c")$Datos = ["NIE", "ALUMNO", "SEXO", "MODALIDAD", "PRIMER_IDIOMA", "REL/AT_EDUC", "OBLIGATORIA1", "OBLIGATORIA2", "OBLIGATORIA3", "OPTATIVA1", "OPTATIVA2", "OPTATIVA3", "OPTATIVA4", "OPTATIVA5", "OPTATIVA6", "OPTATIVA7", "OPTATIVA8", "OPTATIVA9", "OPTATIVA10", "OPTATIVA11", "OPTATIVA12", "OPTATIVA13", "OPTATIVA14", "OPTATIVA15", "OPTATIVA16"];
elseif($tabla=="premat_2bach_h"){
    $Datos = [
    "NIE", "ALUMNO", "SEXO", "PRIMER_IDIOMA", "MODALIDAD1", "MODALIDAD2", "MODALIDAD3", 
    "OPTATIVA1", "OPTATIVA2", "OPTATIVA3", "OPTATIVA4", "OPTATIVA5", "OPTATIVA6", "OPTATIVA7", "OPTATIVA8", 
    "OPTATIVA9", "OPTATIVA10", "OPTATIVA11", "OPTATIVA12", "OPTATIVA13", "OPTATIVA14", "OPTATIVA15", "OPTATIVA16", "OPTATIVA17"
    ];
} 
elseif($tabla=="premat_2bach_c"){
    $Datos = [
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
        $sheet->setCellValue('A1', $error);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="listado_' . $curso . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
    $sheet->fromArray($Datos, NULL, 'A1');
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

        $Datos=["\t". $r["id_nie"],
                ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])),
                $r["sexo"]];

        if($tabla=="premat_2eso"){
            array_push($Datos, $r["curso_actual"]);
            array_push($Datos, $r["grupo_curso_actual"]);
            array_push($Datos, $r["prog_ling"]);
            array_push($Datos,$r["materia1"]);
            array_push($Datos,$r["materia2"]);
            array_push($Datos,$r["materia3"]);
            array_push($Datos,$r["materia4"]);
            array_push($Datos,$r["materia5"]);
            array_push($Datos,$r["materia6"]);
        }
        elseif($tabla=="premat_3eso"){
            array_push($Datos, $r["curso_actual"]);
            array_push($Datos, $r["grupo_curso_actual"]);
            array_push($Datos, $r["prog_ling"]);
            array_push($Datos,$r["materia1"]);
            array_push($Datos,$r["materia2"]);
            array_push($Datos,$r["materia3"]);
            array_push($Datos,$r["materia4"]);
            array_push($Datos,$r["materia5"]);
            array_push($Datos,$r["materia6"]);
        }
        elseif($tabla=="premat_4eso"){
            array_push($Datos, $r["curso_actual"]);
            array_push($Datos, $r["grupo_curso_actual"]);
            array_push($Datos, $r["prog_ling"]);
            array_push($Datos,$r["materia1"]);
            array_push($Datos,$r["materia2"]);
            array_push($Datos,$r["materia3"]);
            array_push($Datos,$r["materia4"]);
            array_push($Datos,$r["materia5"]);
            array_push($Datos,$r["materia6"]);
            array_push($Datos,$r["materia7"]);
            array_push($Datos,$r["materia8"]);
            array_push($Datos,$r["materia9"]);
            array_push($Datos,$r["materia10"]);
            array_push($Datos,$r["materia11"]);
            array_push($Datos,$r["materia12"]);
            array_push($Datos,$r["materia13"]);
            array_push($Datos,$r["materia14"]);
        }
        elseif($tabla=="premat_3esodiv"){
            array_push($Datos, $r["curso_actual"]);
            array_push($Datos, $r["grupo_curso_actual"]);
            array_push($Datos, $r["materia1"]);
            array_push($Datos, $r["materia2"]);
            array_push($Datos, $r["materia3"]);
            array_push($Datos, $r["materia4"]);
        }
        elseif($tabla=="premat_4esodiv"){
            array_push($Datos, $r["curso_actual"]);
            array_push($Datos, $r["grupo_curso_actual"]);
            array_push($Datos, $r["materia1"]);
            array_push($Datos, $r["materia2"]);
            array_push($Datos, $r["materia3"]);
            array_push($Datos, $r["materia4"]);
            array_push($Datos, $r["materia5"]);
            array_push($Datos, $r["materia6"]);
            array_push($Datos, $r["materia7"]);
            array_push($Datos, $r["materia8"]);
            array_push($Datos, $r["materia9"]);
            array_push($Datos, $r["materia10"]);
            array_push($Datos, $r["materia11"]);
        }
        elseif($tabla=="premat_1bach_h" || $tabla=="premat_1bach_c"){
            array_push($Datos, $r["modalidad"]);
            array_push($Datos, $r["materia1"]);
            array_push($Datos, $r["materia2"]);
            array_push($Datos, $r["materia3"]);
            array_push($Datos, $r["materia4"]);
            array_push($Datos, $r["materia5"]);
            array_push($Datos, $r["materia6"]);
            array_push($Datos, $r["materia7"]);
            array_push($Datos, $r["materia8"]);
            array_push($Datos, $r["materia9"]);
            array_push($Datos, $r["materia10"]);
            array_push($Datos, $r["materia11"]);
            array_push($Datos, $r["materia12"]);
            array_push($Datos, $r["materia13"]);
            array_push($Datos, $r["materia14"]);
            array_push($Datos, $r["materia15"]);
            array_push($Datos, $r["materia16"]);
            array_push($Datos, $r["materia17"]);
            array_push($Datos, $r["materia18"]);
            array_push($Datos, $r["materia19"]);
            array_push($Datos, $r["materia20"]);
            array_push($Datos, $r["materia21"]);
        }
        elseif($tabla=="premat_2bach_h"){
            array_push($Datos, $r["materia1"]);
            array_push($Datos, $r["materia2"]);
            array_push($Datos, $r["materia3"]);
            array_push($Datos, $r["materia4"]);
            array_push($Datos, $r["materia5"]);
            array_push($Datos, $r["materia6"]);
            array_push($Datos, $r["materia7"]);
            array_push($Datos, $r["materia8"]);
            array_push($Datos, $r["materia9"]);
            array_push($Datos, $r["materia10"]);
            array_push($Datos, $r["materia11"]);
            array_push($Datos, $r["materia12"]);
            array_push($Datos, $r["materia13"]);
            array_push($Datos, $r["materia14"]);
            array_push($Datos, $r["materia15"]);
            array_push($Datos, $r["materia16"]);
            array_push($Datos, $r["materia17"]);
            array_push($Datos, $r["materia18"]);
            array_push($Datos, $r["materia19"]);
            array_push($Datos, $r["materia20"]);
            array_push($Datos, $r["materia21"]);
        }
        elseif($tabla=="premat_2bach_c"){
            array_push($Datos, $r["materia1"]);
            array_push($Datos, $r["materia2"]);
            array_push($Datos, $r["materia3"]);
            array_push($Datos, $r["materia4"]);
            array_push($Datos, $r["materia5"]);
            array_push($Datos, $r["materia6"]);
            array_push($Datos, $r["materia7"]);
            array_push($Datos, $r["materia8"]);
            array_push($Datos, $r["materia9"]);
            array_push($Datos, $r["materia10"]);
            array_push($Datos, $r["materia11"]);
            array_push($Datos, $r["materia12"]);
            array_push($Datos, $r["materia13"]);
            array_push($Datos, $r["materia14"]);
            array_push($Datos, $r["materia15"]);
            array_push($Datos, $r["materia16"]);
            array_push($Datos, $r["materia17"]);
            array_push($Datos, $r["materia18"]);
            array_push($Datos, $r["materia19"]);
            array_push($Datos, $r["materia20"]);
        }

        $col=1;
        foreach ($Datos as $dato){
            $sheet->setCellValueByColumnAndRow($col , $row, $dato);
            $col++;
        }
        $row++;
    }

} else {
    header('Content-Type: text/csv; charset=latin1');
    header('Content-Disposition: attachment; filename="' . $Name . '"');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    if ($error!="") {
        fputcsv($output, [$error], ';');
        fclose($output);
        exit();
    }
    fputcsv($output, $Datos, ";");

    while($r=$res->fetch_array(MYSQLI_ASSOC)){
        if(substr(strtoupper($r["id_nie"]),0,1)== "P") continue;

        $Datos=["\t". $r["id_nie"],
                ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])),
                $r["sexo"]];

        if($tabla=="premat_2eso"){
            array_push($Datos, $r["curso_actual"]);
            array_push($Datos, $r["grupo_curso_actual"]);
            array_push($Datos, $r["prog_ling"]);
            array_push($Datos,$r["materia1"]);
            array_push($Datos,$r["materia2"]);
            array_push($Datos,$r["materia3"]);
            array_push($Datos,$r["materia4"]);
            array_push($Datos,$r["materia5"]);
            array_push($Datos,$r["materia6"]);
        }
        elseif($tabla=="premat_3eso"){
            array_push($Datos, $r["curso_actual"]);
            array_push($Datos, $r["grupo_curso_actual"]);
            array_push($Datos, $r["prog_ling"]);
            array_push($Datos,$r["materia1"]);
            array_push($Datos,$r["materia2"]);
            array_push($Datos,$r["materia3"]);
            array_push($Datos,$r["materia4"]);
            array_push($Datos,$r["materia5"]);
            array_push($Datos,$r["materia6"]);
        }
        elseif($tabla=="premat_4eso"){
            array_push($Datos, $r["curso_actual"]);
            array_push($Datos, $r["grupo_curso_actual"]);
            array_push($Datos, $r["prog_ling"]);
            array_push($Datos,$r["materia1"]);
            array_push($Datos,$r["materia2"]);
            array_push($Datos,$r["materia3"]);
            array_push($Datos,$r["materia4"]);
            array_push($Datos,$r["materia5"]);
            array_push($Datos,$r["materia6"]);
            array_push($Datos,$r["materia7"]);
            array_push($Datos,$r["materia8"]);
            array_push($Datos,$r["materia9"]);
            array_push($Datos,$r["materia10"]);
            array_push($Datos,$r["materia11"]);
            array_push($Datos,$r["materia12"]);
            array_push($Datos,$r["materia13"]);
            array_push($Datos,$r["materia14"]);
        }
        elseif($tabla=="premat_3esodiv"){
            array_push($Datos, $r["curso_actual"]);
            array_push($Datos, $r["grupo_curso_actual"]);
            array_push($Datos, $r["materia1"]);
            array_push($Datos, $r["materia2"]);
            array_push($Datos, $r["materia3"]);
            array_push($Datos, $r["materia4"]);
        }
        elseif($tabla=="premat_4esodiv"){
            array_push($Datos, $r["curso_actual"]);
            array_push($Datos, $r["grupo_curso_actual"]);
            array_push($Datos, $r["materia1"]);
            array_push($Datos, $r["materia2"]);
            array_push($Datos, $r["materia3"]);
            array_push($Datos, $r["materia4"]);
            array_push($Datos, $r["materia5"]);
            array_push($Datos, $r["materia6"]);
            array_push($Datos, $r["materia7"]);
            array_push($Datos, $r["materia8"]);
            array_push($Datos, $r["materia9"]);
            array_push($Datos, $r["materia10"]);
            array_push($Datos, $r["materia11"]);
        }
        elseif($tabla=="premat_1bach_h" || $tabla=="premat_1bach_c"){
            array_push($Datos, $r["modalidad"]);
            array_push($Datos, $r["materia1"]);
            array_push($Datos, $r["materia2"]);
            array_push($Datos, $r["materia3"]);
            array_push($Datos, $r["materia4"]);
            array_push($Datos, $r["materia5"]);
            array_push($Datos, $r["materia6"]);
            array_push($Datos, $r["materia7"]);
            array_push($Datos, $r["materia8"]);
            array_push($Datos, $r["materia9"]);
            array_push($Datos, $r["materia10"]);
            array_push($Datos, $r["materia11"]);
            array_push($Datos, $r["materia12"]);
            array_push($Datos, $r["materia13"]);
            array_push($Datos, $r["materia14"]);
            array_push($Datos, $r["materia15"]);
            array_push($Datos, $r["materia16"]);
            array_push($Datos, $r["materia17"]);
            array_push($Datos, $r["materia18"]);
            array_push($Datos, $r["materia19"]);
            array_push($Datos, $r["materia20"]);
            array_push($Datos, $r["materia21"]);
        }
        elseif($tabla=="premat_2bach_h"){
            array_push($Datos, $r["materia1"]);
            array_push($Datos, $r["materia2"]);
            array_push($Datos, $r["materia3"]);
            array_push($Datos, $r["materia4"]);
            array_push($Datos, $r["materia5"]);
            array_push($Datos, $r["materia6"]);
            array_push($Datos, $r["materia7"]);
            array_push($Datos, $r["materia8"]);
            array_push($Datos, $r["materia9"]);
            array_push($Datos, $r["materia10"]);
            array_push($Datos, $r["materia11"]);
            array_push($Datos, $r["materia12"]);
            array_push($Datos, $r["materia13"]);
            array_push($Datos, $r["materia14"]);
            array_push($Datos, $r["materia15"]);
            array_push($Datos, $r["materia16"]);
            array_push($Datos, $r["materia17"]);
            array_push($Datos, $r["materia18"]);
            array_push($Datos, $r["materia19"]);
            array_push($Datos, $r["materia20"]);
            array_push($Datos, $r["materia21"]);
        }
        elseif($tabla=="premat_2bach_c"){
            array_push($Datos, $r["materia1"]);
            array_push($Datos, $r["materia2"]);
            array_push($Datos, $r["materia3"]);
            array_push($Datos, $r["materia4"]);
            array_push($Datos, $r["materia5"]);
            array_push($Datos, $r["materia6"]);
            array_push($Datos, $r["materia7"]);
            array_push($Datos, $r["materia8"]);
            array_push($Datos, $r["materia9"]);
            array_push($Datos, $r["materia10"]);
            array_push($Datos, $r["materia11"]);
            array_push($Datos, $r["materia12"]);
            array_push($Datos, $r["materia13"]);
            array_push($Datos, $r["materia14"]);
            array_push($Datos, $r["materia15"]);
            array_push($Datos, $r["materia16"]);
            array_push($Datos, $r["materia17"]);
            array_push($Datos, $r["materia18"]);
            array_push($Datos, $r["materia19"]);
            array_push($Datos, $r["materia20"]);
        }

        fputcsv($output, $Datos, ";");
                
    }

    fclose($output);
    exit();

}


