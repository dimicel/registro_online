<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
require_once(__DIR__.'/../../../php/tcpdf/config/tcpdf_config_alt.php');
require_once(__DIR__.'/../../../php/tcpdf/tcpdf.php');
require __DIR__."/../../../php/mail.php";
include("../../../php/conexion.php");

if ($mysqli->errno>0) {
    exit("servidor");
}

$id_nie=$_POST['id_nie'];
$registro=$_POST['registro'];
$direccion=$_POST['direccion'];
$cp=$_POST['cp'];
$localidad=$_POST['localidad'];
$provincia=$_POST['provincia'];
$iban = trim($_POST['iban']);
$bic = trim($_POST['bic']);
$titular_cuenta = trim($_POST['titular_cuenta']);
$ip_remota=$_SERVER['REMOTE_ADDR'];
$fecha_hora_firma=date('Y-m-d H:i:s');

if (isset($_POST['firma_sepa'])){
	$imageData = urldecode($_POST['firma_sepa']);
	if (!is_dir(__DIR__."/../../../docs/tmp"))mkdir(__DIR__."/../../../docs/tmp",0777);
	$tempFile = tempnam(__DIR__."/../../../docs/tmp", 'canvas_'. session_id() . '.png');
	file_put_contents($tempFile, base64_decode(str_replace('data:image/png;base64,', '', $imageData)));
	$firma = $tempFile;
}

$mysqli->query("update residentes set titular_cuenta='$titular_cuenta', iban='$iban', bic='$bic', ip_remota='$ip_remota', fecha_hora_firma='$fecha_hora_firma' where registro='$registro'");
if ($mysqli->errno>0){
	unlink($tempFile);
    exit("registro_erroneo ".$mysqli->errno);
}

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
$pdf_sepa->Cell(0,0,$titular_cuenta,0,0,'L',0,'',1,true,'T','T');
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

// Array con los nombres de los meses en español
$meses = [
    1 => 'enero',
    2 => 'febrero',
    3 => 'marzo',
    4 => 'abril',
    5 => 'mayo',
    6 => 'junio',
    7 => 'julio',
    8 => 'agosto',
    9 => 'septiembre',
    10 => 'octubre',
    11 => 'noviembre',
    12 => 'diciembre'
];

// Obtener el día, mes y año
$dia = $fecha->format('d');
$mes = $meses[(int)$fecha->format('m')];
$anio = $fecha->format('Y');
$fechaFormateada = "$dia de $mes de $anio";

$pdf_sepa->SetXY(25,207);
$pdf_sepa->Cell(0,0,$localidad." , a " . $fechaFormateada,0,0,'L',0,'',1,true,'T','T');
//$pdf_sepa->Image($firma, 90, 210, 35, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
$pdf_sepa->Image($firma, 90, 210, 35, 0, '', '', '', false, 300);

$ruta_sepa=__DIR__."/../../../docs/".$id_nie."/residencia/sepa_". $id_nie.".pdf";

$pdf_sepa->Output($ruta_sepa, 'F');

exit("ok");


//FIN GENERA PDF


