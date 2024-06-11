<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$respuesta= array();
require_once(__DIR__.'/../../../php/tcpdf/config/tcpdf_config_alt.php');
require_once(__DIR__.'/../../../php/tcpdf/tcpdf.php');
require __DIR__."/../../../php/mail.php";
ob_clean();
include("../../../php/conexion.php");

if ($mysqli->errno>0) {
	$respuesta["status"]="servidor";
    exit(json_encode($respuesta));
}


function generaRegistro(){
    $minus="abcdefghijklmnopqrstuvwxyz";
    $nums="0123456789";
    $array=array("","","","","","","","");
    $registro="";
    $array[0]=substr($nums,mt_rand(0,strlen("mayus")-1),1);
    $array[1]=substr($minus,mt_rand(0,strlen("minus")-1),1);
    $array[2]=substr($nums,mt_rand(0,strlen("nums")-1),1);
    $array[3]=substr($minus,mt_rand(0,strlen("mayus")-1),1);
    $array[4]=substr($nums,mt_rand(0,strlen("minus")-1),1);
    $array[5]=substr($minus,mt_rand(0,strlen("nums")-1),1);
    $array[6]=substr($nums,mt_rand(0,strlen("mayus")-1),1);
    $array[7]=substr($minus,mt_rand(0,strlen("signos")-1),1);
    shuffle($array);
    return "iesulabto_reside_".date('dmY')."_".$array[0].$array[1].$array[2].$array[3].$array[4].$array[5].$array[6].$array[7];;    
}


function recortarSustituirYObtener4Caracteres($cadena) {
    // Recortar a 20 caracteres
    $cadenaRecortada = mb_substr($cadena, 0, 20);

    // Eliminar acentos y diéresis
    $cadenaSinAcentos = preg_replace('/[áäâàÁÄÂÀ]/u', 'a', $cadenaRecortada);
    $cadenaSinAcentos = preg_replace('/[éëêèÉËÊÈ]/u', 'e', $cadenaSinAcentos);
    $cadenaSinAcentos = preg_replace('/[íïîìÍÏÎÌ]/u', 'i', $cadenaSinAcentos);
    $cadenaSinAcentos = preg_replace('/[óöôòÓÖÔÒ]/u', 'o', $cadenaSinAcentos);
    $cadenaSinAcentos = preg_replace('/[úüûùÚÜÛÙ]/u', 'u', $cadenaSinAcentos);

    // Sustituir caracteres no alfanuméricos por guiones bajos
    $cadenaSustituida = preg_replace('/[^a-zA-Z0-9]/', '_', $cadenaSinAcentos);

    // Dividir la cadena en palabras
    $palabras = explode('_', $cadenaSustituida);

    // Obtener los 4 primeros caracteres de cada palabra
    $primerosCaracteres = array_map(function($palabra) {
        return mb_substr($palabra, 0, 5);
    }, $palabras);

    // Unir los resultados en una cadena
    $resultado = implode('_', $primerosCaracteres);

    return $resultado;
}
ob_start();
$email_jef_res="";
$nombre_centro_edu="";
$direccion_centro_edu="";
$cp_centro_edu="";
$localidad_centro_edu="";
$provincia_centro_edu="";
$tlf_centro_edu="";
$fax_centro_edu="";
$fianza_bonificados=0;
$fianza_no_bonificados=0;


$res=$mysqli->query("select * from config_centro");
while ($reg=$res->fetch_assoc()){
	$email_jef_res=$reg["email_jefe_residencia"];
	$nombre_centro_edu=$reg["centro"];
	$direccion_centro_edu=$reg["direccion_centro"];
	$cp_centro_edu=$reg["cp_centro"];
	$localidad_centro_edu=$reg["localidad_centro"];
	$provincia_centro_edu=$reg["provincia_centro"];
	$tlf_centro_edu=$reg["tlf_centro"];
	$fax_centro_edu=$reg["fax_centro"];
	$fianza_bonificados=$reg["residencia_fianza_bonificados"];
	$fianza_no_bonificados=$reg["residencia_fianza_no_bonificados"];
}
$res->free();

