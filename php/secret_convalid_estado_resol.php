<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
$mysqli->set_charset("utf8");
require_once('tcpdf/config/tcpdf_config_alt.php');
require_once('tcpdf/tcpdf.php');
header("Content-Type: text/html;charset=utf-8");


if ($mysqli->errno>0) {
    exit("server");
}
$registro=$_POST["registro"];
$modulos=$_POST["modulo_convalid"];
$estados=$_POST["estado_convalid"];
$motivos=$_POST["motivo_no_fav_convalid"];
$elementos_sin_resolver=false;
$resuelto_por=array(
    "FAVORABLE"=>"CENTRO",
    "NO FAVORABLE"=>"CENTRO",
    "CONSEJERIA"=>"CONSEJERIA",
    "MINISTERIO"=>"MINISTERIO"

);

$res_cen=0;
$res_con=0;
$res_min=0;
for ($i=0; $i<count($estados);$i++){
    if($estados[$i]=="FAVORABLE" || $estados[$i]=="NO FAVORABLE") $res_cen++;
    elseif($estados[$i]=="CONSEJERIA") $res_con++;
    elseif($estados[$i]=="MINISTERIO") $res_min++;
}

if ($res_cen>0) $act_rescen=1;
else $act_rescen=0;
if ($res_con>0) $act_rescon=1;
else $act_rescon=0;
if ($res_min>0) $act_resmin=1;
else $act_resmin=0;

$consulta_act_estado="update convalidaciones set resuelve_cen='$act_rescen', resuelve_con='$act_rescon', resuelve_min='$act_resmin' where registro='$registro'";

$mysqli->begin_transaction();

try {
    // Iterar sobre los arrays y actualizar los registros en la base de datos
    for ($i = 0; $i < count($modulos); $i++) {
        if ($estados[$i]==""){
            if ($estados[$i]=="") $elementos_sin_resolver=true;
            continue;
        }
        $sql = "UPDATE convalidaciones_modulos SET resolucion = '" . $estados[$i] . "', motivo_no_favorable = '" . $motivos[$i] . "', resuelto_por = '" . $resuelto_por[$estados[$i]] . "' WHERE registro = '$registro' AND modulo='$modulos[$i]'";

        if ($mysqli->query($sql) !== TRUE) {
            throw new Exception("error_db");
        }
    }
    
    if ($mysqli->query($consulta_act_estado) !== TRUE) {
        throw new Exception("error_db_conval");
    }

    // Confirmar la transacción
    $mysqli->commit();
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $mysqli->rollback();
    $mysqli->close();
    exit ($e->getMessage());
}

// Cerrar conexión
$mysqli->close();

//Salida del script
if ($elementos_sin_resolver) exit("elementos_sin_resolver");
if($res_cen==0){
    if($res_con==0 && $res_min>0) exit("ok_ministerio");
    elseif($res_con>0 && $res_min==0) exit("ok_consejeria");
    elseif($res_con>0 && $res_min>0) exit("ok_consejeria_ministerio");
}

//Recuperación de datos de la tabla convalidaciones
$concov=$mysqli->query("select * from convalidaciones where registro='$registro'");
if($concov->num_rows!=1){
    exit("no_datospdf");
}
$dr=$concov->fetch_assoc();
$mysqli->close();
//Se genera el pdf para el alumno si están todos los módulos resueltos y, al menos, hay uno que resuelve el centro
class MYPDF extends TCPDF {

	//Page header
	public function Header() {
		// Logo
		$image_file = '../recursos/logo_ccm.jpg';
		$this->Image($image_file, 10, 10, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$image_file = '../recursos/mini_escudo.jpg';
		$this->Image($image_file, 140, 10, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

		$this->SetFont('helvetica', 'B', 14);
		$this->SetXY(0,10);
		$this->Cell(0,0,"RESOLUCIÓN CONVALIDACIÓN MÓDULOS",0,0,'C',0,'',1,false,'T','T');
			
		$this->SetFont('helvetica', '', 8);
		// Title
		//$this->setCellHeightRatio(1.75);
		$encab = "<label><strong>IES Universidad Laboral</strong><br>Avda. Europa, 28<br>45003-TOLEDO<br>Tlf.:925 22 34 00<br>Fax:925 22 24 54</label>";
		$this->writeHTMLCell(0, 0, 160, 11, $encab, 0, 1, 0, true, 'C', true);
		//$this->Ln();
		//$this->writeHTMLCell(0, 0, '', '', '', 'B', 1, 0, true, 'L', true);
	}
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('IES Universidad Laboral');
$pdf->SetTitle('Resolución Convalidaciones');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('ulaboral, PDF, secretaría, Toledo, Resolución Convalidaciones');

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








//--------FINAL
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

$fecha_firma="Toledo, a ".$dd." de ".$mm." de ".$yyyy;
$texto=<<<EOD
<p style="text-align:center">$fecha_firma<br>Nº de registro: $registro</p>
EOD;
$YInicio+=50;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
//$pdf->Cell(0,0,"Nº registro " . $texto,0,0,'C',0,'',1,false,'T','T');
$pdf->writeHTMLCell(180,0,$XInicio,$YInicio,$texto,0,0,false,true,'',true);


//GENERA EL ARCHIVO NUEVO
$nombre_fichero=$registro . '.pdf';
if (!is_dir(__DIR__."/../../../docs/".$id_nie))mkdir(__DIR__."/../../../docs/".$id_nie,0777);
if(!is_dir(__DIR__."/../../../docs/".$id_nie."/matriculas"))mkdir(__DIR__."/../../../docs/".$id_nie."/matriculas",0777);
if(!is_dir(__DIR__."/../../../docs/".$id_nie."/matriculas"."/".$anno_curso))mkdir(__DIR__."/../../../docs/".$id_nie."/matriculas"."/".$anno_curso,0777);
$ruta=__DIR__."/../../../docs/".$id_nie."/"."matriculas/".$anno_curso."/". $nombre_fichero;
$pdf->Output($ruta, 'F');
//Salida OK
exit("ok");

/*
$sql = "UPDATE convalidaciones SET resolucion='$estado' WHERE registro='$registro'";
$result = $mysqli->query($sql);
if ($mysqli->affected_rows > 0) {
    $mysqli->close();
    exit("ok");
}
else {
    $mysqli->close();
    exit("no_registro");
}
*/
