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

$result = $mysqli->query($sql);

if ($mysqli->errno>0) {
    exit("server");
}
if ($result->num_rows==0){
    exit("sin_registro");
}


// Obtener la IP remota del usuario
$ip_remota = $_SERVER['REMOTE_ADDR'];

// Obtener la fecha y hora actuales
$fecha_hora_actual = date("Y-m-d H:i:s");

// Actualizar la tabla exencion_fct
$update_query = "UPDATE exencion_fct 
                 SET resolucion = ?, 
                     motivo = ?, 
                     procesado = 1, 
                     fecha_hora_firma_jd = ?, 
                     ip_remota_j = ? 
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