$anno_curso=$_POST['anno_curso'];
if ($_POST['bonificado']=='NO') {
	$bonificado=0;
	$fianza=$fianza_no_bonificados;
}
else {
	$bonificado=1;
	$fianza=$fianza_bonificados;
} 
$id_nie=$_POST['id_nie'];
$nombre=$_POST['nombre'];
$apellidos=$_POST['apellidos'];
$nif_nie=$_POST['nif_nie'];
$tlf_urgencias=$_POST['tlf_urgencias'];
$fech_nac=$_POST['fech_nac'];
$edad=$_POST['edad'];
$num_hermanos=$_POST['num_hermanos'];
$lugar_hermanos=$_POST['lugar_hermanos'];
$tlf_alum=$_POST['tlf_alum'];
$email_alumno=$_POST['email_alumno'];
$num_ss=$_POST['num_ss'];
$direccion=$_POST['direccion'];
$cp=$_POST['cp'];
$localidad=$_POST['localidad'];
$provincia=$_POST['provincia'];
$estudios=$_POST["estudios"];
$tutor=$_POST["tutor"];
$centro_est=$_POST["centro_est"];
$tlf_centro_est=$_POST["tlf_centro_est"];
$email_centro_est=$_POST["email_centro_est"];
$centro_proc=$_POST["centro_proc"];
$tlf_centro_proc=$_POST["tlf_centro_proc"];
$email_centro_proc=$_POST["email_centro_proc"];
$tut1_nom=$_POST["tut1_nom"];
$tut1_profesion=$_POST["tut1_profesion"];
$tut1_estudios=$_POST["tut1_estudios"];
$tut1_telef=$_POST["tut1_telef"];
$tut1_email=$_POST["tut1_email"];
$tut2_nom=$_POST["tut2_nom"];
$tut2_profesion=$_POST["tut2_profesion"];
$tut2_estudios=$_POST["tut2_estudios"];
$tut2_telef=$_POST["tut2_telef"];
$tut2_email=$_POST["tut2_email"];
$enfermedad_pasada=$_POST["enfermedad_pasada"];
$enfermedad=$_POST["enfermedad"];
$medicacion=$_POST["medicacion"];
$alergias=$_POST["alergias"];
$otros_datos=$_POST["otros_datos"];
/*if($_POST["nombre_tarjeta"]!="") $ruta_tarjeta=__DIR__."/../../../docs/".$id_nie."/tarjeta_sanitaria"."/ts_".$id_nie;
else $ruta_tarjeta="";
if($_POST["nombre_foto"]!="") $ruta_foto=__DIR__."/../../../docs/fotos/".$id_nie.".jpg";
else $ruta_foto="";*/
$ruta_tarjeta=__DIR__."/../../../docs/".$id_nie."/tarjeta_sanitaria"."/ts_".$id_nie.".jpeg";
$ruta_foto=__DIR__."/../../../docs/fotos/".$id_nie.".jpeg";
if (isset($_POST['iban']))$iban = trim($_POST['iban']);
if (isset($_POST['bic']))$bic = trim($_POST['bic']);

if (strlen(trim($enfermedad_pasada))==0)$enfermedad_pasada="No";
if (strlen(trim($enfermedad))==0)$enfermedad="No";
if (strlen(trim($medicacion))==0)$medicacion="No";
if (strlen(trim($alergias))==0)$alergias="No";
if (strlen(trim($otros_datos))==0)$otros_datos="Ninguno";

if (isset($_POST['firma'])){
	$imageData = urldecode($_POST['firma']);
	file_put_contents($firma, base64_decode(str_replace('data:image/png;base64,', '', $imageData)));
}

$fecha_registro=date('Y-m-d');
$registro=generaRegistro();
$repite_registro=true;
while ($repite_registro){
    $res=$mysqli->query("select * from residentes where registro='$registro'");
    if ($mysqli->errno>0){
		$respuesta["status"]="servidor";
    	exit(json_encode($respuesta));
	}
    if ($res->num_rows>0){
       $registro= generaRegistro(); 
    }
    else if ($res->num_rows==0){
        $repite_registro=false;
    }
    $res->free();
}
 if (!$mysqli->query("delete from residentes where id_nie='$id_nie' and curso='$anno_curso'")){
	$respuesta["status"]="db";
	exit(json_encode($respuesta));
 }

