<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
require_once(__DIR__.'/../../../php/tcpdf/config/tcpdf_config_alt.php');
require_once(__DIR__.'/../../../php/tcpdf/tcpdf.php');
include("../../../php/conexion.php");

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
    return "iesulabto_exefem_".date('dmY')."_".$array[0].$array[1].$array[2].$array[3].$array[4].$array[5].$array[6].$array[7];;    
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


$anno_curso= $_POST['anno_curso'];
$fecha_registro=date('Y-m-d');
$id_nie=$_POST['id_nie'];
$lista_don=$_POST['lista_don'];
$nombre=$_POST['nombre'];
$apellidos=$_POST['apellidos'];
$id_nif=$_POST['nif_nie'];
$grado=$_POST['grado'];
$ciclo=$_POST['ciclo'];
$curso_ciclo=$_POST['curso_ciclo'];
$departamento=$_POST['departamento'];
$subidopor=$_POST['subido_por'];
$documentacion="";
$desc= array();

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

if ($grado=="GRADO BÁSICO") $curso="Formación Profesional Básica, en el curso ".$curso_ciclo." de " . $ciclo;
elseif ($grado=="GRADO MEDIO") $curso="Formación Profesional de Grado Medio, en el curso ".$curso_ciclo." de " . $ciclo;
elseif ($grado=="GRADO SUPERIOR")	$curso="Formación Profesional de Grado Superior, en el curso ".$curso_ciclo." de " . $ciclo;

if (isset($_POST["desc"])){
    foreach($_POST["desc"] as $value) {
        $documentacion.=$value."; ";
        $desc[]=$value;
        if ($value!="Certificación de la empresa" &&
            $value!="Certificación de la Tesorería General de la Seguridad Social" &&
            $value!="Certificación de alta en el censo de obligados tributarios" &&
            $value!="Declaración del interesado de las actividades más representativas" &&
            $value!="Certificación de la organización donde se han prestado servicios como voluntario/a o becario/a"){
                $otra_doc.=$value.", ";
            }
    }
    if (substr($otra_doc, -2) === '; ') {
        $otra_doc = substr_replace($otra_doc, "", -2);
    }
    $docs=$_FILES['docs'];
}

$registro= generaRegistro();

if ($mysqli->errno>0) {
    exit("servidor");
}
$mysqli->set_charset("utf8");

$repite_registro=true;
while ($repite_registro){
    $res=$mysqli->query("select * from exencion_fct where registro='$registro'");
    if ($res->num_rows>0){
       $registro= generaRegistro(); 
    }
    else if ($res->num_rows==0){
        $repite_registro=false;
    }
    $res->free();
}

$dirRegistro=substr($registro, 17);

///Parametro de bind sss por la siguiente tabla
//"i": Entero (integer)
//"d": Decimal (double)
//"s": Cadena (string) y fechas
//"b": Blob (para datos binarios)

// Iniciar una transacción para asegurar la integridad de los datos
$mysqli->begin_transaction();

