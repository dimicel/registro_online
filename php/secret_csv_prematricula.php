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

if(strpos($tabla_db,"premat_")>=0){
    if (strpos($tabla_db,"eso")) $tabla_db="premat_eso";
    else $tabla_db="premat_bach";
}

$consulta="select * from $tabla_db where curso='$curso' order by apellidos,nombre";

$res=$mysqli->query($consulta);

if ($res->num_rows==0){
    $error="No hay prematrículas.";
}

$Name = $tabla.'.csv';
$FileName = "./$Name";

if($tabla=="premat_2eso") $Datos='NIE;ALUMNO;SEXO;CURSO_ACTUAL;GRUPO;PROGRAMA_LING;REL/VAL_ETICOS;PRIMER_IDIOMA;OPT1;OPT2;OPT3;OPT4'.PHP_EOL;
elseif($tabla=="premat_3eso") $Datos='NIE;ALUMNO;SEXO;CURSO_ACTUAL;GRUPO;PROGRAMA_LING;REL/VAL_ETICOS;PRIMER_IDIOMA;OPT1;OPT2;OPT3;OPT4'.PHP_EOL;
elseif($tabla=="premat_4eso") $Datos='NIE;ALUMNO;SEXO;CURSO_ACTUAL;GRUPO;PROGRAMA_LING;MATEMATICAS;PRIMER_IDIOMA;REL/VAL_ETICOS;OPC_BLOQUE1;OPC_BLOQUE2_1;OPC_BLOQUE2_2;OPC_BLOQUE2_3;OPC_BLOQUE2_4;OPC_BLOQUE3_1;OPC_BLOQUE3_2;OPC_BLOQUE3_3;OPC_BLOQUE3_4;OPC_BLOQUE3_5;OPC_BLOQUE3_6;OPTATIVA1;OPTATIVA2;OPTATIVA3;OPTATIVA4;OPTATIVA5'.PHP_EOL;
elseif($tabla=="premat_3esodiv") $Datos='NIE;ALUMNO;SEXO;CURSO_ACTUAL;GRUPO;REL/AT.EDUC;OPTATIVA1;OPTATIVA2;OPTATIVA3'.PHP_EOL;
elseif($tabla=="premat_4esodiv") $Datos='NIE;ALUMNO;SEXO;CURSO_ACTUAL;GRUPO;REL/AT.EDUC;OPCION1;OPCION12;OPCION13;OPCION14;OPCION15;OPCION16;OPTATIVA1;OPTATIVA2;OPTATIVA3;OPTATIVA4;OPTATIVA5'.PHP_EOL;
elseif($tabla=="premat_1bach_h" || $tabla=="premat_1bach_c"){
    $Datos='NIE;ALUMNO;SEXO;ITINERARIO;MODALIDAD;PRIMER_IDIOMA;REL/AT.EDUC;OBLIGATORIA1;OBLIGATORIA2;OBLIGATORIA3;';
    $Datos.='OPTATIVA1;OPTATIVA2;OPTATIVA3;OPTATIVA4;OPTATIVA5;OPTATIVA6;OPTATIVA7;OPTATIVA8;';
    $Datos.='OPTATIVA9;OPTATIVA10;OPTATIVA11;OPTATIVA12;OPTATIVA13;OPTATIVA14;OPTATIVA15'.PHP_EOL;
} 
elseif($tabla=="premat_2bach_h"){
    $Datos='NIE;ALUMNO;SEXO;PRIMER_IDIOMA;MODALIDAD1;MODALIDAD2;MODALIDAD3;';
    $Datos.='OPTATIVA1;OPTATIVA2;OPTATIVA3;OPTATIVA4;OPTATIVA5;OPTATIVA6;OPTATIVA7;OPTATIVA8;';
    $Datos.='OPTATIVA9;OPTATIVA10;OPTATIVA11;OPTATIVA12;OPTATIVA13;OPTATIVA14;OPTATIVA15;OPTATIVA16;OPTATIVA17'.PHP_EOL;
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
        $Datos.=utf8_decode($r["optativa6"]).PHP_EOL;
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
        $Datos.=utf8_decode($r["optativa6"]).PHP_EOL;
    }
    elseif($tabla=="premat_4eso"){
        $Datos.=utf8_decode($r["id_nie"].";");
        $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
        $Datos.=utf8_decode($r["sexo"].";");
        $Datos.=utf8_decode($r["curso_actual"].";");
        $Datos.=utf8_decode($r["grupo_curso_actual"].";");
        $Datos.=utf8_decode($r["prog_ling"].";");
        $Datos.=utf8_decode($r["modalidad"].";");
        $Datos.=utf8_decode($r["1_lengua_extr"].";");
        $Datos.=utf8_decode($r["espec_oblig"].";");
        $Datos.=utf8_decode($r["rel_valores_et"].";");
        $Datos.=utf8_decode($r["troncales_opcion1"].";");
        $Datos.=utf8_decode($r["troncales_opcion2"].";");
        $Datos.=utf8_decode($r["espec_opcion1"].";");
        $Datos.=utf8_decode($r["espec_opcion2"].";");
        $Datos.=utf8_decode($r["espec_opcion3"].";");
        $Datos.=utf8_decode($r["espec_opcion4"].";");
        $Datos.=utf8_decode($r["espec_opcion5"].";");
        $Datos.=utf8_decode($r["espec_opcion6"].";");
        $Datos.=utf8_decode($r["espec_opcion7"].";");
        $Datos.=utf8_decode($r["espec_opcion8"]).PHP_EOL;
    }
    if($tabla=="premat_3esopmar"){
        $Datos.=utf8_decode($r["id_nie"].";");
        $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
        $Datos.=utf8_decode($r["sexo"].";");
        $Datos.=utf8_decode($r["curso_actual"].";");
        $Datos.=utf8_decode($r["grupo_curso_actual"].";");
        $Datos.=utf8_decode($r["rel_valores_et"].";");
        $Datos.=utf8_decode($r["optativa1"].";");
        $Datos.=utf8_decode($r["optativa2"].";");
        $Datos.=utf8_decode($r["optativa3"].";");
        $Datos.=utf8_decode($r["optativa4"]).PHP_EOL;
    }
	elseif($tabla=="premat_1bach_hcs"){
        $Datos.=utf8_decode($r["id_nie"].";");
        $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
        $Datos.=utf8_decode($r["sexo"].";");
        $Datos.=utf8_decode($r["itinerario"].";");
        $Datos.=utf8_decode($r["primer_idioma"].";");
        $Datos.=utf8_decode($r["tronc_gen1"].";");
        $Datos.=utf8_decode($r["tronc_gen2"].";");
        $Datos.=utf8_decode($r["tronc_opcion"].";");
        $Datos.=utf8_decode($r["espec_itin1"].";");
        $Datos.=utf8_decode($r["espec_itin2"].";");
        $Datos.=utf8_decode($r["espec_itin3"].";");
        $Datos.=utf8_decode($r["espec_itin4"].";");
        $Datos.=utf8_decode($r["espec_itin5"].";");
        $Datos.=utf8_decode($r["espec_itin6"].";");
        $Datos.=utf8_decode($r["espec_itin7"].";");
        $Datos.=utf8_decode($r["espec_itin8"].";");
        $Datos.=utf8_decode($r["espec_com1"].";");
        $Datos.=utf8_decode($r["espec_com2"].";");
        $Datos.=utf8_decode($r["espec_com3"].";");
        $Datos.=utf8_decode($r["espec_com4"].";");
        $Datos.=utf8_decode($r["espec_com5"].";");
        $Datos.=utf8_decode($r["espec_com6"].";");
        $Datos.=utf8_decode($r["espec_com7"]).PHP_EOL;
	}
	elseif($tabla=="premat_1bach_c"){
        $Datos.=utf8_decode($r["id_nie"].";");
        $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
        $Datos.=utf8_decode($r["sexo"].";");
		$Datos.=utf8_decode($r["itinerario"].";");
        $Datos.=utf8_decode($r["primer_idioma"].";");
        $Datos.=utf8_decode($r["tronc_gen1"].";");
        $Datos.=utf8_decode($r["tronc_gen2"].";");
        $Datos.=utf8_decode($r["espec_itin1"].";");
        $Datos.=utf8_decode($r["espec_itin2"].";");
        $Datos.=utf8_decode($r["espec_itin3"].";");
        $Datos.=utf8_decode($r["espec_itin4"].";");
        $Datos.=utf8_decode($r["espec_itin5"].";");
        $Datos.=utf8_decode($r["espec_itin6"].";");
        $Datos.=utf8_decode($r["espec_itin7"].";");
        $Datos.=utf8_decode($r["espec_itin8"].";");
        $Datos.=utf8_decode($r["espec_itin9"].";");
        $Datos.=utf8_decode($r["espec_itin10"].";");
        $Datos.=utf8_decode($r["espec_itin11"].";");
        $Datos.=utf8_decode($r["espec_com1"].";");
        $Datos.=utf8_decode($r["espec_com2"].";");
        $Datos.=utf8_decode($r["espec_com3"].";");
        $Datos.=utf8_decode($r["espec_com4"].";");
        $Datos.=utf8_decode($r["espec_com5"].";");
        $Datos.=utf8_decode($r["espec_com6"].";");
        $Datos.=utf8_decode($r["espec_com7"]).PHP_EOL;
	}
    elseif($tabla=="premat_1bach_lomloe"){
        $Datos.=utf8_decode($r["id_nie"].";");
        $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
        $Datos.=utf8_decode($r["sexo"].";");
		$Datos.=utf8_decode($r["modalidad"].";");
        $Datos.=utf8_decode($r["primer_idioma"].";");
        $Datos.=utf8_decode($r["religion"].";");
        $Datos.=utf8_decode($r["obligatoria1"].";");
        $Datos.=utf8_decode($r["obligatoria2"].";");
        $Datos.=utf8_decode($r["obligatoria3"].";");
        $Datos.=utf8_decode($r["optativa1"].";");
        $Datos.=utf8_decode($r["optativa2"].";");
        $Datos.=utf8_decode($r["optativa3"].";");
        $Datos.=utf8_decode($r["optativa4"].";");
        $Datos.=utf8_decode($r["optativa5"].";");
        $Datos.=utf8_decode($r["optativa6"].";");
        $Datos.=utf8_decode($r["optativa7"].";");
        $Datos.=utf8_decode($r["optativa8"].";");
        $Datos.=utf8_decode($r["optativa9"].";");
        $Datos.=utf8_decode($r["optativa10"].";");
        $Datos.=utf8_decode($r["optativa11"].";");
        $Datos.=utf8_decode($r["optativa12"].";");
        $Datos.=utf8_decode($r["optativa13"].";");
        $Datos.=utf8_decode($r["optativa14"].";");
        $Datos.=utf8_decode($r["optativa15"].";");
        $Datos.=utf8_decode($r["optativa16"].";");
        $Datos.=utf8_decode($r["optativa17"]).PHP_EOL;
	}
	elseif($tabla=="premat_2bach_hcs"){
        $Datos.=utf8_decode($r["id_nie"].";");
        $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
        $Datos.=utf8_decode($r["sexo"].";");
		$Datos.=utf8_decode($r["itinerario"].";");
        $Datos.=utf8_decode($r["primer_idioma"].";");
        $Datos.=utf8_decode($r["tronc_gen"].";");
        $Datos.=utf8_decode($r["tronc_opc1"].";");
        $Datos.=utf8_decode($r["tronc_opc2"].";");
        $Datos.=utf8_decode($r["espec_itin_com1"].";");
        $Datos.=utf8_decode($r["espec_itin_com2"].";");
        $Datos.=utf8_decode($r["espec_itin_com3"].";");
        $Datos.=utf8_decode($r["espec_itin_com4"].";");
        $Datos.=utf8_decode($r["espec_itin_com5"].";");
        $Datos.=utf8_decode($r["espec_itin_com6"].";");
        $Datos.=utf8_decode($r["espec_itin_com7"].";");
        $Datos.=utf8_decode($r["espec_itin_com8"].";");
        $Datos.=utf8_decode($r["espec_itin_com9"].";");
        $Datos.=utf8_decode($r["espec_itin_com10"].";");
        $Datos.=utf8_decode($r["espec_itin_com11"].";");
        $Datos.=utf8_decode($r["espec_itin_com12"].";");
        $Datos.=utf8_decode($r["espec_itin_com13"].";");
        $Datos.=utf8_decode($r["espec_itin_com14"].";");
        $Datos.=utf8_decode($r["espec_itin_com15"].";");
        $Datos.=utf8_decode($r["espec_itin_com16"].";");
        $Datos.=utf8_decode($r["espec_itin_com17"]).PHP_EOL;
	}
	elseif($tabla=="premat_2bach_c"){
        $Datos.=utf8_decode($r["id_nie"].";");
        $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
        $Datos.=utf8_decode($r["sexo"].";");
		$Datos.=utf8_decode($r["itinerario"].";");
        $Datos.=utf8_decode($r["primer_idioma"].";");
        $Datos.=utf8_decode($r["tronc_opc1"].";");
        $Datos.=utf8_decode($r["tronc_opc2"].";");
        $Datos.=utf8_decode($r["espec_itin_com1"].";");
        $Datos.=utf8_decode($r["espec_itin_com2"].";");
        $Datos.=utf8_decode($r["espec_itin_com3"].";");
        $Datos.=utf8_decode($r["espec_itin_com4"].";");
        $Datos.=utf8_decode($r["espec_itin_com5"].";");
        $Datos.=utf8_decode($r["espec_itin_com6"].";");
        $Datos.=utf8_decode($r["espec_itin_com7"].";");
        $Datos.=utf8_decode($r["espec_itin_com8"].";");
        $Datos.=utf8_decode($r["espec_itin_com9"].";");
        $Datos.=utf8_decode($r["espec_itin_com10"].";");
        $Datos.=utf8_decode($r["espec_itin_com11"].";");
        $Datos.=utf8_decode($r["espec_itin_com12"].";");
        $Datos.=utf8_decode($r["espec_itin_com13"].";");
        $Datos.=utf8_decode($r["espec_itin_com14"].";");
        $Datos.=utf8_decode($r["espec_itin_com15"].";");
        $Datos.=utf8_decode($r["espec_itin_com16"].";");
        $Datos.=utf8_decode($r["espec_itin_com17"].";");
        $Datos.=utf8_decode($r["espec_itin_com18"]).PHP_EOL;
	}
			
}

echo $Datos;