$mysqli->query("insert into residentes (id_nie,
                                        registro,
                                        fecha_registro,
										id_nif,
                                        curso,
										bonificado,
                                        apellidos,
                                        nombre,
                                        fecha_nac,
                                        edad,
                                        num_hermanos,
                                        lugar_hermanos,
										email,
										telef_alumno,
										direccion,
										cp,
										localidad,
										provincia,
										telef_urgencias,
										n_seg_soc,
										iban,
										bic,
										estudios,
										tutor,
										centro_estudios,
										cent_estud_tlf,
										cent_estud_email,
										centro_procedencia,
										cent_proc_tlf,
										cent_proc_email,
                                        tutor1,
										profesion_tut1,
										estudios_tut1,
                                        email_tutor1,
                                        tlf_tutor1,
                                        tutor2,
										profesion_tut2,
										estudios_tut2,
                                        email_tutor2,
                                        tlf_tutor2,
										fianza) 
                                        values ('$id_nie',
                                        '$registro',
                                        '$fecha_registro',
                                        '$nif_nie',
										'$anno_curso',
										'$bonificado',
										'$apellidos',
										'$nombre',
										'$fech_nac',
										'$edad',
										'$num_hermanos',
										'$lugar_hermanos',
										'$email_alumno',
										'$tlf_alum',
										'$direccion',
										'$cp',
										'$localidad',
										'$provincia',
										'$tlf_urgencias',
										'$num_ss',
										'$iban',
										'$bic',
										'$estudios',
										'$tutor',
										'$centro_est',
										'$tlf_centro_est',
										'$email_centro_est',
										'$centro_proc',
										'$tlf_centro_proc',
										'$email_centro_proc',
										'$tut1_nom',
										'$tut1_profesion',
										'$tut1_estudios',
										'$tut1_email',
										'$tut1_telef',
										'$tut2_nom',
										'$tut2_profesion',
										'$tut2_estudios',
										'$tut2_email',
										'$tut2_telef',
										'$fianza')");
if ($mysqli->errno>0){
	$respuesta["status"]="registro_erroneo ".$mysqli->errno;
    exit(json_encode($respuesta));
}


//GENERA EL PDF 

class MYPDF extends TCPDF {
	
	//Page header
	public function Header() {
		
		global $nombre_centro_edu, $direccion_centro_edu, $cp_centro_edu, $localidad_centro_edu, $tlf_centro_edu, $fax_centro_edu;
		
		// Logo
		//$image_file = __DIR__.'/../../../recursos/logo_ccm.jpg';
		//$this->Image($image_file, 10, 10, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$image_file = __DIR__.'/../../../recursos/mini_escudo.jpg';
		$this->Image($image_file, 140, 10, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

		$this->SetFont('helvetica', 'B', 14);
		$this->SetXY(0,10);
		$this->Cell(0,0,"DATOS RESIDENTE",0,0,'C',0,'',1,false,'T','T');
			
		$this->SetFont('helvetica', '', 8);
		// Title
		//$this->setCellHeightRatio(1.75);
		$encab = "<label><strong>" . $nombre_centro_edu . "</strong><br>" . $direccion_centro_edu . "<br>" . $cp_centro_edu . "-" . $localidad_centro_edu . "<br>Tlf.:" . $tlf_centro_edu . "<br>Fax:" . $fax_centro_edu . "</label>";
		$this->writeHTMLCell(0, 0, 160, 11, $encab, 0, 1, 0, true, 'C', true);
		//$this->Ln();
		//$this->writeHTMLCell(0, 0, '', '', '', 'B', 1, 0, true, 'L', true);
		
	}
}

// create new PDF document. Segeneran 2. Uno que no se guarda (datos de salud) y otro que se guarda
$pdf_salud = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf_salud->SetCreator(PDF_CREATOR);
$pdf_salud->SetAuthor('IES Universidad Laboral');
$pdf_salud->SetTitle('Impreso Residencia');
$pdf_salud->SetSubject('Residencia');
$pdf_salud->SetKeywords('ulaboral, PDF, residencia, Toledo, Impreso Residencia');

// set default header data
$pdf_salud->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
//$pdf_salud->setFooterData();

// set header and footer fonts
$pdf_salud->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//$pdf_salud->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf_salud->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf_salud->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf_salud->SetHeaderMargin(PDF_MARGIN_HEADER);
//$pdf_salud->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf_salud->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf_salud->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/spa.php')) {
	require_once(dirname(__FILE__).'/lang/spa.php');
	$pdf_salud->setLanguageArray($l);
}

