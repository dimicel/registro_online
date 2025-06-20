<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");

require_once('tcpdf/config/tcpdf_config_alt.php');
require_once('tcpdf/tcpdf.php');
include("conexion.php");

if ($mysqli->errno>0) {
    http_response_code(500);
    echo "Error en servidor.";
    exit;
}
$titulo_PDF = "INFORME COMEDOR RESIDENTE";
include("cabecera_pdf.php");

$id_nie=$_POST["id_nie"];
$mes_anno=$_POST["mes_anno"];

$ausencias_avisadas=array();
$ausencias_no_avisadas=array();
$asistencias = array();

$sql = "
    SELECT *
    FROM residentes
	WHERE id_nie = ?
";

// Preparar y ejecutar
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
	http_response_code(500);
    echo "Error en prepare: " . $mysqli->error;
	exit;
}

// Pasamos la misma fecha dos veces (inicio y para LASTDAY)
$stmt->bind_param("s", $id_nie);
$stmt->execute();

$result = $stmt->get_result();
$nombre_residente = "";
$apellidos_residente = "";
$fila = $result->fetch_assoc();
if ($fila) {
	$nombre_residente = $fila['nombre'];
	$apellidos_residente = $fila['apellidos'];
} else {
	http_response_code(404);
	echo "Residente no encontrado.";
	exit;
}

$stmt->close();


$sql = "
    SELECT *
    FROM residentes_comedor
	WHERE id_nie = ? AND fecha_no_comedor BETWEEN
        STR_TO_DATE(?, '%m/%Y') AND
        LAST_DAY(STR_TO_DATE(?, '%m/%Y'))
	ORDER BY fecha_no_comedor
";

// Preparar y ejecutar
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
	http_response_code(500);
    echo "Error en prepare: " . $mysqli->error;
	exit;
}

// Pasamos la misma fecha dos veces (inicio y para LASTDAY)
$stmt->bind_param("sss", $id_nie,$mes_anno, $mes_anno);
$stmt->execute();

$result = $stmt->get_result();
while ($fila = $result->fetch_assoc()) {
    $ausencias_avisadas[] = array(
		'fecha' => $fila['fecha_no_comedor']
	);
}

$stmt->close();

$sql = "
    SELECT *
    FROM residentes_comedor
	WHERE id_nie = ? AND fecha_comedor BETWEEN
        STR_TO_DATE(?, '%m/%Y') AND
        LAST_DAY(STR_TO_DATE(?, '%m/%Y'))
	ORDER BY fecha_comedor
";

// Preparar y ejecutar
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
	http_response_code(500);
    echo "Error en prepare: " . $mysqli->error;
	exit;
}

// Pasamos la misma fecha dos veces (inicio y para LASTDAY)
$stmt->bind_param("sss", $id_nie,$mes_anno, $mes_anno);
$stmt->execute();

$result = $stmt->get_result();
while ($fila = $result->fetch_assoc()) {
    $asistencias[] = array(
		'fecha' => $fila['fecha_comedor'],
		'desayuno' => $fila['desayuno'],
		'comida' => $fila['comida'],
		'cena' => $fila['cena']
	);
}

$stmt->close();


$sql = "
	SELECT *
	FROM residentes_comedor rc
	WHERE rc.id_nie = ?
	AND rc.fecha_comedor BETWEEN STR_TO_DATE(?, '%m/%Y') AND LAST_DAY(STR_TO_DATE(?, '%m/%Y'))
	AND rc.desayuno = 0
	AND rc.comida = 0
	AND rc.cena = 0
	AND NOT EXISTS (
		SELECT 1
		FROM residentes_comedor rc2
		WHERE rc2.fecha_no_comedor = rc.fecha_comedor
	)
	ORDER BY rc.fecha_comedor
";

// Preparar y ejecutar
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
	http_response_code(500);
    echo "Error en prepare: " . $mysqli->error;
	exit;
}

