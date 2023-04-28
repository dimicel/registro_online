<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$error="";
$Datos="";

include("conexion.php");
if ($mysqli->errno>0) $error="Error en servidor.";


$tabla=$_POST["premat_csv"];
$tabla_db=$tabla;
$curso=$_POST["curso_csv"];

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

if ($res->num_rows==0){
    $error="No hay prematrículas.";
}

$Name = $tabla.'.csv';
$FileName = "./$Name";

if($tabla=="premat_2eso") $Datos='NIE;ALUMNO;SEXO;CURSO_ACTUAL;GRUPO;PROGRAMA_LING;REL/AT_EDUC;PRIMER_IDIOMA;OPT1;OPT2;OPT3;OPT4'.PHP_EOL;
elseif($tabla=="premat_3eso") $Datos='NIE;ALUMNO;SEXO;CURSO_ACTUAL;GRUPO;PROGRAMA_LING;REL/AT_EDUC;PRIMER_IDIOMA;OPT1;OPT2;OPT3;OPT4'.PHP_EOL;
elseif($tabla=="premat_4eso") $Datos='NIE;ALUMNO;SEXO;CURSO_ACTUAL;GRUPO;PROGRAMA_LING;PRIMER_IDIOMA;REL/AT_EDUC;MATEMATICAS;OPC_BLOQUE1;OPC_BLOQUE2_1;OPC_BLOQUE2_2;OPC_BLOQUE2_3;OPC_BLOQUE2_4;OPC_BLOQUE3_1;OPC_BLOQUE3_2;OPC_BLOQUE3_3;OPC_BLOQUE3_4;OPC_BLOQUE3_5;OPC_BLOQUE3_6;OPTATIVA1;OPTATIVA2;OPTATIVA3;OPTATIVA4;OPTATIVA5'.PHP_EOL;
elseif($tabla=="premat_3esodiv") $Datos='NIE;ALUMNO;SEXO;CURSO_ACTUAL;GRUPO;REL/AT.EDUC;OPTATIVA1;OPTATIVA2;OPTATIVA3'.PHP_EOL;
elseif($tabla=="premat_4esodiv") $Datos='NIE;ALUMNO;SEXO;CURSO_ACTUAL;GRUPO;REL/AT.EDUC;OPCION1;OPCION2;OPCION3;OPCION4;OPCION5;OPCION6;OPTATIVA1;OPTATIVA2;OPTATIVA3;OPTATIVA4;OPTATIVA5'.PHP_EOL;
elseif($tabla=="premat_1bach_h" || $tabla=="premat_1bach_c"){
    $Datos='NIE;ALUMNO;SEXO;MODALIDAD;PRIMER_IDIOMA;REL/AT_EDUC;OBLIGATORIA1;OBLIGATORIA2;OBLIGATORIA3;';
    $Datos.='OPTATIVA1;OPTATIVA2;OPTATIVA3;OPTATIVA4;OPTATIVA5;OPTATIVA6;OPTATIVA7;OPTATIVA8;';
    $Datos.='OPTATIVA9;OPTATIVA10;OPTATIVA11;OPTATIVA12;OPTATIVA13;OPTATIVA14;OPTATIVA15'.PHP_EOL;
} 
elseif($tabla=="premat_2bach_h"){
    $Datos='NIE;ALUMNO;SEXO;PRIMER_IDIOMA;MODALIDAD1;MODALIDAD2;MODALIDAD3;';
    $Datos.='OPTATIVA1;OPTATIVA2;OPTATIVA3;OPTATIVA4;OPTATIVA5;OPTATIVA6;OPTATIVA7;OPTATIVA8;';
    $Datos.='OPTATIVA9;OPTATIVA10;OPTATIVA11;OPTATIVA12;OPTATIVA13;OPTATIVA14;OPTATIVA15;OPTATIVA16'.PHP_EOL;
} 
elseif($tabla=="premat_2bach_c"){
    $Datos='NIE;ALUMNO;SEXO;PRIMER_IDIOMA;MODALIDAD1;MODALIDAD2;MODALIDAD3;';
    $Datos.='OPTATIVA1;OPTATIVA2;OPTATIVA3;OPTATIVA4;OPTATIVA5;OPTATIVA6;OPTATIVA7;OPTATIVA8;';
    $Datos.='OPTATIVA9;OPTATIVA10;OPTATIVA11;OPTATIVA12;OPTATIVA13;OPTATIVA14;OPTATIVA15'.PHP_EOL;
}

header('Expires: 0');
header('Cache-control: private');
header('Content-Type: application/x-octet-stream;charset=utf-8'); // Archivo de Excel
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Content-Description: File Transfer');
header('Last-Modified: '.date('D, d M Y H:i:s'));
header('Content-Disposition: attachment; filename="'.$Name.'"');
header("Content-Transfer-Encoding: binary");