// ---------------------------------------------------------

$pdf_salud->setFontSubsetting(true);

$pdf_salud->SetFont('dejavusans', '', 8, '', true);
$pdf_salud->setFillColor(200);  //Relleno en gris
$pdf_salud->AddPage();
if ($bonificado==1)$t_bonif="SÍ";
else $t_bonif="NO";
$cabecera = <<<HTML1
<h4>Residente: $apellidos, $nombre</h4>
<h4>Residente Bonificado: $t_bonif   Teléfono de Urgencias: $tlf_urgencias</h4>
HTML1;

$YInicio=30;

//$pdf_salud->RoundedRect(82,$YInicio,45,15,2,'1111','','','');
$pdf_salud->writeHTMLCell(0, 0, 40, $YInicio+2, $cabecera, 0, 1, false, true, '', true);

//Padding dentro de la celda del texto
$pdf_salud->setCellPaddings(0,0,0,0);
//Interlineado
$pdf_salud->setCellHeightRatio(1);

$YInicio+=10;
$XInicio=12;
$anchoLinea=$pdf_salud->getPageWidth();
//$XInicioRotulo=17;

///////////////////////DATOS DEL INTERNO
$YInicio+=8;
$pdf_salud->Line(10,$YInicio,$anchoLinea-10,$YInicio);
//$pdf_salud->RoundedRect(10,$YInicio-1,185,30,2,'1111','','','');
//$pdf_salud->SetXY($XInicioRotulo,$YInicio);
//$pdf_salud->SetFont('dejavusans', 'B', 10, '', true);
//$pdf_salud->Cell(0,0,"DATOS DEL INTERNO",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'U', 8, '', true);
$aChar=$pdf_salud->GetStringWidth("Z");
$pdf_salud->Cell(0,0,"NIF/NIE/Pasaporte",0,0,'L',0,'',1,false,'','');
$pdf_salud->SetX(35*$aChar);
$pdf_salud->Cell(0,0,"Fecha Nacimiento",0,0,'L',0,'',1,false,'','');
$pdf_salud->SetX(52*$aChar);
$pdf_salud->Cell(0,0,"Edad",0,0,'L',0,'',1,false,'','');
$pdf_salud->SetX(60*$aChar);
$pdf_salud->Cell(0,0,"Nº Hermanos",0,0,'L',0,'',1,false,'','');
$pdf_salud->SetX(75*$aChar);
$pdf_salud->Cell(0,0,"Lugar que ocupa",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 8, '', true);
$pdf_salud->Cell(0,0,$nif_nie,0,0,'L',0,'',1,false,'T','T');
$pdf_salud->SetX(35*$aChar);
$pdf_salud->Cell(0,0,$fech_nac,0,0,'L',0,'',1,false,'T','T');
$pdf_salud->SetX(52*$aChar+2);
$pdf_salud->Cell(0,0,$edad,0,0,'L',0,'',1,false,'T','T');
$pdf_salud->SetX(60*$aChar+8);
$pdf_salud->Cell(0,0,$num_hermanos,0,0,'L',0,'',1,false,'T','T');
$pdf_salud->SetX(75*$aChar+2);
$pdf_salud->Cell(0,0,$lugar_hermanos,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'U', 8, '', true);
$pdf_salud->Cell(0,0,"Tlf.del Interno ",0,0,'L',0,'',1,false,'','');
$pdf_salud->SetX(35*$aChar);
$pdf_salud->Cell(0,0,"Nº de la S.S.",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 8, '', true);
$pdf_salud->Cell(0,0,$tlf_alum,0,0,'L',0,'',1,false,'T','T');
$pdf_salud->SetX(35*$aChar);
$pdf_salud->Cell(0,0,$num_ss,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'U', 8, '', true);
$pdf_salud->Cell(0,0,"E-mail del Interno",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 8, '', true);
$pdf_salud->Cell(0,0,$email_alumno,0,0,'L',0,'',1,false,'T','T');


