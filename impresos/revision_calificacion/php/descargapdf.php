<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
//ob_start();
//error_reporting(E_ALL & ~E_NOTICE);
//ini_set('display_errors',0);
//ini_set('log_errors',1);

require_once(__DIR__.'/../../../php/tcpdf/config/tcpdf_config_alt.php');
require_once(__DIR__.'/../../../php/tcpdf/tcpdf.php');
include("../../../php/conexion.php");
header('Content-type: application/pdf');


class MYPDF extends TCPDF {
	//Page header
	public function Header() {
		// Logo
		$image_file = '../../../recursos/logo_ccm.jpg';
		$this->Image($image_file, 10, 10, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$image_file = '../../../recursos/mini_escudo.jpg';
		$this->Image($image_file, 170, 10, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
				
		$this->SetFont('helvetica', '', 8);
		// Title
		$encab = "<label><strong>Consejería de Educación, Cultura y Deportes<br>IES Universidad Laboral</strong><br>Avda. Europa, 28 45003 TOLEDO<br>Tlf.:925 22 34 00 Fax:925 22 24 54 e-mail 45003796.ies@edu.jccm.es</label>";
		$this->writeHTMLCell(0, 0, 40, 11, $encab, 0, 1, 0, true, '', true);	
	}
}


if (!isset($_POST["referencia"])) {
    exit("Acceso denegado");
}

$ref=$_POST["referencia"];

$sol=$mysqli->query("select * from revision_calificacion where registro='$ref'");
$registro=$sol->fetch_array(MYSQLI_ASSOC);

$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$fecha_actual=$registro["fecha_registro"];
$fecha_firma="Toledo, a ".substr($fecha_actual,8,2)." de ".substr($fecha_actual,5,2)." de ".substr($fecha_actual,0,4);
$curso=$registro["curso"];

$usuario=$_POST['nombre'];

$id_nif=strtoupper($registro['id_nif']);
$tratamiento=strtoupper($registro['tratamiento']);
$nombre=strtoupper($registro['nombre']);
$tipo_doc=strtoupper($registro['tipo_doc']);
$numero_doc=strtoupper($registro['numero_doc']);
$domicilio=strtoupper($registro['domicilio']);
$telefono=strtoupper($registro['telefono']);
$poblacion=strtoupper($registro['poblacion']);
$cp=strtoupper($registro['cp']);
$provincia=strtoupper($registro['provincia']);
$ciclo_grado=strtoupper($registro['ciclo_grado']);
$ciclo_nombre=strtoupper($registro['ciclo_nombre']);
$modulo=strtoupper($registro["modulo"]);
$nota=strtoupper($registro["nota"]);
$motivos=$registro["motivos"];


$num_documento="";
if ($tipo_doc=="NIF"){
	$num_documento="NIF/NIE NÚMERO " . $numero_doc;
}
elseif ($tipo_doc=="PASS"){
	$num_documento="NÚMERO DE PASAPORTE " . $numero_doc;
}


// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('IES Universidad Laboral');
$pdf->SetTitle('');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('ulaboral, PDF, secretaría, Toledo');

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



//Padding dentro de la celda del texto
$pdf->setCellPaddings(0,0,0,0);
//Interlineado
$pdf->setCellHeightRatio(2);

$YInicio=60;
$XInicio=10;

$texto=<<<EOD
<h2 style="text-align:center"><b>SOLICITUD DE REVISIÓN DE LA CALIFICACIÓN</b></h2>
<br><br>
<b>$tratamiento $nombre</b>, con <b>$num_documento</b>, domicilio <b>$domicilio</b>, teléfono <b>$telefono</b>, población <b>$poblacion</b> C.P. <b>$cp</b> y provincia <b>$provincia</b>,
<br><br>
<b>EXPONE:</b><br>
1.- Que está cursando en el centro <b>IES UNIVERSIDAD LABORAL</b> Localidad <b>TOLEDO</b> Provincia de <b>TOLEDO</b> el ciclo formativo de grado <b>$ciclo_grado</b> denominado <b>$ciclo_nombre</b>.<br>
2.- Que ha obtenido una calificación final del módulo <b>$modulo</b> una nota de <b>$nota</b><br><br>
<b>SOLICITA:</b><br>
1.- Una revisión de dicha calificación.<br>
2.- Las razones expuestas para solicitar dicha revisión son las siguientes:<br>$razones<br><br><br>
<p style="text-align:center">$fecha_firma<br>Fdo. por usuario $id_nif - $usuario</p>
<p>Nº de registro: $ref</p>
EOD;


$pdf->SetXY($XInicio,$YInicio);
$pdf->writeHTMLCell(180,0,$XInicio,$YInicio,$texto,0,0,false,true,'',true);
$pdf->MultiCell(180,0,"JEFE/A DE ESTUDIOS DEL IES UNIVERSIDAD LABORAL DE TOLEDO",0,'L',0,1,10,280,true,0,true,false,0);

$style_qr = array(
    'border' => false,
    'padding' => 0,
    'fgcolor' => array(0,0,0),
    'bgcolor' => false
);

$pdf->write2DBarcode($ref."\t".$nombre."\tREVISION CALIFICACION", "PDF417", 10, 250, 0, 30, $style_qr, 'N');

$pdf->Output($ref.".pdf","I");

//ob_end_clean();