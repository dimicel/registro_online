<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
//ob_start();
//error_reporting(E_ALL & ~E_NOTICE);
//ini_set('display_errors',0);
//ini_set('log_errors',1);
include("../../../php/conexion.php");
include("../../../php/mail.php");

if ($mysqli->errno>0) {
    exit("servidor");
}
$mysqli->set_charset("utf8");

require_once(__DIR__.'/../../../php/tcpdf/config/tcpdf_config_alt.php');
require_once(__DIR__.'/../../../php/tcpdf/tcpdf.php');


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
    return "iesulabto_revexa_".date('dmY')."_".$array[0].$array[1].$array[2].$array[3].$array[4].$array[5].$array[6].$array[7];;    
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


if (!isset($_POST["nombre"])) {
    exit("Acceso denegado");
}

$curso_acad= calculaCurso();
$id_nie=$_POST['id_nie'];
$lista_don=strtoupper($_POST['lista_don']);
$nombre=strtoupper($_POST['nombre']);
$nif_nie=strtoupper($_POST['nif_nie']);
$pass_nif=strtoupper($_POST['pass_nif']);
$padre=strtoupper($_POST['padres']);
$alumno=strtoupper($_POST['alum']);
$curso=strtoupper($_POST['curso']);
$profesor=strtoupper($_POST['profesor']);
$asignatura=strtoupper($_POST['asignatura']);
$fecha=strtoupper($_POST['fecha']);
$departamento=strtoupper($_POST["dpto"]);
$email=$_POST['email'];
$id_nif=$_POST['id_nif'];
$usuario=$_POST['usuario'];
$fecha_registro=date('Y-m-d');

$num_documento='';
if ($pass_nif=="NIF"){
	$num_documento="NIF/NIE NÚMERO " . $nif_nie;
}
elseif ($pass_nif=="PASS"){
	$num_documento="NÚMERO DE PASAPORTE " . $nif_nie;
}
 if ($padre=="ALUMNO") $padres="ALUMNO/A";
 else if ($padre=="PADRE") $padres="PADRE/MADRE";
 else if ($padre=="TUTOR") $padres="TUTOR/A";
 
 $registro= generaRegistro();

$repite_registro=true;
while ($repite_registro){
    $res=$mysqli->query("select * from revision_examen where registro='$registro'");
    if ($res->num_rows>0){
       $registro= generaRegistro(); 
    }
    else if ($res->num_rows==0){
        $repite_registro=false;
    }
    $res->free();
}

$mysqli->query("insert into revision_examen (id_nie,
                                            id_nif,
                                            registro,
                                            fecha_registro,
                                            curso,
                                            tratamiento,
                                            nombre,
                                            tipo_doc,
                                            numero_doc,
                                            en_calidad_de,
                                            del_alumno,
                                            cursa,
                                            departamento,
                                            profesor,
                                            asignatura,
                                            fecha) 
                                         values ('$id_nie',
                                                 '$id_nif',
                                                 '$registro',
                                                 '$fecha_registro',
                                                 '$curso_acad',
                                                 '$lista_don',
                                                 '$nombre',
                                                 '$pass_nif',
                                                 '$nif_nie',
                                                 '$padre',
                                                 '$alumno',
                                                 '$curso',
                                                 '$departamento',
                                                 '$profesor',
                                                 '$asignatura',
                                                 '$fecha')");
if ($mysqli->errno>0){
    exit("registro_erroneo ".$mysqli->errno);
}


// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('IES Universidad Laboral');
$pdf->SetTitle('Revisión de Examen');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('ulaboral, PDF, secretaría, Toledo, Revisión de Examen');

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
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$fecha_actual=getdate();
$fecha_firma="Toledo, a ".$fecha_actual["mday"]." de ".$meses[$fecha_actual["mon"]-1]." de ".$fecha_actual["year"];

$texto_a=<<<EOD
<h2 style="text-align:center"><b>SOLICITUD DE REVISIÓN DE EXAMEN</b></h2>
<br><br>
<b>$lista_don $nombre</b>, con <b>$num_documento</b>, en calidad de <b>$padre</b>, que cursa <b>$curso</b>, solicita la prueba escrita al departamento de <b>$departamento</b> de la asignatura/módulo <b>$asignatura</b> que fue realizada en la fecha <b>$fecha</b>
<br><br><br>
<p style="text-align:center">$fecha_firma<br>
Fdo. por usuario $id_nif." - ".$usuario</p>
<p>Nº de registro: $registro</p>
EOD;

$texto_b=<<<EOD
<h2 style="text-align:center"><b>SOLICITUD DE REVISIÓN DE EXAMEN</b></h2>
<br><br>
<b>$lista_don $nombre</b>, con <b>$num_documento</b>, en calidad de <b>$padre</b> del alumno/a <b>$alumno</b>, que cursa <b>$curso</b>, solicita la prueba escrita al departamento de <b>$departamento</b> de la asignatura/módulo <b>$asignatura</b> que fue realizada en la fecha <b>$fecha</b>
<br><br><br>
<p style="text-align:center">$fecha_firma<br>
Fdo. por usuario $id_nif." - ".$usuario</p>
<p>Nº de registro: $registro</p>
EOD;

if ($padre=="ALUMNO")$texto=$texto_a;
else $texto=$texto_b;

$pie=<<<EOD
- La solicitud de las pruebas escritas se realizará en un plazo máximo de dos días laborales tras la revisión de las pruebas escritas con el/la profesor/a.<br>- Las fotocopias solicitadas tienen un coste según la normativa vigente.<br>- La documentación entregada no podrá ser divulgada en otros medios atendiendo a la Ley Orgánica 15/1999 de 13 de diciembre de Protección de Datos de Carácter Personal, (LOPD) y a la normativa educativa.
EOD;

$pdf->SetXY($XInicio,$YInicio);
$pdf->writeHTMLCell(180,0,$XInicio,$YInicio,$texto,0,0,false,true,'',true);
$YInicio=$YInicio+100;
$pdf->SetXY($XInicio,$YInicio);
//$pdf->Cell(0,0,"Nº registro: ".$registro,0,0,'L',0,'',0,true,'T','T');
$pdf->MultiCell(180,0,$pie,0,'L',0,1,10,250,true,0,true,false,0);

$style_qr = array(
    'border' => false,
    'padding' => 0,
    'fgcolor' => array(0,0,0),
    'bgcolor' => false
);

$pdf->write2DBarcode($registro."\t".$nombre."\tREVISION EXAMEN", "PDF417", $XInicio, $YInicio+4, 0, 30, $style_qr, 'N');

$mail->addAddress($email, '');
$mail->Subject = 'Registro Online';

$cuerpo = 'Registro online del IES Universidad Laboral<br>';
$cuerpo .= 'Tipo de formulario: Revisión de examen<br><br>';
$cuerpo .= 'Su solicitud ha sido registrada con el número: '.$registro.'<br>';
$mail->Body =$cuerpo;
$adjunto=$pdf->Output($registro.".pdf","S");
$mail-> AddStringAttachment ($adjunto,$registro.".pdf", "base64", "application / pdf");

$check_envio=$mail->send();

//ob_end_clean();

if (!$check_envio) {
    exit("envio_fallido ".$registro);
}
echo "envio_ok ".$registro;

