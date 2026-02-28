<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$error="";
$Datos="";

include("conexion.php");
if ($mysqli->errno>0) $error="Error en servidor.";


$tabla=$_POST["mat_csv"];
$curso=$_POST["curso_csv_mat"];

$res=$mysqli->query("select * from $tabla where curso='$curso' order by apellidos,nombre");

if ($res->num_rows==0){
    $error="No hay prematrículas.";
}

$Name = $tabla.'.csv';
$FileName = "./$Name";

if($tabla=="mat_1eso") $Datos='NIE;ALUMNO;SEXO;CURSO_ANTERIOR;GRUPO;CONSOLIDA_PREMAT;PROGRAMA_LING;REL/VAL_ETICOS;PRIMER_IDIOMA;OPT1;OPT2;OPT3;OPT4'.PHP_EOL;
elseif($tabla=="mat_2eso") $Datos='NIE;ALUMNO;SEXO;CURSO_ANTERIOR;GRUPO;CONSOLIDA_PREMAT;PROGRAMA_LING;REL/VAL_ETICOS;PRIMER_IDIOMA;OPT1;OPT2;OPT3;OPT4;OPT5'.PHP_EOL;
elseif($tabla=="mat_3eso") $Datos='NIE;ALUMNO;SEXO;CURSO_ANTERIOR;GRUPO;CONSOLIDA_PREMAT;PROGRAMA_LING;MATEMATICAS;REL/VAL_ETICOS;PRIMER_IDIOMA;OPT1;OPT2;OPT3;OPT4;OPT5'.PHP_EOL;
elseif($tabla=="mat_4eso") $Datos='NIE;ALUMNO;SEXO;CURSO_ANTERIOR;GRUPO;CONSOLIDA_PREMAT;PROGRAMA_LING;MODALIDAD;PRIMER_IDIOMA;ESPEC_OBLIGAT;REL/VAL_ETICOS;TRONC_OPCION1;TRONC_OPCION2;OPT1;OPT2;OPT3;OPT4;OPT5;OPT6;OPT7;OPT8'.PHP_EOL;
elseif($tabla=="mat_2esopmar") $Datos='NIE;ALUMNO;SEXO;CURSO_ANTERIOR;GRUPO;CONSOLIDA_PREMAT;REL/VAL_ETICOS;OPT1;OPT2;OPT3;OPT4'.PHP_EOL;
elseif($tabla=="mat_3esopmar") $Datos='NIE;ALUMNO;SEXO;CURSO_ANTERIOR;GRUPO;CONSOLIDA_PREMAT;REL/VAL_ETICOS;OPT1;OPT2;OPT3;OPT4'.PHP_EOL;
elseif($tabla=="mat_1bach_hcs"){
    $Datos='NIE;ALUMNO;CONSOLIDA_PREMAT;ITINERARIO;PRIMER_IDIOMA;TRONC_GEN1;TRONC_GEN2;TRONC_OPCION;';
    $Datos.='ESPEC_ITIN1;ESPEC_ITIN2;ESPEC_ITIN3;ESPEC_ITIN4;ESPEC_ITIN5;ESPEC_ITIN6;ESPEC_ITIN7;ESPEC_ITIN8;';
    $Datos.='ESPEC_COM1;ESPEC_COM2;ESPEC_COM3;ESPEC_COM4;ESPEC_COM5;ESPEC_COM6;ESPEC_COM7'.PHP_EOL;
} 
elseif($tabla=="mat_1bach_c"){
    $Datos='NIE;ALUMNO;CONSOLIDA_PREMAT;ITINERARIO;PRIMER_IDIOMA;TRONC_GEN_OPC1;TRONC_GEN_OPC2;';
    $Datos.='ESPEC_ITIN1;ESPEC_ITIN2;ESPEC_ITIN3;ESPEC_ITIN4;ESPEC_ITIN5;ESPEC_ITIN6;ESPEC_ITIN7;ESPEC_ITIN8;';
    $Datos.='ESPEC_ITIN9;ESPEC_ITIN10;ESPEC_ITIN11;';
    $Datos.='ESPEC_COM1;ESPEC_COM2;ESPEC_COM3;ESPEC_COM4;ESPEC_COM5;ESPEC_COM6;ESPEC_COM7'.PHP_EOL;
} 
elseif($tabla=="mat_2bach_hcs"){
    $Datos='NIE;ALUMNO;CONSOLIDA_PREMAT;ITINERARIO;PRIMER_IDIOMA;TRONC_GEN;TRONC_OPC1;TRONC_OPC2;';
    $Datos.='ESPEC_ITIM_COM1;ESPEC_ITIM_COM2;ESPEC_ITIM_COM3;ESPEC_ITIM_COM4;ESPEC_ITIM_COM5;ESPEC_ITIM_COM6;';
    $Datos.='ESPEC_ITIM_COM7;ESPEC_ITIM_COM8;ESPEC_ITIM_COM9;ESPEC_ITIM_COM10;ESPEC_ITIM_COM11;ESPEC_ITIM_COM12;';
    $Datos.='ESPEC_ITIM_COM13;ESPEC_ITIM_COM14;ESPEC_ITIM_COM15;ESPEC_ITIM_COM16;ESPEC_ITIM_COM17;'.PHP_EOL;
} 
elseif($tabla=="mat_2bach_c"){
    $Datos='NIE;ALUMNO;CONSOLIDA_PREMAT;ITINERARIO;PRIMER_IDIOMA;TRONC_OPC1;TRONC_OPC2;';
    $Datos.='ESPEC_ITIM_COM1;ESPEC_ITIM_COM2;ESPEC_ITIM_COM3;ESPEC_ITIM_COM4;ESPEC_ITIM_COM5;ESPEC_ITIM_COM6;';
    $Datos.='ESPEC_ITIM_COM7;ESPEC_ITIM_COM8;ESPEC_ITIM_COM9;ESPEC_ITIM_COM10;ESPEC_ITIM_COM11;ESPEC_ITIM_COM12;';
    $Datos.='ESPEC_ITIM_COM13;ESPEC_ITIM_COM14;ESPEC_ITIM_COM15;ESPEC_ITIM_COM16;ESPEC_ITIM_COM17;'.PHP_EOL;
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
    if($tabla=="mat_1eso"){
        $Datos.= $r["id_nie"].";";
        $Datos.= ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";";
        $Datos.= $r["sexo"].";";
        $Datos.= $r["curso_anterior"].";";
        $Datos.= $r["grupo_curso_anterior"].";";
        $Datos.= $r["consolida_premat"].";";
        $Datos.= $r["prog_ling"].";";
        $Datos.= $r["rel_valores_et"].";";
        $Datos.= $r["1_lengua_extr"].";";
        $Datos.= $r["optativa1"].";";
        $Datos.= $r["optativa2"].";";
        $Datos.= $r["optativa3"].";";
        $Datos.= $r["optativa4"].PHP_EOL;
    }
    elseif($tabla=="mat_2eso"){
        $Datos.= $r["id_nie"].";";
        $Datos.= ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";";
        $Datos.= $r["sexo"].";";
        $Datos.= $r["curso_anterior"].";";
        $Datos.= $r["grupo_curso_anterior"].";";
        $Datos.= $r["consolida_premat"].";";
        $Datos.= $r["prog_ling"].";";
        $Datos.= $r["rel_valores_et"].";";
        $Datos.= $r["1_lengua_extr"].";";
        $Datos.= $r["optativa1"].";";
        $Datos.= $r["optativa2"].";";
        $Datos.= $r["optativa3"].";";
        $Datos.= $r["optativa4"].";";
        $Datos.= $r["optativa5"].PHP_EOL;
    }
    elseif($tabla=="mat_3eso"){
        $Datos.= $r["id_nie"].";";
        $Datos.= ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";";
        $Datos.= $r["sexo"].";";
        $Datos.= $r["curso_anterior"].";";
        $Datos.= $r["grupo_curso_anterior"].";";
        $Datos.= $r["consolida_premat"].";";
        $Datos.= $r["prog_ling"].";";
        $Datos.= $r["matematicas"].";";
        $Datos.= $r["rel_valores_et"].";";
        $Datos.= $r["1_lengua_extr"].";";
        $Datos.= $r["optativa1"].";";
        $Datos.= $r["optativa2"].";";
        $Datos.= $r["optativa3"].";";
        $Datos.= $r["optativa4"].";";
        $Datos.= $r["optativa5"].PHP_EOL;
    }
    elseif($tabla=="mat_4eso"){
        $Datos.= $r["id_nie"].";";
        $Datos.= ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";";
        $Datos.= $r["sexo"].";";
        $Datos.= $r["curso_anterior"].";";
        $Datos.= $r["grupo_curso_anterior"].";";
        $Datos.= $r["consolida_premat"].";";
        $Datos.= $r["prog_ling"].";";
        $Datos.= $r["modalidad"].";";
        $Datos.= $r["1_lengua_extr"].";";
        $Datos.= $r["espec_oblig"].";";
        $Datos.= $r["rel_valores_et"].";";
        $Datos.= $r["troncales_opcion1"].";";
        $Datos.= $r["troncales_opcion2"].";";
        $Datos.= $r["espec_opcion1"].";";
        $Datos.= $r["espec_opcion2"].";";
        $Datos.= $r["espec_opcion3"].";";
        $Datos.= $r["espec_opcion4"].";";
        $Datos.= $r["espec_opcion5"].";";
        $Datos.= $r["espec_opcion6"].";";
        $Datos.= $r["espec_opcion7"].";";
        $Datos.= $r["espec_opcion8"].PHP_EOL;
    }
    elseif($tabla=="mat_2esopmar"){
        $Datos.= $r["id_nie"].";";
        $Datos.= ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";";
        $Datos.= $r["sexo"].";";
        $Datos.= $r["curso_anterior"].";";
        $Datos.= $r["grupo_curso_anterior"].";";
        $Datos.= $r["consolida_premat"].";";
        $Datos.= $r["rel_valores_et"].";";
        $Datos.= $r["optativa1"].";";
        $Datos.= $r["optativa2"].";";
        $Datos.= $r["optativa3"].";";
        $Datos.= $r["optativa4"].PHP_EOL;
    }
    elseif($tabla=="mat_3esopmar"){
        $Datos.= $r["id_nie"].";";
        $Datos.= ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";";
        $Datos.= $r["sexo"].";";
        $Datos.= $r["curso_anterior"].";";
        $Datos.= $r["grupo_curso_anterior"].";";
        $Datos.= $r["consolida_premat"].";";
        $Datos.= $r["rel_valores_et"].";";
        $Datos.= $r["optativa1"].";";
        $Datos.= $r["optativa2"].";";
        $Datos.= $r["optativa3"].";";
        $Datos.= $r["optativa4"].PHP_EOL;
    }
	elseif($tabla=="mat_1bach_hcs"){
        $Datos.= $r["id_nie"].";";
        $Datos.= ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";";
        $Datos.= $r["consolida_premat"].";";
        $Datos.= $r["itinerario"].";";
        $Datos.= $r["primer_idioma"].";";
        $Datos.= $r["tronc_gen1"].";";
        $Datos.= $r["tronc_gen2"].";";
        $Datos.= $r["tronc_opcion"].";";
        $Datos.= $r["espec_itin1"].";";
        $Datos.= $r["espec_itin2"].";";
        $Datos.= $r["espec_itin3"].";";
        $Datos.= $r["espec_itin4"].";";
        $Datos.= $r["espec_itin5"].";";
        $Datos.= $r["espec_itin6"].";";
        $Datos.= $r["espec_itin7"].";";
        $Datos.= $r["espec_itin8"].";";
        $Datos.= $r["espec_com1"].";";
        $Datos.= $r["espec_com2"].";";
        $Datos.= $r["espec_com3"].";";
        $Datos.= $r["espec_com4"].";";
        $Datos.= $r["espec_com5"].";";
        $Datos.= $r["espec_com6"].";";
        $Datos.= $r["espec_com7"].PHP_EOL;
	}
	elseif($tabla=="mat_1bach_c"){
        $Datos.= $r["id_nie"].";";
        $Datos.= ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";";
        $Datos.= $r["consolida_premat"].";";
		$Datos.= $r["itinerario"].";";
        $Datos.= $r["primer_idioma"].";";
        $Datos.= $r["tronc_gen1"].";";
        $Datos.= $r["tronc_gen2"].";";
        $Datos.= $r["espec_itin1"].";";
        $Datos.= $r["espec_itin2"].";";
        $Datos.= $r["espec_itin3"].";";
        $Datos.= $r["espec_itin4"].";";
        $Datos.= $r["espec_itin5"].";";
        $Datos.= $r["espec_itin6"].";";
        $Datos.= $r["espec_itin7"].";";
        $Datos.= $r["espec_itin8"].";";
        $Datos.= $r["espec_itin9"].";";
        $Datos.= $r["espec_itin10"].";";
        $Datos.= $r["espec_itin11"].";";
        $Datos.= $r["espec_com1"].";";
        $Datos.= $r["espec_com2"].";";
        $Datos.= $r["espec_com3"].";";
        $Datos.= $r["espec_com4"].";";
        $Datos.= $r["espec_com5"].";";
        $Datos.= $r["espec_com6"].";";
        $Datos.= $r["espec_com7"].PHP_EOL;
	}
	elseif($tabla=="mat_2bach_hcs"){
        $Datos.= $r["id_nie"].";";
        $Datos.= ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";";
        $Datos.= $r["consolida_premat"].";";
		$Datos.= $r["itinerario"].";";
        $Datos.= $r["primer_idioma"].";";
        $Datos.= $r["tronc_gen"].";";
        $Datos.= $r["tronc_opc1"].";";
        $Datos.= $r["tronc_opc2"].";";
        $Datos.= $r["espec_itin_com1"].";";
        $Datos.= $r["espec_itin_com2"].";";
        $Datos.= $r["espec_itin_com3"].";";
        $Datos.= $r["espec_itin_com4"].";";
        $Datos.= $r["espec_itin_com5"].";";
        $Datos.= $r["espec_itin_com6"].";";
        $Datos.= $r["espec_itin_com7"].";";
        $Datos.= $r["espec_itin_com8"].";";
        $Datos.= $r["espec_itin_com9"].";";
        $Datos.= $r["espec_itin_com10"].";";
        $Datos.= $r["espec_itin_com11"].";";
        $Datos.= $r["espec_itin_com12"].";";
        $Datos.= $r["espec_itin_com13"].";";
        $Datos.= $r["espec_itin_com14"].";";
        $Datos.= $r["espec_itin_com15"].";";
        $Datos.= $r["espec_itin_com16"].";";
        $Datos.= $r["espec_itin_com17"].PHP_EOL;
	}
	elseif($tabla=="mat_2bach_c"){
        $Datos.= $r["id_nie"].";";
        $Datos.= ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";";
        $Datos.= $r["consolida_premat"].";";
		$Datos.= $r["itinerario"].";";
        $Datos.= $r["primer_idioma"].";";
        $Datos.= $r["tronc_opc1"].";";
        $Datos.= $r["tronc_opc2"].";";
        $Datos.= $r["espec_itin_com1"].";";
        $Datos.= $r["espec_itin_com2"].";";
        $Datos.= $r["espec_itin_com3"].";";
        $Datos.= $r["espec_itin_com4"].";";
        $Datos.= $r["espec_itin_com5"].";";
        $Datos.= $r["espec_itin_com6"].";";
        $Datos.= $r["espec_itin_com7"].";";
        $Datos.= $r["espec_itin_com8"].";";
        $Datos.= $r["espec_itin_com9"].";";
        $Datos.= $r["espec_itin_com10"].";";
        $Datos.= $r["espec_itin_com11"].";";
        $Datos.= $r["espec_itin_com12"].";";
        $Datos.= $r["espec_itin_com13"].";";
        $Datos.= $r["espec_itin_com14"].";";
        $Datos.= $r["espec_itin_com15"].";";
        $Datos.= $r["espec_itin_com16"].";";
        $Datos.= $r["espec_itin_com17"].";";
        $Datos.= $r["espec_itin_com18"].PHP_EOL;
	}
			
}

echo $Datos;

