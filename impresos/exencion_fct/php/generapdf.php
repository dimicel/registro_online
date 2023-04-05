<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
require_once(__DIR__.'/../../../php/tcpdf/config/tcpdf_config_alt.php');
require_once(__DIR__.'/../../../php/tcpdf/tcpdf.php');

class MYPDF extends TCPDF {

	//Page header
	public function Header() {
		// Logo
		$image_file = '../recursos/logo_ccm.jpg';
		$this->Image($image_file, 10, 10, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$image_file = '../recursos/mini_escudo.jpg';
		$this->Image($image_file, 140, 10, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
				
		$this->SetFont('helvetica', '', 8);
		// Title
		//$this->setCellHeightRatio(1.75);
		$encab = "<label><strong>IES Universidad Laboral</strong><br>Avda. Europa, 28<br>45003-TOLEDO<br>Tlf.:925 22 34 00<br>Fax:925 22 24 54</label>";
		$this->writeHTMLCell(0, 0, 160, 11, $encab, 0, 1, 0, true, 'C', true);
		//$this->Ln();
		//$this->writeHTMLCell(0, 0, '', '', '', 'B', 1, 0, true, 'L', true);
		
	}
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
    return "iesulabto_exefct_".date('dmY')."_".$array[0].$array[1].$array[2].$array[3].$array[4].$array[5].$array[6].$array[7];;    
}

function quitaAcentos($s){	
    $s=str_replace("á","a",$s);
    $s=str_replace("é","e",$s);
    $s=str_replace("í","i",$s);
    $s=str_replace("ó","o",$s);
    $s=str_replace("ú","u",$s);
    $s=str_replace("Á","A",$s);
    $s=str_replace("É","E",$s);
    $s=str_replace("Í","I",$s);
    $s=str_replace("Ó","O",$s);
    $s=str_replace("Ú","U",$s);
    $s=str_replace("1ª","Primera",$s);
    $s=str_replace("2ª","Segunda",$s);
    return $s;
}

function calculaCurso(){
    $mes=(int)date("n");
    $anno=(int)date("Y");
    if ($mes>=7 && $mes<=12) 
        return (string)($anno).'-'.(string)($anno+1);
    else
        return (string)($anno-1).'-'.(string)($anno);
}

if (!isset($_POST["nombre"])) exit("Acceso denegado");
$curso_acad= calculaCurso();
$lista_don=$_POST['lista_don'];
$nombre=$_POST['nombre'];
$nif_nie=$_POST['nif_nie'];

$pass_nif=$_POST['pass_nif'];

if (!isset($_POST['formacion'])) $ensenanzas="";
else{
	if (is_null($_POST['formacion'])) $ensenanzas="";
	else $ensenanzas=$_POST['formacion'];
}	
if (!isset($_POST['gmedio'])) $gmedio="";
else{
	if (is_null($_POST['gmedio'])) $gmedio="";
	else $gmedio=$_POST['gmedio'];
}	
if (!isset($_POST['gsuperior'])) $gsuperior="";
else{
	if (is_null($_POST['gsuperior'])) $gsuperior="";
	else $gsuperior=$_POST['gsuperior'];
}	

if (!isset($_POST['fpb'])) $fpb="";
else{
	if (is_null($_POST['fpb'])) $fpb="";
	else $fpb=$_POST['fpb'];
}


if ($ensenanzas=="Formación Profesional Básica") $curso=", especialidad de " . $fpb;
elseif ($ensenanzas=="Formación Profesional de Grado Medio") $curso=", especialidad de " . $gmedio;
elseif ($ensenanzas=="Formación Profesional de Grado Superior")	$curso=", especialidad de " . $gsuperior;
$documentacion=str_replace("\n","<br>",$_POST['documentacion']);

$num_documento="";
if ($pass_nif=="nif"){
	$num_documento="NIF/NIE número " . $nif_nie;
}
elseif ($pass_nif=="pass"){
	$num_documento="número de pasaporte " . $nif_nie;
}

$registro= generaRegistro();
include("conexion.php");

if ($mysqli->errno>0) {
    //header('Location: ../recepcion.html?mensaje="servidor"');
    exit("servidor");
}
$mysqli->set_charset("utf8");

$repite_registro=true;
while ($repite_registro){
    $res=$mysqli->query("select * from exenc_fct where registro='$registro'");
    if ($res->num_rows>0){
       $registro= generaRegistro(); 
    }
    else if ($res->num_rows==0){
        $repite_registro=false;
    }
    $res->free();
}


// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('IES Universidad Laboral');
$pdf->SetTitle('Impreso Matrícula');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('ulaboral, PDF, secretaría, Toledo, Impreso Matrícula');

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

$YInicio=40;
$XInicio=12;
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$fecha_actual=getdate();
$fecha_firma="Toledo, a ".$fecha_actual["mday"]." de ".$meses[$fecha_actual["mon"]-1]." de ".$fecha_actual["year"];

$texto=<<<EOD
<h2 style="text-align:center"><b>SOLICITUD DE EXENCIÓN DEL MÓDULO DE FORMACIÓN EN CENTROS DE TRABAJO</b></h2>
<br><br>
$lista_don $nombre, con $num_documento, <b>solicita la exención</b> de la Formación en Centros de Trabajo correspondiente a las enseñanzas de $ensenanzas de $curso que se imparte en el centro IES Universidad Laboral de Toledo, en el que está matriculado.  
<br><br>
Así, presenta la documentación establecida en el artículo 25 punto 2 de la Orden de 29 de julio de 2010, de la Consejería de Educación, Ciencia y Cultura, por la que se regula la evaluación, promoción y acreditación académica del alumnado de formación profesional inicial del sistema educativo de la Comunidad Autónoma de Castilla-La Mancha.<br>
$documentacion<br><br>
<p style="text-align:center">$fecha_firma<br><br><br><br>
Fdo.: $nombre</p>
EOD;

$pdf->SetXY($XInicio,$YInicio);
$pdf->MultiCell(180,0,$texto,0,'L',0,1,$XInicio,$YInicio,true,0,true,false,0);

$pdf->SetXY($XInicio,275);
$pdf->Cell(180,0,"SR/A. DIRECTOR/A DEL IES UNIVERSIDAD LABORAL DE TOLEDO",0,0,'L',0,'',1,true,'T','T');

// Close and output PDF document
$pdf->Output('exen_fct.pdf', 'D');
