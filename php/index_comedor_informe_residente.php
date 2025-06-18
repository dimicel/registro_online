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

$id_nie=$_POST["id_nie"];
$mes_anno=$_POST["mes_anno"];

$dat_centro = $mysqli->query("SELECT * FROM config_centro");
$datos_cen= $dat_centro->fetch_assoc();
$dat_centro->close();

$ausencias_avisadas=array();
$ausencias_no_avisadas=array();
$asistencias = array();

$sql = "
    SELECT *
    FROM residentes_comedor
	WHERE id_nie = ?
    WHERE fecha_no_comedor BETWEEN
        STR_TO_DATE(?, '%m/%Y') AND
        LASTDAY(STR_TO_DATE(?, '%m/%Y'))
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
$stmt->bind_param("ss", $mes_anno, $mes_anno);
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
	WHERE id_nie = ?
    WHERE fecha_comedor BETWEEN
        STR_TO_DATE(?, '%m/%Y') AND
        LASTDAY(STR_TO_DATE(?, '%m/%Y'))
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
$stmt->bind_param("ss", $mes_anno, $mes_anno);
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
	AND rc.fecha_comedor BETWEEN STR_TO_DATE(?, '%m/%Y') AND LASTDAY(STR_TO_DATE(?, '%m/%Y'))
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
$stmt->bind_param("ss", $mes_anno, $mes_anno);
$stmt->execute();

$result = $stmt->get_result();
while ($fila = $result->fetch_assoc()) {
    $ausencias_no_avisadas[] = array(
		'fecha' => $fila['fecha_comedor']
	);
}

$stmt->close();
$mysqli->close();

class MYPDF extends TCPDF {
	private $datos_cen;

    public function __construct($datos_cen) {
        parent::__construct(); // Llama al constructor de TCPDF
        $this->datos_cen = $datos_cen;
    }

	//Page header
	public function Header() {
		// Logo
		$image_file = '../recursos/logo_ccm.jpg';
		$this->Image($image_file, 10, 10, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$image_file = '../recursos/mini_escudo.jpg';
		$this->Image($image_file, 140, 10, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

		$this->SetFont('helvetica', 'B', 14);
		$this->SetXY(0,10);
		$this->Cell(0,0,"M A T R Í C U L A",0,0,'C',0,'',1,false,'T','T');
			
		$this->SetFont('helvetica', '', 8);
		// Title
		//$this->setCellHeightRatio(1.75);
		$encab = "<label><strong>".$this->datos_cen["centro"]."</strong><br>".$this->datos_cen["direccion_centro"]."<br>".$this->datos_cen["cp_centro"]."-".$this->datos_cen["localidad_centro"]."<br>Tlf.:".$this->datos_cen["tlf_centro"]."<br>Fax:".$this->datos_cen["fax_centro"]."</label>";
		$this->writeHTMLCell(0, 0, 160, 11, $encab, 0, 1, 0, true, 'C', true);
		//$this->Ln();
		//$this->writeHTMLCell(0, 0, '', '', '', 'B', 1, 0, true, 'L', true);
		
	}
}


// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($datos_cen["centro"]);
$pdf->SetTitle('PDF DNI/NIF');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('PDF, secretaría,'. $datos_cen["localidad_centro"].', PDF DNI/NIF');

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



$pdf->Output("Informe_comedor".$id_nie."_".$mes_anno.".pdf", 'D');



