<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");

require_once(__DIR__.'/../../../php/tcpdf/config/tcpdf_config_alt.php');
require_once(__DIR__.'/../../../php/tcpdf/tcpdf.php');
require "mail.php";
ob_clean();

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
$bonificado=$_POST['bonificado'];
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
if($_POST["nombre_tarjeta"]!="") $ruta_tarjeta="../../../docs/".$id_nie."/tarjeta_sanitaria"."/ts_".$id_nie;
else $ruta_tarjeta="";
if($_POST["nombre_foto"]!="") $ruta_foto="../../../docs/fotos/".$id_nie.".jpg";
else $ruta_foto="";


if (strlen(trim($enfermedad_pasada))==0)$enfermedad_pasada="No";
if (strlen(trim($enfermedad))==0)$enfermedad="No";
if (strlen(trim($medicacion))==0)$medicacion="No";
if (strlen(trim($alergias))==0)$alergias="No";
if (strlen(trim($otros_datos))==0)$otros_datos="Ninguno";

//GENERA EL PDF 

class MYPDF extends TCPDF {

	//Page header
	public function Header() {
		// Logo
		//$image_file = '../recursos/logo_ccm.jpg';
		//$this->Image($image_file, 10, 10, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$image_file = '../recursos/mini_escudo.jpg';
		$this->Image($image_file, 140, 10, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

		$this->SetFont('helvetica', 'B', 14);
		$this->SetXY(0,10);
		$this->Cell(0,0,"DATOS RESIDENTE",0,0,'C',0,'',1,false,'T','T');
			
		$this->SetFont('helvetica', '', 8);
		// Title
		//$this->setCellHeightRatio(1.75);
		$encab = "<label><strong>IES Universidad Laboral</strong><br>Avda. Europa, 28<br>45003-TOLEDO<br>Tlf.:925 22 34 00<br>Fax:925 22 24 54</label>";
		$this->writeHTMLCell(0, 0, 160, 11, $encab, 0, 1, 0, true, 'C', true);
		//$this->Ln();
		//$this->writeHTMLCell(0, 0, '', '', '', 'B', 1, 0, true, 'L', true);
		
	}
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('IES Universidad Laboral');
$pdf->SetTitle('Impreso Transporte Escolar');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('ulaboral, PDF, secretaría, Toledo, Impreso Transporte Escolar');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
//$pdf->setFooterData();

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/spa.php')) {
	require_once(dirname(__FILE__).'/lang/spa.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

$pdf->setFontSubsetting(true);

$pdf->SetFont('dejavusans', '', 8, '', true);
$pdf->setFillColor(200);  //Relleno en gris
$pdf->AddPage();

$cabecera = <<<HTML1
<h4>Residente: $apellidos, $nombre</h4>
<h4>Residente Bonificado: $bonificado   Teléfono de Urgencias: $tlf_urgencias</h4>
HTML1;

$YInicio=30;

//$pdf->RoundedRect(82,$YInicio,45,15,2,'1111','','','');
$pdf->writeHTMLCell(0, 0, 40, $YInicio+2, $cabecera, 0, 1, false, true, '', true);

//Padding dentro de la celda del texto
$pdf->setCellPaddings(0,0,0,0);
//Interlineado
$pdf->setCellHeightRatio(1);

$YInicio+=10;
$XInicio=12;
$anchoLinea=$pdf->getPageWidth();
//$XInicioRotulo=17;

///////////////////////DATOS DEL INTERNO
$YInicio+=8;
$pdf->Line(10,$YInicio,$anchoLinea-10,$YInicio);
//$pdf->RoundedRect(10,$YInicio-1,185,30,2,'1111','','','');
//$pdf->SetXY($XInicioRotulo,$YInicio);
//$pdf->SetFont('dejavusans', 'B', 10, '', true);
//$pdf->Cell(0,0,"DATOS DEL INTERNO",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$aChar=$pdf->GetStringWidth("Z");
$pdf->Cell(0,0,"NIF/NIE/Pasaporte",0,0,'L',0,'',1,false,'','');
$pdf->SetX(35*$aChar);
$pdf->Cell(0,0,"Fecha Nacimiento",0,0,'L',0,'',1,false,'','');
$pdf->SetX(52*$aChar);
$pdf->Cell(0,0,"Edad",0,0,'L',0,'',1,false,'','');
$pdf->SetX(60*$aChar);
$pdf->Cell(0,0,"Nº Hermanos",0,0,'L',0,'',1,false,'','');
$pdf->SetX(75*$aChar);
$pdf->Cell(0,0,"Lugar",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,$nif_nie,0,0,'L',0,'',1,false,'T','T');
$pdf->SetX(35*$aChar);
$pdf->Cell(0,0,$fech_nac,0,0,'L',0,'',1,false,'T','T');
$pdf->SetX(52*$aChar+2);
$pdf->Cell(0,0,$edad,0,0,'L',0,'',1,false,'T','T');
$pdf->SetX(60*$aChar+8);
$pdf->Cell(0,0,$num_hermanos,0,0,'L',0,'',1,false,'T','T');
$pdf->SetX(75*$aChar+2);
$pdf->Cell(0,0,$lugar_hermanos,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Tlf.del Interno ",0,0,'L',0,'',1,false,'','');
$pdf->SetX(35*$aChar);
$pdf->Cell(0,0,"Nº de la S.S.",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,$tlf_alum,0,0,'L',0,'',1,false,'T','T');
$pdf->SetX(35*$aChar);
$pdf->Cell(0,0,$num_ss,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"E-mail del Interno",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,$email_alumno,0,0,'L',0,'',1,false,'T','T');


///////////////DOMICILIO DEL INTERNO
$YInicio+=4;
$pdf->Line(10,$YInicio,$anchoLinea-10,$YInicio);
/*$pdf->RoundedRect(10,$YInicio-1,185,25,2,'1111','','','');
$pdf->SetXY($XInicioRotulo,$YInicio);
$pdf->SetFont('dejavusans', 'B', 10, '', true);
$pdf->Cell(0,0,"DATOS DOMICILIO DEL INTERNO",0,0,'L',0,'',1,false,'','');*/

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Dirección",0,0,'L',0,'',1,false,'','');
$pdf->SetX(80*$aChar);
$pdf->Cell(0,0,"CP",0,0,'L',0,'',1,false,'','');


$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,$direccion,0,0,'L',0,'',1,false,'T','T');
$pdf->SetX(80*$aChar);
$pdf->Cell(0,0,$cp,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Localidad",0,0,'L',0,'',1,false,'','');
$pdf->SetX(50*$aChar);
$pdf->Cell(0,0,"Provincia",0,0,'L',0,'',1,false,'','');


$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,$localidad,0,0,'L',0,'',1,false,'T','T');
$pdf->SetX(50*$aChar);
$pdf->Cell(0,0,$provincia,0,0,'L',0,'',1,false,'T','T');



//////////////////// DATOS RELACIONADOS CON LOS ESTUDIOS
$YInicio+=4;
$pdf->Line(10,$YInicio,$anchoLinea-10,$YInicio);
/*$pdf->RoundedRect(10,$YInicio-1,185,50,2,'1111','','','');
$pdf->SetXY($XInicioRotulo,$YInicio);
$pdf->SetFont('dejavusans', 'B', 10, '', true);
$pdf->Cell(0,0,"DATOS RELACIONADOS CON LOS ESTUDIOS",0,0,'L',0,'',1,false,'','');*/

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Estudios",0,0,'L',0,'',1,false,'','');
$pdf->SetX(55*$aChar);
$pdf->Cell(0,0,"Tutor",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,$estudios,0,0,'L',0,'',1,false,'T','T');
$pdf->SetX(55*$aChar);
$pdf->Cell(0,0,$tutor,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Centro de Estudios",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,90,$centro_est,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Teléfono",0,0,'L',0,'',1,false,'','');
$pdf->SetX(25*$aChar);
$pdf->Cell(0,0,"Email",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,$tlf_centro_est,0,0,'L',0,'',1,false,'T','T');
$pdf->SetX(25*$aChar);
$pdf->Cell(0,0,$email_centro_est,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Centro de Procedencia",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,$centro_proc,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Teléfono",0,0,'L',0,'',1,false,'','');
$pdf->SetX(25*$aChar);
$pdf->Cell(0,0,"Email",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,$tlf_centro_proc,0,0,'L',0,'',1,false,'T','T');
$pdf->SetX(25*$aChar);
$pdf->Cell(0,0,$email_centro_proc,0,0,'L',0,'',1,false,'T','T');



/////////////////////  DATOS RELACIONADOS CON LOS PADRES/TUTORES LEGALES
$YInicio+=4;
$pdf->Line(10,$YInicio,$anchoLinea-10,$YInicio);
/*$pdf->RoundedRect(10,$YInicio-1,185,60,2,'1111','','','');
$pdf->SetXY($XInicioRotulo,$YInicio);
$pdf->SetFont('dejavusans', 'B', 10, '', true);
$pdf->Cell(0,0,"DATOS RELACIONADOS CON LOS PADRES/TUTORES LEGALES",0,0,'L',0,'',1,false,'','');*/

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Padre/Tutor legal",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,$tut1_nom,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Profesión",0,0,'L',0,'',1,false,'','');
$pdf->SetX(55*$aChar);
$pdf->Cell(0,0,"Estudios",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,$tut1_profesion,0,0,'L',0,'',1,false,'T','T');
$pdf->SetX(55*$aChar);
$pdf->Cell(0,0,$tut1_estudios,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Teléfono",0,0,'L',0,'',1,false,'','');
$pdf->SetX(25*$aChar);
$pdf->Cell(0,0,"Email",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,$tut1_telef,0,0,'L',0,'',1,false,'T','T');
$pdf->SetX(25*$aChar);
$pdf->Cell(0,0,$tut1_email,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Madre/Tutora legal",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,$tut2_nom,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Profesión",0,0,'L',0,'',1,false,'','');
$pdf->SetX(55*$aChar);
$pdf->Cell(0,0,"Estudios",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,$tut2_profesion,0,0,'L',0,'',1,false,'T','T');
$pdf->SetX(55*$aChar);
$pdf->Cell(0,0,$tut2_estudios,0,0,'L',0,'',1,false,'T','T');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Teléfono",0,0,'L',0,'',1,false,'','');
$pdf->SetX(25*$aChar);
$pdf->Cell(0,0,"Email",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,$tut2_telef,0,0,'L',0,'',1,false,'T','T');
$pdf->SetX(25*$aChar);
$pdf->Cell(0,0,$tut2_email,0,0,'L',0,'',1,false,'T','T');



//////////////// DATOS RELACIONADOS CON LA SALUD
$YInicio+=4;
$pdf->Line(10,$YInicio,$anchoLinea-10,$YInicio);
/*$pdf->RoundedRect(10,$YInicio-1,185,55,2,'1111','','','');
$pdf->SetXY($XInicioRotulo,$YInicio);
$pdf->SetFont('dejavusans', 'B', 10, '', true);
$pdf->Cell(0,0,"DATOS RELACIONADOS CON LA SALUD",0,0,'L',0,'',1,false,'','');*/

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Ha tenido una enfermedad importante",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
//$pdf->Cell(0,0,$enfermedad_pasada,0,0,'L',0,'',1,false,'T','T');
$pdf->MultiCell(0, 0, $enfermedad_pasada, 0, 'L');

$YInicio+=6;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Actualmente padece una enfermedad",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->MultiCell(0, 0, $enfermedad, 0, 'L');

$YInicio+=6;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Tiene medicación diaria",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->MultiCell(0, 0, $medicacion, 0, 'L');

$YInicio+=6;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Alergias a medicamentos y/o alimentos",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->MultiCell(0, 0, $alergias, 0, 'L');

$YInicio+=6;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Otros datos de interés",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->MultiCell(0, 0, $otros_datos, 0, 'L');

$YInicio+=6;
$pdf->Line(10,$YInicio,$anchoLinea-10,$YInicio);
$YInicio+=3;

// Agregar la imagen al PDF
if($_POST["nombre_tarjeta"]!=""){
	//if(file_exists($ruta_tarjeta)) $pdf->Image($ruta_tarjeta, 50, $YInicio, 100, 0, 'auto'); // Ajusta las coordenadas y dimensiones según tus necesidades
	if(file_exists($ruta_tarjeta)) $pdf->Image($ruta_tarjeta, 50, $YInicio, 100, 0, '','','T'); // Ajusta las coordenadas y dimensiones según tus necesidades

}
if($_POST["nombre_foto"]!=""){
	//if(file_exists($ruta_foto)) $pdf->Image($ruta_foto, 10, 10, 25, 35, 'auto');
	if(file_exists($ruta_foto)) $pdf->Image($ruta_foto, 10, 10, 25, 35, '','','T'); 
}

//GENERA EL ARCHIVO NUEVO
$nombre_fichero=recortarSustituirYObtener4Caracteres($apellidos).", ".recortarSustituirYObtener4Caracteres($nombre).".pdf";
$adjunto=$pdf->Output('', 'S');
$mail->addAddress('jjgp46@educastillalamancha.es', 'Jefe Residencia');
$mail->Subject="Formulario de ". $apellidos.", ".$nombre;
$mail->Body="Envío automático generado desde la plataforma. Interno: ". $apellidos.", ".$nombre;
$mail->addStringAttachment($adjunto,$nombre_fichero,"base64","application/pdf");
$mail->send();
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=" . $nombre_fichero);
$pdf->Output($nombre_fichero, 'I');


//FIN GENERA PDF


