<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
require_once('tcpdf/config/tcpdf_config_alt.php');
require_once('tcpdf/tcpdf.php');
header("Content-Type: text/html;charset=utf-8");

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

if ($mysqli->errno>0) {
    exit("server");
}

$consultaCentro = $mysqli->query("SELECT * FROM config_centro ");
if ($consultaCentro->num_rows!=1){
    exit("config_centro");
}
else {
    $datosCentro=$consultaCentro->fetch_assoc();
    $nombreDirector=$datosCentro["director"];
    $nombreDirectorMayus=strtoupper($nombreDirector);
}

$id_nie=$_POST["id_nie"];
$curso=$_POST["curso"];
$departamento=$_POST["departamento"];
$apellidos=$_POST["apellidos"];
$nombre=$_POST["nombre"];
$id_nif=$_POST["id_nif"];
$curso_ciclo=$_POST["curso_ciclo"];
$grado=$_POST["grado"];
$ciclo=$_POST["ciclo"];
$valoracion=$_POST["valoracion"];
$motivo=$_POST["motivo"];
$registro=$_POST["registro"];
$dirRegistro=$_POST["dirRegistro"];
$nombre_ap_jd=$_POST["nombre_ap_jd"];
// Obtener la IP remota del usuario
$ip_remota = $_SERVER['REMOTE_ADDR'];
// Obtener la fecha y hora actuales
$fecha_hora_actual = date("Y-m-d H:i:s");

if (isset($_POST['firma'])){
    $imageData = urldecode($_POST['firma']);
    if (!is_dir(__DIR__."/../../../docs/tmp")) mkdir(__DIR__."/../../../docs/tmp", 0777);
    
    // Generar el archivo temporal
    $tempFile = tempnam(__DIR__."/../../../docs/tmp", 'canvas_' . session_id());
    
    // Asegurarse de que la extensión sea '.png' y no haya caracteres extra
    $tempFile = pathinfo($tempFile, PATHINFO_DIRNAME) . '/' . basename($tempFile, '.tmp') . '.png';
    
    // Guardar el archivo de imagen
    file_put_contents($tempFile, base64_decode(str_replace('data:image/png;base64,', '', $imageData)));
    $firma = $tempFile;
}


$consulta="SELECT * FROM exencion_fct  where registro='$registro'";

$result = $mysqli->query($consulta);

if ($mysqli->errno>0) {
    exit("server");
}
if ($result->num_rows!=1){
    exit("sin_registro");
}
else {
	$row = $result->fetch_assoc();
	$tratamiento=$row["tratamiento"];
}


// Actualizar la tabla exencion_fct
$update_query = "UPDATE exencion_fct 
                 SET resolucion = ?, 
                     motivo = ?, 
                     procesado = 1, 
                     fecha_hora_firma_jd = ?, 
                     ip_remota_jd = ? 
                 WHERE registro = ?";

$stmt = $mysqli->prepare($update_query);
if (!$stmt) {
    exit("Error en la preparación de la consulta: " . $mysqli->error);
}

$stmt->bind_param("sssss", $valoracion, $motivo, $fecha_hora_actual, $ip_remota, $registro);

if (!$stmt->execute()) {
    exit("Error al ejecutar la consulta: " . $stmt->error);
}

