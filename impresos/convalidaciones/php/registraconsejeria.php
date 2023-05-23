<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("../../../php/conexion.php");

if ($mysqli->errno>0) {
    exit("servidor");
}

$mysqli->set_charset("utf8");
require_once(__DIR__.'/../../../php/tcpdf/config/tcpdf_config_alt.php');
require_once(__DIR__.'/../../../php/tcpdf/tcpdf.php');

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
    return "iesulabto_convco_".date('dmY')."_".$array[0].$array[1].$array[2].$array[3].$array[4].$array[5].$array[6].$array[7];   
}



$id_nie = urldecode($_POST['id_nie']);
$anno_curso = urldecode($_POST['curso']);
$fecha_registro=date('Y-m-d');
$formulario = urldecode($_POST['formulario']);
$nombre = urldecode($_POST['nombre']);
$apellidos = urldecode($_POST['apellidos']);
$id_nif = urldecode($_POST['id_nif']);
$direccion = urldecode($_POST['direccion']);
$cp = urldecode($_POST['cp']);
$localidad = urldecode($_POST['localidad']);
$provincia = urldecode($_POST['provincia']);
$ley="LOE";
$grado = urldecode($_POST['grado']);
$ciclo = urldecode($_POST['ciclo']);
$modulos = urldecode($_POST['modulos']);
$estudios_superados=urldecode($_POST['estudios_superados']);
$desc= array();
$estudios_aportados="";
$otra_doc="";
$check1=false;
$check2=false;
$check3=false;
$check4=false;
$check5=false;

if (isset($_POST["desc"])){
    foreach($_POST["desc"] as $value) {
        $desc[]=urldecode($value);
        if (urldecode($value)=="Certificación de estar matriculado en los estudios de Foprmación Profesional cuya convalidación solicita") $check1=true;
        elseif(urldecode($value)=="Documento de identificación (DNI/NIE)" || urldecode($value)=="Documento de identificación (Pasaporte)") $check2=true;
        elseif(urldecode($value)=="Fotocopia compulsada de la certificación académica de los estudios realizados") $check3=true;
        elseif(urldecode($value)=="Fotocopia compulsada del título") $check4=true;
        else{
            $check5=true;
            $otra_doc.=urldecode($value).", ";
        } 
    }
    if (substr($otra_doc, -2) === ', ') {
        $otra_doc = substr_replace($otra_doc, "", -2);
    }
    $docs=$_FILES['docs'];
}
$imageData = urldecode($_POST['firma']);
if (!is_dir(__DIR__."/../../../docs/tmp"))mkdir(__DIR__."/../../../docs/tmp",0777);
$tempFile = tempnam(__DIR__."/../../../docs/tmp", 'canvas_'. session_id() . '.png');
file_put_contents($tempFile, base64_decode(str_replace('data:image/png;base64,', '', $imageData)));
$firma = $tempFile;

$repite_registro=true;
while($repite_registro){
    $registro=generaRegistro();
    $vReg=$mysqli->query("select * from convalidaciones where registro='$registro'");
    if ($mysqli->errno>0){
        unlink($tempFile);
        exit("database");
    }
    if ($vReg->num_rows==0) {
        $repite_registro=false;
    }
    $vReg->free();
}
$dirRegistro=substr($registro, 17);

//ver esto que es la hostia made in chatGPT////////////////////////////////////////////////////////////
///Parametro de bind sss por la siguiente tabla
//"i": Entero (integer)
//"d": Decimal (double)
//"s": Cadena (string) y fechas
//"b": Blob (para datos binarios)

// Iniciar una transacción para asegurar la integridad de los datos
$mysqli->begin_transaction();