// Pasamos la misma fecha dos veces (inicio y para LASTDAY)
$stmt->bind_param("sss", $id_nie, $mes_anno, $mes_anno);
$stmt->execute();

$result = $stmt->get_result();
while ($fila = $result->fetch_assoc()) {
    $ausencias_no_avisadas[] = array(
		'fecha' => $fila['fecha_comedor']
	);
}

$stmt->close();
$mysqli->close();


// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($datos_cen["centro"]);
$pdf->SetTitle('PDF DNI/NIF');
$pdf->SetSubject('Residencia Comedor Informe Residente');
$pdf->SetKeywords('PDF, comedor,'. $datos_cen["localidad_centro"].', PDF DNI/NIF');

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
$XInicio=10;
$YInicio=50;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(180, 0, "INFORME COMEDOR RESIDENTE: ".$id_nie." - ".$nombre_residente." ".$apellidos_residente."-".$mes_anno, 0, 1, 'C', 0, '', 0, false, 'T', 'T');
$texto=<<<EOD
DIAS AVISADOS: Fechas en las que se comunicó que no se iba a asistir al comedor.
INJUSTIFICADAS: Fechas en las que el residente no asistió al comedor en todo el día.
AISTENCIAS: Fechas en las que el residente asistió al comedor, y tipo de servicio usado (desayuno (Des), comida (Com) o cena (Cen)).
EOD;
$YInicio+=10;
$pdf->SetFont('dejavusans', '', 8, '', true);
$pdf->SetXY($XInicio,$YInicio);
$pdf->writeHTMLCell(180, 0, $XInicio, $YInicio, $texto, 0, 1, false, true, 'L', true);
$YInicio+=20;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);

// Encabezados alineados: izquierda, centro, derecha
$pdf->Cell(60, 10, 'DIAS AVISADOS', 0, 0, 'L');
$pdf->Cell(60, 10, 'INJUSTIFICADAS', 0, 0, 'C');
$pdf->Cell(60, 10, 'ASISTENCIAS  Des  Com  Cen', 0, 1, 'L');

$YInicio+=10;
$pdf->SetFont('dejavusans', '', 8, '', true);
$fila=0;
$avis=true;
$noavis=true;
$asist=true;
while (true){
	if ($fila < count($ausencias_avisadas)) {
		$pdf->Cell(60, 5, date("d/m/Y", strtotime($ausencias_avisadas[$fila]['fecha'])), 0, 0, 'L');
	} else {
		if($fila==0) $pdf->Cell(60, 5, 'No hay fechas', 0, 0, 'L');
		else $pdf->Cell(60, 5, '', 0, 0, 'L');
		$avis = false;
	}
	if ($fila < count($ausencias_no_avisadas)) {
		$pdf->Cell(60, 5, date("d/m/Y", strtotime($ausencias_no_avisadas[$fila]['fecha'])), 0, 0, 'C');
	} else {
		if($fila==0) $pdf->Cell(60, 5, 'No hay fechas', 0, 0, 'C');
		else  $pdf->Cell(60, 5, '', 0, 0, 'C');
		$noavis = false;
	}
	if ($fila < count($asistencias)) {
		$desayuno = $asistencias[$fila]['desayuno'] ? 'X' : '';
		$comida = $asistencias[$fila]['comida'] ? 'X' : '';
		$cena = $asistencias[$fila]['cena'] ? 'X' : '';
		$pdf->Cell(60, 5, date("d/m/Y", strtotime($asistencias[$fila]['fecha'])).'    '.$desayuno.'        '.$comida.'       '.$cena, 0, 1, 'L');
	} else {
		if($fila==0) $pdf->Cell(60, 5, 'No hay fechas', 0, 1, 'L');
		else $pdf->Cell(60, 5, '', 0, 1, 'L');
		$asist = false;
	}
	if (!$avis && !$noavis && !$asist) break; // Si no hay más datos, salimos del bucle
	$fila++;
}

$pdf->Output("Informe_comedor".$id_nie."_".$mes_anno.".pdf", 'D');