// Verificar si se actualizó alguna fila
if ($stmt->affected_rows === 0) {
    exit("sin_actualizacion");
}
$stmt->close();
$mysqli->close();
exit("ok_text");
///////////////////////////////////////////////////////////////////////////////////////////
///GENERA EL INFORME
///////////////////////////////////////////////////////////////////////////////////////////
if($valoracion=="exento"){
	$texto_acuerda="INFORMAR FAVORABLEMENTE DE LA EXENCIÓN TOTAL DEL PERÍODO DE FORMACIÓN EN EMPRESAS AL ALUMNO <br>";
	$texto_acuerda.=strtoupper($tratamiento)." ".strtoupper($nombre)." ".strtoupper($apellidos);
	$motivo="";
}
elseif($valoracion=="parcialmente_exento"){
	$texto_acuerda="INFORMAR FAVORABLEMENTE DE LA EXENCIÓN PARCIAL DEL PERÍODO DE FORMACIÓN EN EMPRESAS AL ALUMNO ";
	$texto_acuerda.=strtoupper($tratamiento)." ".strtoupper($nombre)." ".strtoupper($apellidos)." POR LOS MOTIVOS QUE A CUNTINUACIÓN SE RAZONAN:<br>";
}
elseif($valoracion=="no_exento"){
	$texto_acuerda="INFORMAR DESFAVORABLEMENTE DE LA EXENCIÓN DEL PERÍODO DE FORMACIÓN EN EMPRESAS AL ALUMNO ";
	$texto_acuerda.=strtoupper($tratamiento)." ".strtoupper($nombre)." ".strtoupper($apellidos)." POR LOS MOTIVOS QUE A CUNTINUACIÓN SE RAZONAN:<br>";
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('IES Universidad Laboral');
$pdf->SetTitle('Exención PFE - Informe JD');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('ulaboral, PDF, secretaría, Toledo, Exención PFE - Informe JD');

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
<h3 style="text-align:center"><b>DEPARTAMENTO DE {strtoupper($departamento)}</b></h3>
<h2>style="text-align:center"><b>INFORME DE EXENCIÓN DE PFE A {strtoupper($tratamiento)} {strtoupper($nombre)} {strtoupper($apellidos)}</b></h2>
Examinada la documentación recibida en la Secretaría del Centro sobre la petición de solicitud de exención del Período de Formación en Empresas
 presentada por el alumno/a <b>{strtoupper($tratamiento)} {strtoupper($nombre)} {strtoupper($apellidos)}</b>, con DNI/NIE <b>$id_nif</b>, del curso 
 <b>$curso_ciclo</b> del Ciclo Formativo de Grado <b>$grado</b> de <b>$ciclo</b>, este departamento<br><br>
 ACUERDA:<br><br>
<p style="text-align:justify">$texto_acuerda</p>
<p style="text-align:justify">$motivo<br><br>
<p style="text-align:center">$fecha_firma
EOD;
$pdf->SetXY($XInicio,$YInicio);
$pdf->writeHTMLCell(180, 0, $XInicio, $YInicio, $texto, 0, 1, false, true, 'L', true);
$posicionY=$pdf->getY();
$anchoImagen=50;
$pdf->Image($firma, (210-$anchoImagen)/2, $posicionY, $anchoImagen, 0, 'PNG');

$texto=<<<EOD
<br><br><br><br><br><br>
Fdo.: $nombre_ap_jd</p>
EOD;

$posicionY=$pdf->getY();
$pdf->SetXY($XInicio,$posicionY);
$pdf->writeHTMLCell(180, 0, $XInicio, $posicionY, $texto, 0, 1, false, true, 'C', true);


// Agregar texto en el lateral izquierdo en formato vertical, centrado en la altura de un A4
$pdf->StartTransform();
$pdf->Rotate(90, 5, 148); // Rotar el texto 90 grados (centrado en la altura de A4)

// Calcular la posición centrada en la altura de A4
$alturaPagina = $pdf->getPageHeight(); // Altura de la página
$posicionCentradaY = $alturaPagina / 2; // Calcular la posición centrada

$pdf->SetFont('dejavusans', '', 8, '', true);
$pdf->Text(5, $posicionCentradaY, "Fecha y hora de firma: $fecha_hora_firma");
$pdf->StopTransform();


//GENERA EL ARCHIVO NUEVO
if(!is_dir(__DIR__."/../../../docs/".$id_nie."/"."exencion_form_emp/".$anno_curso."/".$dirRegistro."/docs/informe_jd"))mkdir(__DIR__."/../../../docs/".$id_nie."/"."exencion_form_emp/".$anno_curso."/".$dirRegistro."/docs/informe_jd",0777,true);
$ruta=__DIR__."/../../../docs/".$id_nie."/"."exencion_form_emp/".$anno_curso."/".$dirRegistro."/docs/informe_jd/informe_jd.pdf";
$pdf->Output($ruta, 'F');
//FIN GENERA PDF



///////////////////////////////////////////////////////////////////////////////////////////
///GENERA LA RESOLUCIÓN
///////////////////////////////////////////////////////////////////////////////////////////
if($valoracion=="exento"){
	$texto_acuerda="INFORMAR FAVORABLEMENTE DE LA EXENCIÓN TOTAL DEL PERÍODO DE FORMACIÓN EN EMPRESAS AL ALUMNO <br>";
	$texto_acuerda.=strtoupper($tratamiento)." ".strtoupper($nombre)." ".strtoupper($apellidos);
	$motivo="";
}
elseif($valoracion=="parcialmente_exento"){
	$texto_acuerda="INFORMAR FAVORABLEMENTE DE LA EXENCIÓN PARCIAL DEL PERÍODO DE FORMACIÓN EN EMPRESAS AL ALUMNO ";
	$texto_acuerda.=strtoupper($tratamiento)." ".strtoupper($nombre)." ".strtoupper($apellidos)." POR LOS MOTIVOS QUE A CUNTINUACIÓN SE RAZONAN:<br>";
}
elseif($valoracion=="no_exento"){
	$texto_acuerda="INFORMAR DESFAVORABLEMENTE DE LA EXENCIÓN DEL PERÍODO DE FORMACIÓN EN EMPRESAS AL ALUMNO ";
	$texto_acuerda.=strtoupper($tratamiento)." ".strtoupper($nombre)." ".strtoupper($apellidos)." POR LOS MOTIVOS QUE A CUNTINUACIÓN SE RAZONAN:<br>";
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('IES Universidad Laboral');
$pdf->SetTitle('Exención PFE - Informe JD');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('ulaboral, PDF, secretaría, Toledo, Exención PFE - Informe JD');

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

$texto=<<<EOD
<h3 style="text-align:center"><b>RESOLUCIÓN EXENCIÓN PFE</b></h3>
Recibido el informe del Jefe de Departamento de $departamento 
 presentada por el alumno/a <b>{strtoupper($tratamiento)} {strtoupper($nombre)} {strtoupper($apellidos)}</b>, con DNI/NIE <b>$id_nif</b>, del curso 
 <b>$curso_ciclo</b> del Ciclo Formativo de Grado <b>$grado</b> de <b>$ciclo</b>, este departamento<br><br>
 ACUERDA:<br><br>
<p style="text-align:justify">$texto_acuerda</p>
<p style="text-align:justify">$motivo<br><br>
<p style="text-align:center">$fecha_firma
EOD;
$pdf->SetXY($XInicio,$YInicio);
$pdf->writeHTMLCell(180, 0, $XInicio, $YInicio, $texto, 0, 1, false, true, 'L', true);
$posicionY=$pdf->getY();
$anchoImagen=50;
$pdf->Image("../recursos/sello_firma.jpg", (210-$anchoImagen)/2, $posicionY, $anchoImagen, 0, 'JPG');

$texto=<<<EOD
<br><br><br><br><br><br>
Fdo.: $nombreDirector</p>
EOD;

$posicionY=$pdf->getY();
$pdf->SetXY($XInicio,$posicionY);
$pdf->writeHTMLCell(180, 0, $XInicio, $posicionY, $texto, 0, 1, false, true, 'C', true);


// Agregar texto en el lateral izquierdo en formato vertical, centrado en la altura de un A4
$pdf->StartTransform();
$pdf->Rotate(90, 5, 148); // Rotar el texto 90 grados (centrado en la altura de A4)

// Calcular la posición centrada en la altura de A4
$alturaPagina = $pdf->getPageHeight(); // Altura de la página
$posicionCentradaY = $alturaPagina / 2; // Calcular la posición centrada

$pdf->SetFont('dejavusans', '', 8, '', true);
$pdf->Text(5, $posicionCentradaY, "Fecha y hora de firma: $fecha_hora_firma");
$pdf->StopTransform();


//GENERA EL ARCHIVO NUEVO
if(!is_dir(__DIR__."/../../../docs/".$id_nie."/"."exencion_form_emp/".$anno_curso."/".$dirRegistro."/docs/resolucion"))mkdir(__DIR__."/../../../docs/".$id_nie."/"."exencion_form_emp/".$anno_curso."/".$dirRegistro."/docs/resolucion",0777,true);
$ruta=__DIR__."/../../../docs/".$id_nie."/"."exencion_form_emp/".$anno_curso."/".$dirRegistro."/docs/resolucion/informe_jd.pdf";
$pdf->Output($ruta, 'F');
//FIN GENERA PDF














