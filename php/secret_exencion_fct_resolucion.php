<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
require_once('tcpdf/config/tcpdf_config_alt.php');
require_once('tcpdf/tcpdf.php');
header("Content-Type: text/html;charset=utf-8");


if ($mysqli->errno>0) {
    exit("server");
}

$registro=$_POST["registro"];

$consultaCentro = $mysqli->query("SELECT * FROM config_centro ");
if ($consultaCentro->num_rows!=1){
    exit("config_centro");
}
else {
    $datosCentro=$consultaCentro->fetch_assoc();
    $nombreDirector=$datosCentro["director"];
    $nombreDirectorMayus=strtoupper($nombreDirector);
	$centroEducativo=$datosCentro["centro"];
	$centroEducativoMayus=strtoupper($centroEducativo);	
}

$consulta = $mysqli->query("SELECT * FROM exencion_fct WHERE registro='$registro'");
if ($consulta->num_rows!=1){
	exit("no_registro");
}
else {
	$datos=$consulta->fetch_assoc();

	$tratamiento=$datos["tratamiento"];
	$tratamientoMayus=strtoupper($tratamiento);
	$id_nie=$datos["id_nie"];
	$anno_curso=$datos["curso"];
	$departamento=$datos["departamento"];
	$departamentoMayus=strtoupper($departamento);
	$apellidos=$datos["apellidos"];
	$apellidosMayus=strtoupper($apellidos);
	$nombre=$datos["nombre"];
	$nombreMayus=strtoupper($nombre);
	$id_nif=$datos["id_nif"];
	$curso_ciclo=$datos["curso_ciclo"];
	$grado=$datos["grado"];
	$ciclo=$datos["ciclo"];
	$valoracion=$datos["resolucion"];
	$motivo=$datos["motivo"];
	$dirRegistro=substr($registro,17);
	// Obtener la IP remota del usuario
	$ip_remota = $_SERVER['REMOTE_ADDR'];
	// Obtener la fecha y hora actuales
	$fecha_hora_actual = date("Y-m-d H:i:s");
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
/*if ($stmt->affected_rows === 0) {
    exit("sin_actualizacion");
}*/
$stmt->close();



///////////////////////////////////////////////////////////////////////////////////////////
///GENERA LA RESOLUCIÓN
///////////////////////////////////////////////////////////////////////////////////////////

//----------------------- Configuración del PDF ------------------
include("cabecera_pdf.php");
$titulo_PDF = "<label><strong>CONSEJERÍA DE EDUCACIÓN CULTURA Y DEPORTES DE CASTILLA - LA MANCHA<br>".strtoupper($datos_cen["centro"]) ." DE ". strtoupper($datos_cen["localidad_centro"])."</strong></label>";
$pdf = new MYPDF($datos_cen, $titulo_PDF);

$mysqli->close();

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($datos_cen["centro"]);
$pdf->SetTitle('Exención PFE - Reslución');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('PDF, secretaría, '. $datos_cen["localidad_centro"].', Exención PFE - Resolución');

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

$pdf->setFontSubsetting(true);

$pdf->SetFont('dejavusans', '', 8, '', true);
$pdf->setFillColor(200);  //Relleno en gris


$pdf->AddPage();

// ---------------------------------------------------------
if($valoracion=="exento"){
	$texto_acuerda="<b>RECONOCER</b> la exención <b>TOTAL</b> del Período de Formación en Empresas por su correspondencia con la experiencia laboral acreditada.";
	$motivo="";
}
elseif($valoracion=="parcialmente exento"){
	$texto_acuerda="<b>RECONOCER</b> la exención <b>PARCIAL</b> del Período de Formación en Empresas por los motivos que a continuación se razonan:";
}
elseif($valoracion=="no exento"){
	$texto_acuerda="<b>NO RECONOCER</b> la exención del Período de Formación en Empresas por los motivos que a continuación se razonan:";
}

$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$fecha_actual=getdate();
$fecha_firma=$datos_cen["localidad_centro"].", a ".$fecha_actual["mday"]." de ".$meses[$fecha_actual["mon"]-1]." de ".$fecha_actual["year"];



//Padding dentro de la celda del texto
$pdf->setCellPaddings(0,0,0,0);
//Interlineado
$pdf->setCellHeightRatio(2);

$YInicio=40;
$XInicio=12; 

$texto=<<<EOD
<br><br><br><br><br><br><br><br><b>D./DÑA. $nombreDirectorMayus</b>, director/a del Centro Educativo $centroEducativoMayus DE TOLEDO, una vez
examinada la documentación presentada por el alumno/a <b>$tratamientoMayus $nombreMayus $apellidosMayus</b>, con DNI/NIE <b>$id_nif</b>,
solicitando la exención del Perído de Formación en Empresas, correspondiente al curso <b>$curso_ciclo del Ciclo Formativo de Grado $grado</b> de <b>$ciclo</b><br><br>
<b>RESUELVE:</b><br>$texto_acuerda<br>$motivo<br><br>
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
$pdf->Rotate(90, 2, 148); // Rotar el texto 90 grados (centrado en la altura de A4)

// Calcular la posición centrada en la altura de A4
$alturaPagina = $pdf->getPageHeight(); // Altura de la página
$posicionCentradaY = $alturaPagina / 2; // Calcular la posición centrada

$pdf->SetFont('dejavusans', '', 5, '', true);
$pdf->Text(2, $posicionCentradaY, "Fecha y hora de firma: $fecha_hora_actual");
$pdf->StopTransform();


//GENERA EL ARCHIVO NUEVO
$dir = "../docs/".$id_nie."/"."exencion_form_emp/".$anno_curso."/".$dirRegistro."/docs/resolucion";
if (!is_dir($dir)) mkdir($dir, 0777, true);

$ruta = realpath($dir) . "/resolucion.pdf";
$pdf->Output($ruta, 'F');
//FIN GENERA RESOLUCIÓN

exit("ok");