try {
    // Insertar registro en la primera tabla
    $stmt1 = $mysqli->prepare("INSERT INTO exencion_fct (id_nie,fecha_registro,registro,curso,nombre,apellidos,id_nif,
                                                        grado,ciclo,curso_ciclo,departamento) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?,?)");
    $stmt1->bind_param("sssssssssss", $id_nie,$fecha_registro,$registro,$anno_curso,$nombre,$apellidos,$id_nif,
                                                $grado,$ciclo,$curso_ciclo,$departamento);
    
    if ($stmt1->execute() === false) {
        throw new Exception("Error al ejecutar la consulta de inserción: " . $stmt1->error);
    }
    $stmt1->close();
    // Insertar registros en la segunda tabla
    $stmt2 = $mysqli->prepare("INSERT INTO exencion_fct_docs (id_nie, registro, descripcion, ruta, subidopor) VALUES (?, ?, ?, ?, ?)");
    $contador_docs=0;
    for($i=0;$i<count($desc);$i++) {
        $contador_docs=$i+1;
        $indice=sprintf("%02d", $i+1)."_";
        $rutaTb="docs/".$id_nie."/exencion_form_emp"."/".$anno_curso."/".$dirRegistro."/docs"."/".$indice.$_FILES["docs"]["name"][$i];
        $stmt2->bind_param("sssss", $id_nie, $registro, $desc[$i], $rutaTb, $subidopor);
        if ($stmt2->execute() === false) {
            throw new Exception("Error al ejecutar la consulta de inserción: " . $stmt2->error);
        }
    }
    $stmt2->close();
    $rutaCompleta=__DIR__."/../../../docs/".$id_nie."/"."exencion_form_emp/".$anno_curso."/".$dirRegistro."/docs"."/";
    if (!is_dir($rutaCompleta)) {
        mkdir($rutaCompleta, 0777, true);
    }
    $contador_docs=0;
    if (isset($_FILES["docs"])){
        for ($i=0;$i<count($_FILES["docs"]["tmp_name"]);$i++){
            $contador_docs=$i+1;
            $indice=sprintf("%02d", $i+1)."_";
            $nombreDoc=$indice.$_FILES["docs"]["name"][$i];
            if(!move_uploaded_file($_FILES["docs"]["tmp_name"][$i], $rutaCompleta.$nombreDoc))
            {
                // Si hay un error al mover el archivo, eliminar los archivos ya movidos
                $archivosEliminados = glob($rutaCompleta . "*");
                foreach ($archivosEliminados as $archivoEliminado) {
                    unlink($archivoEliminado);
                }
    
                // Eliminar la ruta creada
                rmdir($rutaCompleta);
                rmdir(__DIR__."/../../../docs/".$id_nie."/"."exencion_form_emp/".$anno_curso."/".$dirRegistro."/");
    
                // Revertir la transacción en la base de datos
                $mysqli->rollback();
                $mysqli->close();
    
                // Mostrar mensaje de error o realizar otras acciones necesarias
                exit("error_subida");
            }
        }
    }
    // Confirmar la transacción
    $mysqli->commit();
} catch (Exception $e) {
    // En caso de error, revertir la transacción
    $mysqli->rollback();
    $mysqli->close();
    exit("database ".$e->getMessage());
}
////////////////////////////////////////////////////////////

$ruta=__DIR__."/../../../docs/".$id_nie."/"."exencion_form_emp/".$anno_curso."/".$dirRegistro."/docs"."/";


// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('IES Universidad Laboral');
$pdf->SetTitle('Exención PFE');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('ulaboral, PDF, secretaría, Toledo, Exención PFE');

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
<h2 style="text-align:center"><b>SOLICITUD DE EXENCIÓN DEL MÓDULO DE PERÍODO DE FORMACIÓN EN EMPRESAS</b></h2>
<br><br>
$lista_don $nombre $apellidos, con $num_documento, <b>solicita la exención</b> del Período de Formación en Empresas correspondiente a las enseñanzas de grado $grado de $curso_ciclo curso de $ciclo que se imparte en el centro IES Universidad Laboral de Toledo, en el que está matriculado.  
<br><br>
Así, presenta la documentación establecida en el artículo 25 punto 2 de la Orden de 29 de julio de 2010, de la Consejería de Educación, Ciencia y Cultura, por la que se regula la evaluación, promoción y acreditación académica del alumnado de formación profesional inicial del sistema educativo de la Comunidad Autónoma de Castilla-La Mancha.<br>
$documentacion<br><br>
<p style="text-align:center">$fecha_firma<br>
<img src='$firma' width='35' style='display: block; margin-left: auto; margin-right: auto;'>
<br>
Fdo.: $nombre $apellidos</p>
EOD;

$pdf->SetXY($XInicio,$YInicio);
//$pdf->MultiCell(180,0,$texto,0,'L',0,1,$XInicio,$YInicio,true,0,true,false,0);
$pdf->writeHTMLCell(180, 0, $XInicio, $YInicio, $texto, 0, 1, false, true, 'L', true);


$pdf->SetXY($XInicio,275);
$pdf->Cell(180,0,"SR/A. DIRECTOR/A DEL IES UNIVERSIDAD LABORAL DE TOLEDO",0,0,'L',0,'',1,true,'T','T');

//GENERA EL ARCHIVO NUEVO
$nombre_fichero=$registro . '.pdf';
if(!is_dir(__DIR__."/../../../docs/".$id_nie."/"."exencion_form_emp/".$anno_curso."/".$dirRegistro))mkdir(__DIR__."/../../../docs/".$id_nie."/"."convalidaciones/".$anno_curso."/".$dirRegistro,0777,true);
$ruta=__DIR__."/../../../docs/".$id_nie."/"."exencion_form_emp/".$anno_curso."/".$dirRegistro."/". $nombre_fichero;
$pdf->Output($ruta, 'F');
//FIN GENERA PDF
exit("ok");