try {
    // Insertar registro en la primera tabla
    $stmt1 = $mysqli->prepare("INSERT INTO convalidaciones (id_nie,organismo_destino,fecha_registro,registro,curso,nombre,apellidos,id_nif,direccion,localidad,provincia,cp,
                                                            grado,ciclo,ley,estudios_superados,modulos) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt1->bind_param("sssssssssssssssss", $id_nie,$formulario,$fecha_registro,$registro,$anno_curso,$nombre,$apellidos,$id_nif,$direccion,
                                                $localidad,$provincia,$cp,$grado,$ciclo,$ley,$estudios_superados,$modulos);
    $stmt1->execute();
    $stmt1->close();
    
    // Insertar registros en la segunda tabla
    $stmt2 = $mysqli->prepare("INSERT INTO convalidaciones_docs (registro, descripcion, ruta) VALUES (?, ?, ?)");
    $contador_docs=1;
    for($i=0;$i<count($desc);$i++) {
        $contador_docs=$i+1;
        $indice=sprintf("%02d", $i+1)."_";
        $rutaTb="docs/".$id_nie."/convalidaciones"."/".$anno_curso."/".$dirRegistro."/docs"."/".$indice.$docs[$i]["name"];
        $stmt2->bind_param("sss", $registro, $desc[$i], $rutaTb);
        $stmt2->execute();
        
    }
    exit("555555555555");
    if (isset($_FILES["pasaporte"]) || isset($_FILES["dni_anverso"])){
        $indice=sprintf("%02d", $contador_docs+1)."_";
        $rutaTb="docs/".$id_nie."/convalidaciones"."/".$anno_curso."/".$dirRegistro."/docs"."/".$indice."documento_identificacion.pdf";
        $stmt2->bind_param("sss", $registro, "Documento de identificación", $rutaTb);
        $stmt2->execute();
    }
    $stmt2->close();
    $rutaCompleta=__DIR__."/../../../docs/".$id_nie."/"."convalidaciones/".$anno_curso."/".$dirRegistro."/docs"."/";
    if (!is_dir($rutaCompleta)) {
        mkdir($rutaCompleta, 0777, true);
    }
    $contador_docs=1;
    if (isset($_FILES["docs"])){
        for ($i=0;$i<count($_FILES["docs"]["tmp_name"]);$i++){
            $contador_docs=$i+1;
            $indice=sprintf("%02d", $i+1)."_";
            $nombreDoc=$indice.$_FILES["docs"]["name"][$i];
            try {
                move_uploaded_file($_FILES["docs"]["tmp_name"][$i], $rutaCompleta.$nombreDoc);
            } catch (Exception $ex) {
                // Si hay un error al mover el archivo, eliminar los archivos ya movidos
                $archivosEliminados = glob($rutaCompleta . "*");
                foreach ($archivosEliminados as $archivoEliminado) {
                    unlink($archivoEliminado);
                }
    
                // Eliminar la ruta creada
                rmdir($rutaCompleta);
                rmdir(__DIR__."/../../../docs/".$id_nie."/"."convalidaciones/".$anno_curso."/".$dirRegistro."/");
    
                // Revertir la transacción en la base de datos
                $mysqli->rollback();
    
                // Mostrar mensaje de error o realizar otras acciones necesarias
                unlink($tempFile);
                exit("error_subida");
            }
        }
    }
    if (isset($_FILES["pasaporte"])){
        $pdf_docIdent = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf_docIdent->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf_docIdent->AddPage();
        $pdf_docIdent->Image($_FILES["pasaporte"]["tmp_name"], 20, 20, 90, 0);
        $pdf_docIdent->Output($rutaCompleta.sprintf("%02d", $contador_docs+1)."_"."documento_identificacion.pdf", 'F');
    }
    elseif(isset($_FILES["dni_anverso"])){
        $pdf_docIdent = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf_docIdent->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf_docIdent->AddPage();
        $pdf_docIdent->Image($_FILES["dni_anverso"]["tmp_name"], 20, 20, 90, 0);
        $pdf_docIdent->Image($_FILES["dni_reverso"]["tmp_name"], 20, 80, 90, 0);
        $pdf_docIdent->Output($rutaCompleta, 'F');
        $pdf_docIdent->Output($rutaCompleta.sprintf("%02d", $contador_docs+1)."_"."documento_identificacion.pdf", 'F');
    }

    // Confirmar la transacción
    $mysqli->commit();
} catch (Exception $e) {
    // En caso de error, revertir la transacción
    $mysqli->rollback();
    unlink($tempFile);
    exit("database");
}
////////////////////////////////////////////////////////////

$ruta=__DIR__."/../../../docs/".$id_nie."/"."convalidaciones/".$anno_curso."/".$dirRegistro."/docs"."/";




//////////////PDF///////////////////////

//GENERA EL PDF Y LO GUARDA EN EL SERVIDOR

$meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
$fecha_actual=getdate();
$dia=$fecha_actual["mday"];
$mes=$meses[$fecha_actual["mon"]-1];
$anno=$fecha_actual["year"];

class MYPDF extends TCPDF {
    // Constructor
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false) {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
    }

	//Page header
	public function Header() {}
}

// create new PDF document
$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('IES Universidad Laboral');
$pdf->SetTitle('Convalidaciones Centro Educativo y Ministerio');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('ulaboral, PDF, secretaría, Toledo, Convalidaciones Centro Educativo y Ministerio');

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT, PDF_MARGIN_HEADER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------

$pdf->setFontSubsetting(true);

