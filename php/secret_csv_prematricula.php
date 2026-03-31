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
    fputcsv($output, ["NIE", "ALUMNO", "N_DOCUMENTO", "ES_PASAPORTE", "FECHA_CADUCIDAD", "CADUCADO", "DIAS_HASTA_CADUCIDAD", "PAIS", "CURSO", "TURNO", "SUBIDA_ANVERSO_DNI", "SUBIDA_REVERSO_DNI", "SUBIDA_PASAPORTE", "SUBIDA_SEGURO_ESCOLAR"], ";");

    while($r=$res->fetch_array(MYSQLI_ASSOC)){
        if(substr(strtoupper($r["id_nie"]),0,1)== "P") continue;

        $Datos.= $r["id_nie"].";";
        $Datos.= ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";";
        $Datos.= $r["sexo"].";";

        if($tabla=="premat_2eso"){
            $Datos.= $r["curso_actual"].";";
            $Datos.= $r["grupo_curso_actual"].";";
            $Datos.= $r["prog_ling"].";";
            $Datos.= $r["materia2"].";";
            $Datos.= $r["materia1"].";";
            $Datos.= $r["materia3"].";";
            $Datos.= $r["materia4"].";";
            $Datos.= $r["materia5"].";";
            $Datos.= $r["materia6"].PHP_EOL;
        }
        elseif($tabla=="premat_3eso"){
            $Datos.= $r["curso_actual"].";";
            $Datos.= $r["grupo_curso_actual"].";";
            $Datos.= $r["prog_ling"].";";
            $Datos.= $r["materia2"].";";
            $Datos.= $r["materia1"].";";
            $Datos.= $r["materia3"].";";
            $Datos.= $r["materia4"].";";
            $Datos.= $r["materia5"].";";
            $Datos.= $r["materia6"].PHP_EOL;
        }
        elseif($tabla=="premat_4eso"){
            $Datos.= $r["curso_actual"].";";
            $Datos.= $r["grupo_curso_actual"].";";
            $Datos.= $r["prog_ling"].";";
            $Datos.= $r["materia1"].";";
            $Datos.= $r["materia2"].";";
            $Datos.= $r["materia3"].";";
            $Datos.= $r["materia4"].";";
            $Datos.= $r["materia5"].";";
            $Datos.= $r["materia6"].";";
            $Datos.= $r["materia7"].";";
            $Datos.= $r["materia8"].";";
            $Datos.= $r["materia9"].";";
            $Datos.= $r["materia10"].";";
            $Datos.= $r["materia11"].";";
            $Datos.= $r["materia12"].";";
            $Datos.= $r["materia13"].";";
            $Datos.= $r["materia14"].PHP_EOL;
        }
        elseif($tabla=="premat_3esodiv"){
            $Datos.= $r["curso_actual"].";";
            $Datos.= $r["grupo_curso_actual"].";";
            $Datos.= $r["materia1"].";";
            $Datos.= $r["materia2"].";";
            $Datos.= $r["materia3"].";";
            $Datos.= $r["materia4"].PHP_EOL;
        }
        elseif($tabla=="premat_4esodiv"){
            $Datos.= $r["curso_actual"].";";
            $Datos.= $r["grupo_curso_actual"].";";
            $Datos.= $r["materia1"].";";
            $Datos.= $r["materia2"].";";
            $Datos.= $r["materia3"].";";
            $Datos.= $r["materia4"].";";
            $Datos.= $r["materia5"].";";
            $Datos.= $r["materia6"].";";
            $Datos.= $r["materia7"].";";
            $Datos.= $r["materia8"].";";
            $Datos.= $r["materia9"].";";
            $Datos.= $r["materia10"].";";
            $Datos.= $r["materia11"].PHP_EOL;
        }
        elseif($tabla=="premat_1bach_h" || $tabla=="premat_1bach_c"){
            $Datos.= $r["modalidad"].";";
            $Datos.= $r["materia1"].";";
            $Datos.= $r["materia2"].";";
            $Datos.= $r["materia3"].";";
            $Datos.= $r["materia4"].";";
            $Datos.= $r["materia5"].";";
            $Datos.= $r["materia6"].";";
            $Datos.= $r["materia7"].";";
            $Datos.= $r["materia8"].";";
            $Datos.= $r["materia9"].";";
            $Datos.= $r["materia10"].";";
            $Datos.= $r["materia11"].";";
            $Datos.= $r["materia12"].";";
            $Datos.= $r["materia13"].";";
            $Datos.= $r["materia14"].";";
            $Datos.= $r["materia15"].";";
            $Datos.= $r["materia16"].";";
            $Datos.= $r["materia17"].";";
            $Datos.= $r["materia18"].";";
            $Datos.= $r["materia19"].";";
            $Datos.= $r["materia20"].";";
            $Datos.= $r["materia21"].PHP_EOL;
        }
        elseif($tabla=="premat_2bach_h"){
            $Datos.= $r["materia1"].";";
            $Datos.= $r["materia2"].";";
            $Datos.= $r["materia3"].";";
            $Datos.= $r["materia4"].";";
            $Datos.= $r["materia5"].";";
            $Datos.= $r["materia6"].";";
            $Datos.= $r["materia7"].";";
            $Datos.= $r["materia8"].";";
            $Datos.= $r["materia9"].";";
            $Datos.= $r["materia10"].";";
            $Datos.= $r["materia11"].";";
            $Datos.= $r["materia12"].";";
            $Datos.= $r["materia13"].";";
            $Datos.= $r["materia14"].";";
            $Datos.= $r["materia15"].";";
            $Datos.= $r["materia16"].";";
            $Datos.= $r["materia17"].";";
            $Datos.= $r["materia18"].";";
            $Datos.= $r["materia19"].";";
            $Datos.= $r["materia20"].";";
            $Datos.= $r["materia21"].PHP_EOL;
        }
        elseif($tabla=="premat_2bach_c"){
            $Datos.= $r["materia1"].";";
            $Datos.= $r["materia2"].";";
            $Datos.= $r["materia3"].";";
            $Datos.= $r["materia4"].";";
            $Datos.= $r["materia5"].";";
            $Datos.= $r["materia6"].";";
            $Datos.= $r["materia7"].";";
            $Datos.= $r["materia8"].";";
            $Datos.= $r["materia9"].";";
            $Datos.= $r["materia10"].";";
            $Datos.= $r["materia11"].";";
            $Datos.= $r["materia12"].";";
            $Datos.= $r["materia13"].";";
            $Datos.= $r["materia14"].";";
            $Datos.= $r["materia15"].";";
            $Datos.= $r["materia16"].";";
            $Datos.= $r["materia17"].";";
            $Datos.= $r["materia18"].";";
            $Datos.= $r["materia19"].";";
            $Datos.= $r["materia20"].PHP_EOL;
        }
                
    }

    echo $Datos;

}


