<?php

require_once('php/tcpdf/config/tcpdf_config_alt.php');
require_once('php/tcpdf/tcpdf.php');

$nombre=str_repeat('X', 25);
$apellidos=str_repeat('X', 40);
$direccion=str_repeat('X', 90);
$cp=str_repeat('X', 5);
$localidad=str_repeat('X', 35);
$provincia=str_repeat('X', 35);
$iban = str_repeat('X', 24);
$bic = str_repeat('X', 11);

class MYPDF extends TCPDF {
    // Constructor
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false) {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
    }

	//Page header
	public function Header() {}
}

// create new PDF document
$pdf_sepa = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// set document information
$pdf_sepa->SetCreator(PDF_CREATOR);
$pdf_sepa->SetAuthor('IES Universidad Laboral');
$pdf_sepa->SetTitle('Convalidaciones Centro Educativo y Ministerio');
$pdf_sepa->SetSubject('Secretaría');
$pdf_sepa->SetKeywords('ulaboral, PDF, secretaría, Toledo, Convalidaciones Centro Educativo y Ministerio');

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
$pdf_sepa->Image("sepa.jpg", 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
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

$pdf_sepa->Image("recursos/logo_ccm.jpg", 90, 210, 35, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=" . $nombre_fichero);
$pdf_sepa->Output($nombre_fichero, 'I');