$pdf->SetFont('dejavusans', '', 7, '', true);
$pdf->setFillColor(200);  //Relleno en gris
//Padding dentro de la celda del texto
$pdf->setCellPaddings(0,0,0,0);
//Interlineado
$pdf->setCellHeightRatio(1.5);


$pdf->AddPage();
// get the current page break margin
$bMargin = $pdf->getBreakMargin();
// get current auto-page-break mode
$auto_page_break = $pdf->getAutoPageBreak();
// disable auto-page-break
$pdf->SetAutoPageBreak(false, 0);
// set background image
$pdf->Image("../recursos/consejeria.jpg", 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
// restore auto-page-break status
$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
// set the starting point for the page content
$pdf->setPageMark();


$pdf->SetXY(33,50);
$pdf->Cell(0,0,$nombre . " " . $apellidos,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(122,54);
$pdf->Cell(0,0,$id_nif,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(35,58);
$pdf->Cell(0,0,$direccion,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(125,58);
$pdf->Cell(54,0,$localidad,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(35,62);
$pdf->Cell(0,0,$cp,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(85,62);
$pdf->Cell(40,0,$provincia,0,0,'L',0,'',1,true,'T','T');

$pdf->SetXY(90,79);
$pdf->Cell(0,0,"IES UNIVERSIDAD LABORAL",0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(65,83);
$pdf->Cell(0,0,"AVDA. EUROPA, 28",0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(56,87);
$pdf->Cell(0,0,"45003",0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(116,87);
$pdf->Cell(0,0,"TOLEDO",0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(40,91);
$pdf->Cell(0,0,$grado,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(83,91);
$pdf->Cell(0,0,$ciclo,0,0,'L',0,'',1,true,'T','T');

$pdf->SetXY(45,103);
$pdf->Cell(125,0,$estudios_superados,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(147,134);
$pdf->Cell(0,0,"X",0,0,'L',0,'',1,true,'T','T');

//$pdf->setCellHeightRatio(1.4);
$pdf->SetXY(30,124);
$pdf->MultiCell(150,0,$modulos,0,'L',0,1,'','',true,0,false,false,0);

if($check1){
    $pdf->SetXY(41.5,144);
    $pdf->MultiCell(150,0,'x',0,'L',0,1,'','',true,0,false,false,0);
}
if($check2){
    $pdf->SetXY(41.5,152);
    $pdf->MultiCell(150,0,'x',0,'L',0,1,'','',true,0,false,false,0);
}
if($check3){
    $pdf->SetXY(41.5,156);
    $pdf->MultiCell(150,0,'x',0,'L',0,1,'','',true,0,false,false,0);
}
if($check4){
    $pdf->SetXY(41.5,161.5);
    $pdf->MultiCell(150,0,'x',0,'L',0,1,'','',true,0,false,false,0);
}
if($check5){
    $pdf->SetXY(41.5,165.5);
    $pdf->MultiCell(150,0,'x',0,'L',0,1,'','',true,0,false,false,0);
    $pdf->SetXY(40,170);
    $pdf->MultiCell(150,0,$otra_doc,0,'L',0,1,'','',true,0,false,false,0);
}



/////Tratamiento de la imagen
$pdf->Image($firma, 120, 183, 20, 0, '', '', '', false, 300);


$pdf->setCellHeightRatio(1.4);
$pdf->SetXY(60,177);
$pdf->Cell(0,0,"Toledo",0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(96,177);
$pdf->Cell(0,0,$dia,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(115,177);
$pdf->Cell(0,0,$mes,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(153,177);
$pdf->Cell(0,0,$anno,0,0,'L',0,'',1,true,'T','T');


$pdf->SetFont('dejavusans', '', 5, '', true);
$pdf->SetXY(30,195);
$pdf->Cell(0,0,$registro,0,0,'L',0,'',1,true,'T','T');


//GENERA EL ARCHIVO NUEVO
$nombre_fichero=$registro . '.pdf';
if (!is_dir(__DIR__."/../../../docs/".$id_nie))mkdir(__DIR__."/../../../docs/".$id_nie,0777);
if(!is_dir(__DIR__."/../../../docs/".$id_nie."/convalidaciones"))mkdir(__DIR__."/../../../docs/".$id_nie."/convalidaciones",0777);
if(!is_dir(__DIR__."/../../../docs/".$id_nie."/convalidaciones"."/".$anno_curso))mkdir(__DIR__."/../../../docs/".$id_nie."/convalidaciones"."/".$anno_curso,0777);
$ruta=__DIR__."/../../../docs/".$id_nie."/"."convalidaciones/".$anno_curso."/".$dirRegistro."/". $nombre_fichero;
$pdf->Output($ruta, 'F');
//FIN GENERA PDF
unlink($tempFile);
exit("ok");