if ($error!="") {
    echo $error;
    exit();
}

while($r=$res->fetch_array(MYSQLI_ASSOC)){
    if(substr(strtoupper($r["id_nie"]),0,1)== "P") continue;
    if($tabla=="premat_2eso"){
        $Datos.=utf8_decode($r["id_nie"].";");
        $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
        $Datos.=utf8_decode($r["sexo"].";");
        $Datos.=utf8_decode($r["curso_actual"].";");
        $Datos.=utf8_decode($r["grupo_curso_actual"].";");
        $Datos.=utf8_decode($r["prog_ling"].";");
        $Datos.=utf8_decode($r["materia2"].";");
        $Datos.=utf8_decode($r["materia1"].";");
        $Datos.=utf8_decode($r["materia3"].";");
        $Datos.=utf8_decode($r["materia4"].";");
        $Datos.=utf8_decode($r["materia5"].";");
        $Datos.=utf8_decode($r["materia6"]).PHP_EOL;
    }
    elseif($tabla=="premat_3eso"){
        $Datos.=utf8_decode($r["id_nie"].";");
        $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
        $Datos.=utf8_decode($r["sexo"].";");
        $Datos.=utf8_decode($r["curso_actual"].";");
        $Datos.=utf8_decode($r["grupo_curso_actual"].";");
        $Datos.=utf8_decode($r["prog_ling"].";");
        $Datos.=utf8_decode($r["materia2"].";");
        $Datos.=utf8_decode($r["materia1"].";");
        $Datos.=utf8_decode($r["materia3"].";");
        $Datos.=utf8_decode($r["materia4"].";");
        $Datos.=utf8_decode($r["materia5"].";");
        $Datos.=utf8_decode($r["materia6"]).PHP_EOL;
    }
    elseif($tabla=="premat_4eso"){
        $Datos.=utf8_decode($r["id_nie"].";");
        $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
        $Datos.=utf8_decode($r["sexo"].";");
        $Datos.=utf8_decode($r["curso_actual"].";");
        $Datos.=utf8_decode($r["grupo_curso_actual"].";");
        $Datos.=utf8_decode($r["prog_ling"].";");
        $Datos.=utf8_decode($r["materia1"].";");
        $Datos.=utf8_decode($r["materia2"].";");
        $Datos.=utf8_decode($r["materia3"].";");
        $Datos.=utf8_decode($r["materia4"].";");
        $Datos.=utf8_decode($r["materia5"].";");
        $Datos.=utf8_decode($r["materia6"].";");
        $Datos.=utf8_decode($r["materia7"].";");
        $Datos.=utf8_decode($r["materia8"].";");
        $Datos.=utf8_decode($r["materia9"].";");
        $Datos.=utf8_decode($r["materia10"].";");
        $Datos.=utf8_decode($r["materia11"].";");
        $Datos.=utf8_decode($r["materia12"].";");
        $Datos.=utf8_decode($r["materia13"].";");
        $Datos.=utf8_decode($r["materia14"].";");
        $Datos.=utf8_decode($r["materia15"].";");
        $Datos.=utf8_decode($r["materia16"].";");
        $Datos.=utf8_decode($r["materia17"].";");
        $Datos.=utf8_decode($r["materia18"].";");
        $Datos.=utf8_decode($r["materia19"]).PHP_EOL;
    }
    elseif($tabla=="premat_3esodiv"){
        $Datos.=utf8_decode($r["id_nie"].";");
        $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
        $Datos.=utf8_decode($r["sexo"].";");
        $Datos.=utf8_decode($r["curso_actual"].";");
        $Datos.=utf8_decode($r["grupo_curso_actual"].";");
        $Datos.=utf8_decode($r["materia1"].";");
        $Datos.=utf8_decode($r["materia2"].";");
        $Datos.=utf8_decode($r["materia3"].";");
        $Datos.=utf8_decode($r["materia4"]).PHP_EOL;
    }
    elseif($tabla=="premat_4esodiv"){
        $Datos.=utf8_decode($r["id_nie"].";");
        $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
        $Datos.=utf8_decode($r["sexo"].";");
        $Datos.=utf8_decode($r["curso_actual"].";");
        $Datos.=utf8_decode($r["grupo_curso_actual"].";");
        $Datos.=utf8_decode($r["materia1"].";");
        $Datos.=utf8_decode($r["materia2"].";");
        $Datos.=utf8_decode($r["materia3"].";");
        $Datos.=utf8_decode($r["materia4"].";");
        $Datos.=utf8_decode($r["materia5"].";");
        $Datos.=utf8_decode($r["materia6"].";");
        $Datos.=utf8_decode($r["materia7"].";");
        $Datos.=utf8_decode($r["materia8"].";");
        $Datos.=utf8_decode($r["materia9"].";");
        $Datos.=utf8_decode($r["materia10"].";");
        $Datos.=utf8_decode($r["materia11"].";");
        $Datos.=utf8_decode($r["materia12"]).PHP_EOL;
    }
	elseif($tabla=="premat_1bach_h" || $tabla=="premat_1bach_c"){
        $Datos.=utf8_decode($r["id_nie"].";");
        $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
        $Datos.=utf8_decode($r["sexo"].";");
        $Datos.=utf8_decode($r["modalidad"].";");
        $Datos.=utf8_decode($r["materia1"].";");
        $Datos.=utf8_decode($r["materia2"].";");
        $Datos.=utf8_decode($r["materia3"].";");
        $Datos.=utf8_decode($r["materia4"].";");
        $Datos.=utf8_decode($r["materia5"].";");
        $Datos.=utf8_decode($r["materia6"].";");
        $Datos.=utf8_decode($r["materia7"].";");
        $Datos.=utf8_decode($r["materia8"].";");
        $Datos.=utf8_decode($r["materia9"].";");
        $Datos.=utf8_decode($r["materia10"].";");
        $Datos.=utf8_decode($r["materia11"].";");
        $Datos.=utf8_decode($r["materia12"].";");
        $Datos.=utf8_decode($r["materia13"].";");
        $Datos.=utf8_decode($r["materia14"].";");
        $Datos.=utf8_decode($r["materia15"].";");
        $Datos.=utf8_decode($r["materia16"].";");
        $Datos.=utf8_decode($r["materia17"].";");
        $Datos.=utf8_decode($r["materia18"].";");
        $Datos.=utf8_decode($r["materia19"].";");
        $Datos.=utf8_decode($r["materia20"]).PHP_EOL;
	}
	elseif($tabla=="premat_2bach_h"){
        $Datos.=utf8_decode($r["id_nie"].";");
        $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
        $Datos.=utf8_decode($r["sexo"].";");
		$Datos.=utf8_decode($r["materia1"].";");
        $Datos.=utf8_decode($r["materia2"].";");
        $Datos.=utf8_decode($r["materia3"].";");
        $Datos.=utf8_decode($r["materia4"].";");
        $Datos.=utf8_decode($r["materia5"].";");
        $Datos.=utf8_decode($r["materia6"].";");
        $Datos.=utf8_decode($r["materia7"].";");
        $Datos.=utf8_decode($r["materia8"].";");
        $Datos.=utf8_decode($r["materia9"].";");
        $Datos.=utf8_decode($r["materia10"].";");
        $Datos.=utf8_decode($r["materia11"].";");
        $Datos.=utf8_decode($r["materia12"].";");
        $Datos.=utf8_decode($r["materia13"].";");
        $Datos.=utf8_decode($r["materia14"].";");
        $Datos.=utf8_decode($r["materia15"].";");
        $Datos.=utf8_decode($r["materia16"].";");
        $Datos.=utf8_decode($r["materia17"].";");
        $Datos.=utf8_decode($r["materia18"].";");
        $Datos.=utf8_decode($r["materia19"].";");
        $Datos.=utf8_decode($r["materia20"]).PHP_EOL;
	}
	elseif($tabla=="premat_2bach_c"){
        $Datos.=utf8_decode($r["id_nie"].";");
        $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
        $Datos.=utf8_decode($r["sexo"].";");
		$Datos.=utf8_decode($r["materia1"].";");
        $Datos.=utf8_decode($r["materia2"].";");
        $Datos.=utf8_decode($r["materia3"].";");
        $Datos.=utf8_decode($r["materia4"].";");
        $Datos.=utf8_decode($r["materia5"].";");
        $Datos.=utf8_decode($r["materia6"].";");
        $Datos.=utf8_decode($r["materia7"].";");
        $Datos.=utf8_decode($r["materia8"].";");
        $Datos.=utf8_decode($r["materia9"].";");
        $Datos.=utf8_decode($r["materia10"].";");
        $Datos.=utf8_decode($r["materia11"].";");
        $Datos.=utf8_decode($r["materia12"].";");
        $Datos.=utf8_decode($r["materia13"].";");
        $Datos.=utf8_decode($r["materia14"].";");
        $Datos.=utf8_decode($r["materia15"].";");
        $Datos.=utf8_decode($r["materia16"].";");
        $Datos.=utf8_decode($r["materia17"].";");
        $Datos.=utf8_decode($r["materia18"].";");
        $Datos.=utf8_decode($r["materia19"]).PHP_EOL;
	}
			
}

echo $Datos;