///////////////DOMICILIO DEL INTERNO
$YInicio+=4;
$pdf_salud->Line(10,$YInicio,$anchoLinea-10,$YInicio);
/*$pdf_salud->RoundedRect(10,$YInicio-1,185,25,2,'1111','','','');
$pdf_salud->SetXY($XInicioRotulo,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 10, '', true);
$pdf_salud->Cell(0,0,"DATOS DOMICILIO DEL INTERNO",0,0,'L',0,'',1,false,'','');*/

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'U', 8, '', true);
$pdf_salud->Cell(0,0,"Dirección",0,0,'L',0,'',1,false,'','');
$pdf_salud->SetX(80*$aChar);
$pdf_salud->Cell(0,0,"CP",0,0,'L',0,'',1,false,'','');


$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 8, '', true);
$pdf_salud->Cell(0,0,$direccion,0,0,'L',0,'',1,false,'T','T');
$pdf_salud->SetX(80*$aChar);
$pdf_salud->Cell(0,0,$cp,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'U', 8, '', true);
$pdf_salud->Cell(0,0,"Localidad",0,0,'L',0,'',1,false,'','');
$pdf_salud->SetX(50*$aChar);
$pdf_salud->Cell(0,0,"Provincia",0,0,'L',0,'',1,false,'','');


$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 8, '', true);
$pdf_salud->Cell(0,0,$localidad,0,0,'L',0,'',1,false,'T','T');
$pdf_salud->SetX(50*$aChar);
$pdf_salud->Cell(0,0,$provincia,0,0,'L',0,'',1,false,'T','T');



//////////////////// DATOS RELACIONADOS CON LOS ESTUDIOS
$YInicio+=4;
$pdf_salud->Line(10,$YInicio,$anchoLinea-10,$YInicio);
/*$pdf_salud->RoundedRect(10,$YInicio-1,185,50,2,'1111','','','');
$pdf_salud->SetXY($XInicioRotulo,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 10, '', true);
$pdf_salud->Cell(0,0,"DATOS RELACIONADOS CON LOS ESTUDIOS",0,0,'L',0,'',1,false,'','');*/

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'U', 8, '', true);
$pdf_salud->Cell(0,0,"Estudios",0,0,'L',0,'',1,false,'','');
$pdf_salud->SetX(55*$aChar);
$pdf_salud->Cell(0,0,"Tutor",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 8, '', true);
$pdf_salud->Cell(0,0,$estudios,0,0,'L',0,'',1,false,'T','T');
$pdf_salud->SetX(55*$aChar);
$pdf_salud->Cell(0,0,$tutor,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'U', 8, '', true);
$pdf_salud->Cell(0,0,"Centro de Estudios",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 8, '', true);
$pdf_salud->Cell(0,90,$centro_est,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'U', 8, '', true);
$pdf_salud->Cell(0,0,"Teléfono",0,0,'L',0,'',1,false,'','');
$pdf_salud->SetX(25*$aChar);
$pdf_salud->Cell(0,0,"Email",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 8, '', true);
$pdf_salud->Cell(0,0,$tlf_centro_est,0,0,'L',0,'',1,false,'T','T');
$pdf_salud->SetX(25*$aChar);
$pdf_salud->Cell(0,0,$email_centro_est,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'U', 8, '', true);
$pdf_salud->Cell(0,0,"Centro de Procedencia",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 8, '', true);
$pdf_salud->Cell(0,0,$centro_proc,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'U', 8, '', true);
$pdf_salud->Cell(0,0,"Teléfono",0,0,'L',0,'',1,false,'','');
$pdf_salud->SetX(25*$aChar);
$pdf_salud->Cell(0,0,"Email",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 8, '', true);
$pdf_salud->Cell(0,0,$tlf_centro_proc,0,0,'L',0,'',1,false,'T','T');
$pdf_salud->SetX(25*$aChar);
$pdf_salud->Cell(0,0,$email_centro_proc,0,0,'L',0,'',1,false,'T','T');



