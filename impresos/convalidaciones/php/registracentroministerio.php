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
    return "iesulabto_conval_".date('dmY')."_".$array[0].$array[1].$array[2].$array[3].$array[4].$array[5].$array[6].$array[7];   
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
$tlf_fijo = urldecode($_POST['tlf_fijo']);
$tlf_movil = urldecode($_POST['tlf_movil']);
$email = urldecode($_POST['email']);
$ley="LOE";
$grado = urldecode($_POST['grado']);
$ciclo = urldecode($_POST['ciclo']);
$modulos = urldecode($_POST['modulos']);
$desc= array();
$estudios_aportados="";
if (isset($_POST["desc"])){
    foreach($_POST["desc"] as $value) {
        $desc[]=urldecode($value);
    }
    $docs=$_FILES['docs'];
    
    $estudios_aportados=$desc[0];
    for($i=1;$i<count($desc);$i++){
        $estudios_aportados.=", ".$desc[$i];
    }
}


$repite_registro=true;
while($repite_registro){
    $registro=generaRegistro();
    $vReg=$mysqli->query("select * from convalidaciones where registro='$registro'");
    if ($mysqli->errno>0){
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
                                                            tlf_fijo,tlf_movil,email,grado,ciclo,ley,modulos) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt1->bind_param("sssssssssssssssssss", $id_nie,$formulario,$fecha_registro,$registro,$anno_curso,$nombre,$apellidos,$id_nif,$direccion,
                                                $localidad,$provincia,$cp,$tlf_fijo,$tlf_movil,$email,$grado,$ciclo,$ley,$modulos);
    $stmt1->execute();
    $stmt1->close();
    
    // Insertar registros en la segunda tabla
    $stmt2 = $mysqli->prepare("INSERT INTO convalidaciones_docs (registro, descripcion, ruta) VALUES (?, ?, ?)");exit("aqui");
    for($i=0;$i<count($desc);$i++) {
        $indice=sprintf("%02d", $i+1)."_";
        $stmt2->bind_param("sss", $registro, $desc[$i], "docs/".$id_nie."/convalidaciones"."/".$anno_curso."/".$dirRegistro."/docs"."/".$indice.$docs[$i]["name"]);
        $stmt2->execute();
    }
    $stmt2->close();
    $rutaCompleta=__DIR__."/../../../docs/".$id_nie."/"."convalidaciones/".$anno_curso."/".$dirRegistro."/docs"."/";
    if (!is_dir($rutaCompleta)) {
        mkdir($rutaCompleta, 0777, true);
    }
    if (isset($_FILES["docs"])){
        for ($i=0;$i<count($_FILES["docs"]["tmp_name"]);$i++){
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
                exit("error_subida");
            }
        }
    }

    // Confirmar la transacción
    $mysqli->commit();
} catch (Exception $e) {
    // En caso de error, revertir la transacción
    $mysqli->rollback();
    exit("database");
}
////////////////////////////////////////////////////////////

$ruta=__DIR__."/../../../docs/".$id_nie."/"."convalidaciones/".$anno_curso."/".$dirRegistro."/docs"."/";




//////////////PDF///////////////////////

//GENERA EL PDF Y LO GUARDA EN EL SERVIDOR

class MYPDF extends TCPDF {

	//Page header
	public function Header() {}
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('IES Universidad Laboral');
$pdf->SetTitle('Convalidaciones Ministerio');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('ulaboral, PDF, secretaría, Toledo, Convalidaciones Ministerio');

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
$pdf->Image("../recursos/centroministerio.jpg", 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
// restore auto-page-break status
$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
// set the starting point for the page content
$pdf->setPageMark();


$meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
$fecha_actual=getdate();
$dia=$fecha_actual["mday"];
$mes=$meses[$fecha_actual["mon"]-1];
$anno=$fecha_actual["year"];


$pdf->SetXY(58,46.5);
$pdf->Cell(0,0,$id_nif,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(105,46.5);
$pdf->Cell(0,0,$nombre,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(42,51);
$pdf->Cell(0,0,$apellidos,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(42,55);
$pdf->Cell(0,0,$direccion,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(48,59);
$pdf->Cell(0,0,$cp,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(74,59);
$pdf->Cell(54,0,$localidad,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(145,59);
$pdf->Cell(40,0,$provincia,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(38,63);
$pdf->Cell(0,0,$tlf_fijo,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(78,63);
$pdf->Cell(0,0,$tlf_movil,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(130,63);
$pdf->Cell(52,0,$email,0,0,'L',0,'',1,true,'T','T');

$pdf->SetXY(50,81);
$pdf->Cell(0,0,"IES UNIVERSIDAD LABORAL",0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(50,85);
$pdf->Cell(0,0,"AVDA. EUROPA, 28",0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(48,89);
$pdf->Cell(0,0,"45003",0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(80,89);
$pdf->Cell(0,0,"TOLEDO",0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(150,89);
$pdf->Cell(0,0,"TOLEDO",0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(40,93);
$pdf->Cell(0,0,"925223400",0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(73,93);
$pdf->Cell(0,0,"925222454",0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(125,93);
$pdf->Cell(0,0,"45003796.ies@edu.jccm.es",0,0,'L',0,'',1,true,'T','T');

$pdf->SetXY(18,111);
$pdf->Cell(125,0,"Grado " . $grado . " de " . $ciclo,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(166,115);
$pdf->Cell(0,0,"X",0,0,'L',0,'',1,true,'T','T');

$pdf->setCellHeightRatio(1.3);
$pdf->SetXY(15,127);
$pdf->MultiCell(175,0,$estudios_aportados,0,'L',0,1,'','',true,0,false,false,0);

$pdf->setCellHeightRatio(1.4);
$pdf->SetXY(15,150);
$pdf->MultiCell(175,0,$modulos,0,'L',0,1,'','',true,0,false,false,0);

$pdf->setCellHeightRatio(1.4);
$pdf->SetXY(147,181);
$pdf->Cell(0,0,$dia . " de " . $mes . " de " . $anno,0,0,'L',0,'',1,true,'T','T');

$pdf->SetXY(40,198);
$pdf->Cell(0,0,"Luis Corrales Mariblanca",0,0,'L',0,'',1,true,'T','T');


//GENERA EL ARCHIVO NUEVO
$nombre_fichero=$registro . '.pdf';
if (!is_dir(__DIR__."/../../../docs/".$id_nie))mkdir(__DIR__."/../../../docs/".$id_nie,0777);
if(!is_dir(__DIR__."/../../../docs/".$id_nie."/convalidaciones"))mkdir(__DIR__."/../../../docs/".$id_nie."/convalidaciones",0777);
if(!is_dir(__DIR__."/../../../docs/".$id_nie."/convalidaciones"."/".$anno_curso))mkdir(__DIR__."/../../../docs/".$id_nie."/convalidaciones"."/".$anno_curso,0777);
$ruta=__DIR__."/../../../docs/".$id_nie."/"."convalidaciones/".$anno_curso."/".$dirRegistro."/". $nombre_fichero;
$pdf->Output($ruta, 'F');
//FIN GENERA PDF
exit("envio_ok ");