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
$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('IES Universidad Laboral');
$pdf->SetTitle('Convalidaciones Centro Educativo y Ministerio');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('ulaboral, PDF, secretaría, Toledo, Convalidaciones Centro Educativo y Ministerio');

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT, PDF_MARGIN_HEADER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------

$pdf->setFontSubsetting(true);

$pdf->SetFont('dejavusans', '', 8, '', true);
$pdf->setFillColor(200);  //Relleno en gris
//Padding dentro de la celda del texto
$pdf->setCellPaddings(0,0,0,0);
//Interlineado
$pdf->setCellHeightRatio(1.5);


$pdf->AddPage();
// get the current page break margin
$bMargin = $pdf->getBreakMargin();
// get current auto-page-break mode
$auto_page_break = $pdf->getAutoPageBreak();
// disable auto-page-break
$pdf->SetAutoPageBreak(false, 0);
// set background image
$pdf->Image("sepa.jpg", 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
// restore auto-page-break status
$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
// set the starting point for the page content
$pdf->setPageMark();

$pdf->SetXY(22,129);
$pdf->Cell(0,0,$nombre . " " . $apellidos,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(22,139);
$pdf->Cell(0,0,$direccion,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(22,148);
$pdf->Cell(0,0,$cp." -" .$localidad." - ".$provincia,0,0,'L',0,'',1,true,'T','T');

for ($i=0;$i<strlen($bic);$i++){
    $pdf->SetXY(22+$i*7.5,169);
    $pdf->Cell(0,0,substr($bic,$i,1),0,0,'L',0,'',1,true,'T','T');
}
for ($i=0;$i<strlen($iban);$i++){
    $pdf->SetXY(22+$i*6,184);
    $pdf->Cell(0,0,substr($iban,$i,1),0,0,'L',0,'',1,true,'T','T');
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

$pdf->SetXY(25,207);
$pdf->Cell(0,0,$localidad." , a " . $fechaFormateada,0,0,'L',0,'',1,true,'T','T');

$pdf->Image("logo_ccm.jpg", 10, 10, 50, 30, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=" . $nombre_fichero);
$pdf->Output($nombre_fichero, 'I');