/////////////////////  DATOS RELACIONADOS CON LOS PADRES/TUTORES LEGALES
$YInicio+=4;
$pdf_salud->Line(10,$YInicio,$anchoLinea-10,$YInicio);
/*$pdf_salud->RoundedRect(10,$YInicio-1,185,60,2,'1111','','','');
$pdf_salud->SetXY($XInicioRotulo,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 10, '', true);
$pdf_salud->Cell(0,0,"DATOS RELACIONADOS CON LOS PADRES/TUTORES LEGALES",0,0,'L',0,'',1,false,'','');*/

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'U', 8, '', true);
$pdf_salud->Cell(0,0,"Padre/Tutor legal",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 8, '', true);
$pdf_salud->Cell(0,0,$tut1_nom,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'U', 8, '', true);
$pdf_salud->Cell(0,0,"Profesión",0,0,'L',0,'',1,false,'','');
$pdf_salud->SetX(55*$aChar);
$pdf_salud->Cell(0,0,"Estudios",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 8, '', true);
$pdf_salud->Cell(0,0,$tut1_profesion,0,0,'L',0,'',1,false,'T','T');
$pdf_salud->SetX(55*$aChar);
$pdf_salud->Cell(0,0,$tut1_estudios,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'U', 8, '', true);
$pdf_salud->Cell(0,0,"Teléfono",0,0,'L',0,'',1,false,'','');
$pdf_salud->SetX(25*$aChar);
$pdf_salud->Cell(0,0,"Email",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 8, '', true);
$pdf_salud->Cell(0,0,$tut1_telef,0,0,'L',0,'',1,false,'T','T');
$pdf_salud->SetX(25*$aChar);
$pdf_salud->Cell(0,0,$tut1_email,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'U', 8, '', true);
$pdf_salud->Cell(0,0,"Madre/Tutora legal",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 8, '', true);
$pdf_salud->Cell(0,0,$tut2_nom,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'U', 8, '', true);
$pdf_salud->Cell(0,0,"Profesión",0,0,'L',0,'',1,false,'','');
$pdf_salud->SetX(55*$aChar);
$pdf_salud->Cell(0,0,"Estudios",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 8, '', true);
$pdf_salud->Cell(0,0,$tut2_profesion,0,0,'L',0,'',1,false,'T','T');
$pdf_salud->SetX(55*$aChar);
$pdf_salud->Cell(0,0,$tut2_estudios,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'U', 8, '', true);
$pdf_salud->Cell(0,0,"Teléfono",0,0,'L',0,'',1,false,'','');
$pdf_salud->SetX(25*$aChar);
$pdf_salud->Cell(0,0,"Email",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 8, '', true);
$pdf_salud->Cell(0,0,$tut2_telef,0,0,'L',0,'',1,false,'T','T');
$pdf_salud->SetX(25*$aChar);
$pdf_salud->Cell(0,0,$tut2_email,0,0,'L',0,'',1,false,'T','T');


//Escribir el número de registro en el lateral izquierdo y en vertical
$pageHeight = 240;
// Obtener la altura del texto para centrarlo
$textHeight = $pdf_salud->GetStringHeight(0, "Nº registro: ".$registro);

// Calcular la posición Y para centrar el texto verticalmente
$YPosition = ($pageHeight - $textHeight) / 2;

// Guardar el estado de transformación actual
$pdf_salud->StartTransform();

// Rotar el texto 90 grados en sentido horario
$pdf_salud->Rotate(90, 0, $YPosition);

// Establecer la posición del texto
$pdf_salud->Text(8, $YPosition, "Nº registro: ".$registro);

// Restaurar el estado de transformación anterior
$pdf_salud->StopTransform();

/////////////////////////////////////////Hasta aquí son los dos iguales, por lo que se hace copia del de salud en el otro
$pdf= clone $pdf_salud;

//////////////// DATOS RELACIONADOS CON LA SALUD
$YInicio+=4;
$pdf_salud->Line(10,$YInicio,$anchoLinea-10,$YInicio);
/*$pdf_salud->RoundedRect(10,$YInicio-1,185,55,2,'1111','','','');
$pdf_salud->SetXY($XInicioRotulo,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 10, '', true);
$pdf_salud->Cell(0,0,"DATOS RELACIONADOS CON LA SALUD",0,0,'L',0,'',1,false,'','');*/

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'U', 8, '', true);
$pdf_salud->Cell(0,0,"Ha tenido una enfermedad importante",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 8, '', true);
//$pdf_salud->Cell(0,0,$enfermedad_pasada,0,0,'L',0,'',1,false,'T','T');
$pdf_salud->MultiCell(0, 0, $enfermedad_pasada, 0, 'L');

