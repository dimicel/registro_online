<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$error="";
$Datos="";

include("conexion.php");
if ($mysqli->errno>0) $error="Error en servidor.";


$tabla=$_POST["premat_csv"];
$curso=$_POST["curso_csv"];

if ($tabla=="premat_1bach_lomloe") $consulta="select * from $tabla where curso='$curso' order by modalidad,apellidos,nombre";
else $consulta="select * from $tabla where curso='$curso' order by apellidos,nombre";

$res=$mysqli->query($consulta);

if ($res->num_rows==0){
    $error="No hay prematrículas.";
}

$Name = $tabla.'.csv';
$FileName = "./$Name";

if($tabla=="premat_2eso") $Datos='NIE;ALUMNO;SEXO;CURSO_ACTUAL;GRUPO;PROGRAMA_LING;REL/VAL_ETICOS;PRIMER_IDIOMA;OPT1;OPT2;OPT3;OPT4;OPT5'.PHP_EOL;
elseif($tabla=="premat_3eso") $Datos='NIE;ALUMNO;SEXO;CURSO_ACTUAL;GRUPO;PROGRAMA_LING;REL/AT.EDUC;PRIMER_IDIOMA;OPT1;OPT2;OPT3;OPT4'.PHP_EOL;
elseif($tabla=="premat_4eso") $Datos='NIE;ALUMNO;SEXO;CURSO_ACTUAL;GRUPO;PROGRAMA_LING;MODALIDAD;PRIMER_IDIOMA;ESPEC_OBLIGAT;REL/VAL_ETICOS;TRONC_OPCION1;TRONC_OPCION2;OPT1;OPT2;OPT3;OPT4;OPT5;OPT6;OPT7;OPT8'.PHP_EOL;
elseif($tabla=="premat_3esopmar") $Datos='NIE;ALUMNO;SEXO;CURSO_ACTUAL;GRUPO;REL/AT.EDUC;OPT1;OPT2;OPT3;OPT4'.PHP_EOL;
elseif($tabla=="premat_1bach_hcs"){
    $Datos='NIE;ALUMNO;SEXO;ITINERARIO;PRIMER_IDIOMA;TRONC_GEN1;TRONC_GEN2;TRONC_OPCION;';
    $Datos.='ESPEC_ITIN1;ESPEC_ITIN2;ESPEC_ITIN3;ESPEC_ITIN4;ESPEC_ITIN5;ESPEC_ITIN6;ESPEC_ITIN7;ESPEC_ITIN8;';
    $Datos.='ESPEC_COM1;ESPEC_COM2;ESPEC_COM3;ESPEC_COM4;ESPEC_COM5;ESPEC_COM6;ESPEC_COM7'.PHP_EOL;
} 
elseif($tabla=="premat_1bach_c"){
    $Datos='NIE;ALUMNO;SEXO;ITINERARIO;PRIMER_IDIOMA;TRONC_GEN_OPC1;TRONC_GEN_OPC2;';
    $Datos.='ESPEC_ITIN1;ESPEC_ITIN2;ESPEC_ITIN3;ESPEC_ITIN4;ESPEC_ITIN5;ESPEC_ITIN6;ESPEC_ITIN7;ESPEC_ITIN8;';
    $Datos.='ESPEC_ITIN9;ESPEC_ITIN10;ESPEC_ITIN11;';
    $Datos.='ESPEC_COM1;ESPEC_COM2;ESPEC_COM3;ESPEC_COM4;ESPEC_COM5;ESPEC_COM6;ESPEC_COM7'.PHP_EOL;
} 
elseif($tabla=="premat_1bach_lomloe"){
    $Datos='NIE;ALUMNO;SEXO;MODALIDAD;PRIMER_IDIOMA;RELIGION/AT_EDUC;OBLIGATORIA1;OBLIGATORIA2;OBLIGATORIA3;';
    $Datos.='OPTAT1;OPTAT2;OPTAT3;OPTAT4;OPTAT5;OPTAT6;OPTAT7;OPTAT8;OPTAT9;OPTAT10;OPTAT11;OPTAT12;OPTAT13;OPTAT14;OPTAT15;OPTAT16;OPTAT17'.PHP_EOL;
} 
elseif($tabla=="premat_2bach_hcs"){
    $Datos='NIE;ALUMNO;SEXO;ITINERARIO;PRIMER_IDIOMA;TRONC_GEN;TRONC_OPC1;TRONC_OPC2;';
    $Datos.='ESPEC_ITIM_COM1;ESPEC_ITIM_COM2;ESPEC_ITIM_COM3;ESPEC_ITIM_COM4;ESPEC_ITIM_COM5;ESPEC_ITIM_COM6;';
    $Datos.='ESPEC_ITIM_COM7;ESPEC_ITIM_COM8;ESPEC_ITIM_COM9;ESPEC_ITIM_COM10;ESPEC_ITIM_COM11;ESPEC_ITIM_COM12;';
    $Datos.='ESPEC_ITIM_COM13;ESPEC_ITIM_COM14;ESPEC_ITIM_COM15;ESPEC_ITIM_COM16;ESPEC_ITIM_COM17;'.PHP_EOL;
} 
elseif($tabla=="premat_2bach_c"){
    $Datos='NIE;ALUMNO;SEXO;ITINERARIO;PRIMER_IDIOMA;TRONC_OPC1;TRONC_OPC2;';
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
    if($tabla=="premat_2eso"){
        $Datos.=utf8_decode($r["id_nie"].";");
        $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
        $Datos.=utf8_decode($r["sexo"].";");
        $Datos.=utf8_decode($r["curso_actual"].";");
        $Datos.=utf8_decode($r["grupo_curso_actual"].";");
        $Datos.=utf8_decode($r["prog_ling"].";");
        $Datos.=utf8_decode($r["rel_valores_et"].";");
        $Datos.=utf8_decode($r["1_lengua_extr"].";");
        $Datos.=utf8_decode($r["optativa1"].";");
        $Datos.=utf8_decode($r["optativa2"].";");
        $Datos.=utf8_decode($r["optativa3"].";");
        $Datos.=utf8_decode($r["optativa4"].";");
        $Datos.=utf8_decode($r["optativa5"]).PHP_EOL;
    }
    elseif($tabla=="premat_3eso"){
        $Datos.=utf8_decode($r["id_nie"].";");
        $Datos.=utf8_decode(ucwords(strtolower($r["apellidos"])).", ".ucwords(strtolower($r["nombre"])).";");
        $Datos.=utf8_decode($r["sexo"].";");
        $Datos.=utf8_decode($r["curso_actual"].";");
        $Datos.=utf8_decode($r["grupo_curso_actual"].";");
        $Datos.=utf8_decode($r["prog_ling"].";");
        $Datos.=utf8_decode($r["rel_valores_et"].";");
        $Datos.=utf8_decode($r["1_lengua_extr"].";");
        $Datos.=utf8_decode($r["optativa1"].";");
        $Datos.=utf8_decode($r["optativa2"].";");
        $Datos.=utf8_decode($r["optativa3"].";");
        $Datos.=utf8_decode($r["optativa4"]).PHP_EOL;
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

