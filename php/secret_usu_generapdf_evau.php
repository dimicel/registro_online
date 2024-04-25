<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");

require_once('tcpdf/config/tcpdf_config_alt.php');
require_once('tcpdf/tcpdf.php');

$id_nie=$_POST["id_down"];
$nombre=$_POST["nombre_down"];

// Ruta del directorio raíz que deseas comprimir
$directorio = "../docs/".$id_nie."/dni/";



class MYPDF extends TCPDF {

	//Page header
	public function Header() {
		
	}
}


// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('IES Universidad Laboral');
$pdf->SetTitle('PDF DNI/NIF');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('ulaboral, PDF, secretaría, Toledo, PDF DNI/NIF');

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

// -------------------------------------------------------------------------------------

$pdf->setFontSubsetting(true);

$pdf->SetFont('dejavusans', '', 8, '', true);
$pdf->setFillColor(200);  //Relleno en gris
$pdf->AddPage();

if (file_exists($directorio.$id_nie."-A.jpg")) $anverso=$directorio.$id_nie."-A.jpg";
elseif (file_exists($directorio.$id_nie."-A.jpeg")) $anverso=$directorio.$id_nie."-A.jpeg";
else $anverso="no";

if (file_exists($directorio.$id_nie."-R.jpg")) $reverso=$directorio.$id_nie."-R.jpg";
elseif (file_exists($directorio.$id_nie."-R.jpeg")) $reverso=$directorio.$id_nie."-R.jpeg";
else $reverso="no";


if ($anverso!="no"){
    $pdf->Image($anverso,15,35,300,200,'','','T');
}
if ($reverso!="no"){
    $pdf->Image($reverso,350,35,300,200,'','','T');
}
if ($anverso=="no"  && $reverso=="no"){
    $pdf->SetXY(40,35);
    $pdf->Cell(0,0,"No existe documento de identificación",0,0,'L',0,'',1,false,'','');
}

$pdf->Output($id_nie."_".$nombre, 'D');



