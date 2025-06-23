<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("../../../php/conexion.php");
include("../../../php/mail.php");

if ($mysqli->errno>0) {
    exit("servidor");
}
include("../../../php/funciones.php");
require_once(__DIR__.'/../../../php/tcpdf/config/tcpdf_config_alt.php');
require_once(__DIR__.'/../../../php/tcpdf/tcpdf.php');
include("../../../php/cabecera_pdf.php");


function calculaCurso(){
    $mes=(int)date("n");
    $anno=(int)date("Y");
    if ($mes>=7 && $mes<=12) 
        return (string)($anno).'-'.(string)($anno+1);
    else
        return (string)($anno-1).'-'.(string)($anno);
}

if (!isset($_POST["nombre"])) {
    echo "Acceso denegado";
    exit();
}
$curso= calculaCurso();
$id_nie=$_POST['id_nie'];
$lista_don=strtoupper($_POST['lista_don']);
$nombre=strtoupper($_POST['nombre']);
$nif_nie=strtoupper($_POST['nif_nie']);
$pass_nif=strtoupper($_POST['pass_nif']);
$domicilio=strtoupper($_POST['domicilio']);
$telefono=strtoupper($_POST['telefono']);
$poblacion=strtoupper($_POST['poblacion']);
$cp=strtoupper($_POST['cp']);
$provincia=strtoupper($_POST['provincia']);
$grado=strtoupper($_POST['grado']);
$ciclo=strtoupper($_POST['ciclo']);
$modulo=strtoupper($_POST["modulo"]);
$nota=strtoupper($_POST["nota"]);
$razones=$_POST["razones"];
$email=$_POST["email"];
$id_nif=$_POST['id_nif'];
$usuario=$_POST['usuario'];
$fecha_registro=date('Y-m-d');

$num_documento="";
if ($pass_nif=="NIF"){
	$num_documento="NIF/NIE NÚMERO " . $nif_nie;
}
elseif ($pass_nif=="PASS"){
	$num_documento="NÚMERO DE PASAPORTE " . $nif_nie;
}

$registro=generaRegistro($mysqli, "revision_calificacion", "iesulabto_revcal_");

$mysqli->query("insert into revision_calificacion (id_nie
                                                   id_nif,
                                                   registro,
                                                   fecha_registro,
                                                   curso,
                                                   tratamiento,
                                                   nombre,
                                                   tipo_doc,
                                                   numero_doc,
                                                   domicilio,
                                                   telefono,
                                                   poblacion,
                                                   cp,
                                                   provincia,
                                                   ciclo_grado,
                                                   ciclo_nombre,
                                                   modulo,
                                                   nota,
                                                   motivos) 
                                                   values ('$id_nie',
                                                   '$id_nif',
                                                   '$registro',
                                                   '$fecha_registro',
                                                   '$curso',
                                                   '$lista_don',
                                                   '$nombre',
                                                   '$pass_nif',
                                                   '$nif_nie',
                                                   '$domicilio',
                                                   '$telefono',
                                                   '$poblacion',
                                                   '$cp',
                                                   '$provincia',
                                                   '$grado',
                                                   '$ciclo',
                                                   '$modulo',
                                                   '$nota',
                                                   '$razones')");
if ($mysqli->errno>0){
    exit("registro_erroneo ".$mysqli->errno);
}

// create new PDF document
$titulo_PDF = "";
$pdf = new MYPDF($datos_cen, $titulo_PDF);


// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($datos_cen["centro"]);
$pdf->SetTitle('Revisión de Calificación');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('ulaboral, PDF, secretaría, '. $datos_cen["localidad_centro"].', Revisión de Calificación');

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
$fecha_firma=$datos_cen["localidad_centro"].", a ".$fecha_actual["mday"]." de ".$meses[$fecha_actual["mon"]-1]." de ".$fecha_actual["year"];
$centro_estudios= strtoupper($datos_cen["centro"]);
$localidad_centro= strtoupper($datos_cen["localidad_centro"]);
$provincia_centro= strtoupper($datos_cen["provincia_centro"]);
$texto=<<<EOD
<h2 style="text-align:center"><b>SOLICITUD DE REVISIÓN DE LA CALIFICACIÓN</b></h2>
<br><br>
<b>$lista_don $nombre</b>, con <b>$num_documento</b>, domicilio <b>$domicilio</b>, teléfono <b>$telefono</b>, población <b>$poblacion</b> C.P. <b>$cp</b> y provincia <b>$provincia</b>,
<br><br>
<b>EXPONE:</b><br>
1.- Que está cursando en el centro <b>$centro_estudios</b> Localidad <b>$localidad_centro</b> Provincia de <b>$provincia_centro</b> el ciclo formativo de grado <b>$grado</b> denominado <b>$ciclo</b>.<br>
2.- Que ha obtenido una calificación final del módulo <b>$modulo</b> una nota de <b>$nota</b><br><br>
<b>SOLICITA:</b><br>
1.- Una revisión de dicha calificación.<br>
2.- Las razones expuestas para solicitar dicha revisión son las siguientes:<br>$razones<br><br><br>
<p style="text-align:center">$fecha_firma<br>Nº de registro: $registro</p>
EOD;


$pdf->SetXY($XInicio,$YInicio);
$pdf->writeHTMLCell(180,0,$XInicio,$YInicio,$texto,0,0,false,true,'',true);
$pdf->MultiCell(180,0,"JEFE/A DE ESTUDIOS DEL".strtoupper($datos_cen["centro"])." DE ".strtoupper($datos_cen["localidad_centro"]),0,'L',0,1,10,280,true,0,true,false,0);

$style_qr = array(
    'border' => false,
    'padding' => 0,
    'fgcolor' => array(0,0,0),
    'bgcolor' => false
);

$pdf->write2DBarcode($registro."\t".$nombre."\tREVISION CALIFICACION", "PDF417", 10, 250, 0, 30, $style_qr, 'N');

$mail->addAddress($email, '');
$mail->Subject = 'Registro Online';

$cuerpo = 'Registro online del '.$datos_cen["centro"].'<br>';
$cuerpo .= 'Tipo de formulario: Revisión de calificación<br><br>';
$cuerpo .= 'Su solicitud ha sido generada con el número de registro: '.$registro.'<br>';
$mail->Body =$cuerpo;
$adjunto=$pdf->Output($registro.".pdf","S");
$mail-> AddStringAttachment ($adjunto,$registro.".pdf", "base64", "application / pdf");

$check_envio=$mail->send();

if (!$check_envio) exit("envio_fallido".$registro);
echo "envio_ok".$registro;