$YInicio+=6;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'U', 8, '', true);
$pdf_salud->Cell(0,0,"Actualmente padece una enfermedad",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 8, '', true);
$pdf_salud->MultiCell(0, 0, $enfermedad, 0, 'L');

$YInicio+=6;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'U', 8, '', true);
$pdf_salud->Cell(0,0,"Tiene medicación diaria",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 8, '', true);
$pdf_salud->MultiCell(0, 0, $medicacion, 0, 'L');

$YInicio+=6;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'U', 8, '', true);
$pdf_salud->Cell(0,0,"Alergias a medicamentos y/o alimentos",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 8, '', true);
$pdf_salud->MultiCell(0, 0, $alergias, 0, 'L');

$YInicio+=6;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'U', 8, '', true);
$pdf_salud->Cell(0,0,"Otros datos de interés",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf_salud->SetXY($XInicio,$YInicio);
$pdf_salud->SetFont('dejavusans', 'B', 8, '', true);
$pdf_salud->MultiCell(0, 0, $otros_datos, 0, 'L');

$YInicio+=6;
$pdf_salud->Line(10,$YInicio,$anchoLinea-10,$YInicio);
$YInicio+=3;



// Agregar la imagen al PDF
//if($_POST["nombre_tarjeta"]!=""){
	//if(file_exists($ruta_tarjeta)) $pdf_salud->Image($ruta_tarjeta, 50, $YInicio, 100, 0, 'auto'); // Ajusta las coordenadas y dimensiones según tus necesidades
	if(file_exists($ruta_tarjeta)){
		$pdf_salud->Image($ruta_tarjeta, 50, $YInicio, 100, 0, '','','T'); // Ajusta las coordenadas y dimensiones según tus necesidades
		$pdf->Image($ruta_tarjeta, 50, $YInicio, 100, 0, '','','T'); // Ajusta las coordenadas y dimensiones según tus necesidades
	} 

//}

//if($_POST["nombre_foto"]!=""){
	//if(file_exists($ruta_foto)) $pdf_salud->Image($ruta_foto, 10, 10, 25, 35, 'auto');
	if(file_exists($ruta_foto)){
		$pdf_salud->Image($ruta_foto, 10, 10, 25, 35, '','','T'); 
		$pdf->Image($ruta_foto, 10, 10, 25, 35, '','','T'); 
	}  
//}

//SI YA HAY ALGUNA INSCRIPCIÓN BORRA EL ARCHIVO
$dir = __DIR__."/../../../docs/".$id_nie."/residencia"."/".$anno_curso.'/';     
$handle = opendir($dir);
while ($file = readdir($handle)) {
	if (is_file($dir.$file)) unlink($dir.$file);
}
closedir($dir);

//GENERA EL ARCHIVO NUEVO
$nombre_fichero=recortarSustituirYObtener4Caracteres($apellidos).", ".recortarSustituirYObtener4Caracteres($nombre).".pdf";

if (strlen($email_jef_res)>0){
	$adjunto=$pdf_salud->Output('', 'S');
	$mail->addAddress($email_jef_res, 'Jefe Residencia');//jjgp46@educastillalamancha.es
	$mail->Subject="Formulario de ". $apellidos.", ".$nombre;
	$mail->Body="Envío automático generado desde la plataforma. Residente: NIE -> ". $id_nie."  Apellidos y nombre -> ".  $apellidos.", ".$nombre;
	$mail->addStringAttachment($adjunto,$nombre_fichero,"base64","application/pdf");
	$mail->send();
}

if(!is_dir(__DIR__."/../../../docs/".$id_nie."/residencia"."/".$anno_curso)) mkdir(__DIR__."/../../../docs/".$id_nie."/residencia"."/".$anno_curso,0777,true);
$ruta_pdf=__DIR__."/../../../docs/".$id_nie."/"."residencia/".$anno_curso."/". $registro.".pdf";
$pdf->Output($ruta_pdf, 'F');

$pdf_base64 = base64_encode($pdf_salud->Output($nombre_fichero, 'S'));

// Crear el array de respuesta JSON
$respuesta["pdf"] = $pdf_base64;

if($bonificado==0){
	//Genera orden SEPA si el residente es NO bonificado
	class MYPDF_sepa extends TCPDF {
		// Constructor
		public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false) {
			parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
		}
	
		//Page header
		public function Header() {}
	}
	
	// create new PDF document
	$pdf_sepa = new MYPDF_sepa('P', 'mm', 'A4', true, 'UTF-8', false);
	
	// set document information
	$pdf_sepa->SetCreator(PDF_CREATOR);
	$pdf_sepa->SetAuthor('IES Universidad Laboral');
	$pdf_sepa->SetTitle('Inscripción a Residencia_SEPA');
	$pdf_sepa->SetSubject('Residencia');
	$pdf_sepa->SetKeywords('ulaboral, PDF, residencia, Toledo, Inscripción residentes_SEPA');
	
	// set default monospaced font
	$pdf_sepa->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	
	// set margins
	$pdf_sepa->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT, PDF_MARGIN_HEADER);
	
	// set auto page breaks
	$pdf_sepa->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	
	// set image scale factor
	$pdf_sepa->setImageScale(PDF_IMAGE_SCALE_RATIO);
	
	// ---------------------------------------------------------
	
	$pdf_sepa->setFontSubsetting(true);
	
	$pdf_sepa->SetFont('dejavusans', '', 8, '', true);
	$pdf_sepa->setFillColor(200);  //Relleno en gris
	//Padding dentro de la celda del texto
	$pdf_sepa->setCellPaddings(0,0,0,0);
	//Interlineado
	$pdf_sepa->setCellHeightRatio(1.5);
	
	
	$pdf_sepa->AddPage();
	// get the current page break margin
	$bMargin = $pdf_sepa->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf_sepa->getAutoPageBreak();
	// disable auto-page-break
	$pdf_sepa->SetAutoPageBreak(false, 0);
	// set background image
	$pdf_sepa->Image("../recursos/sepa.jpg", 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf_sepa->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf_sepa->setPageMark();
	
	$pdf_sepa->SetXY(22,129);
	$pdf_sepa->Cell(0,0,$nombre . " " . $apellidos,0,0,'L',0,'',1,true,'T','T');
	$pdf_sepa->SetXY(22,139);
	$pdf_sepa->Cell(0,0,$direccion,0,0,'L',0,'',1,true,'T','T');
	$pdf_sepa->SetXY(22,148);
	$pdf_sepa->Cell(0,0,$cp." -" .$localidad." - ".$provincia,0,0,'L',0,'',1,true,'T','T');
	
	for ($i=0;$i<strlen($bic);$i++){
		$pdf_sepa->SetXY(22+$i*7.5,169);
		$pdf_sepa->Cell(0,0,substr($bic,$i,1),0,0,'L',0,'',1,true,'T','T');
	}
	for ($i=0;$i<strlen($iban);$i++){
		$pdf_sepa->SetXY(22+$i*6,184);
		$pdf_sepa->Cell(0,0,substr($iban,$i,1),0,0,'L',0,'',1,true,'T','T');
	}
	
	
	$fecha = new DateTime();
	
	// Crear un formateador de fecha para español
	$formateador = new IntlDateFormatter(
		'es_ES', 
		IntlDateFormatter::FULL, 
		IntlDateFormatter::NONE, 
		'Europe/Madrid', 
		IntlDateFormatter::GREGORIAN,
		"d 'de' MMMM 'del' y"
	);
	
	// Formatear la fecha
	$fechaFormateada = $formateador->format($fecha);
	
	$pdf_sepa->SetXY(25,207);
	$pdf_sepa->Cell(0,0,$localidad." , a " . $fechaFormateada,0,0,'L',0,'',1,true,'T','T');
	
	$pdf_sepa->Image($firma, 90, 210, 35, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
	
	$ruta_sepa=__DIR__."/../../../docs/".$id_nie."/residencia/sepa_". $id_nie_.".pdf";
	$pdf_sepa->Output($ruta_sepa, 'F');
}

$respuesta["status"]="ok";
header('Content-Type: application/json');
exit(json_encode($respuesta));


//FIN GENERA PDF


