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



$id_nie = $_POST['id_nie'];
$anno_curso = $_POST['anno_curso'];
$fecha_registro=date('Y-m-d');
$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$id_nif = $_POST['id_nif'];
$direccion = $_POST['direccion'];
$cp = $_POST['cp'];
$localidad = $_POST['localidad'];
$provincia = $_POST['provincia'];
$grado = $_POST['grado'];
$ciclo = $_POST['ciclo'];
$curso = $_POST['curso'];
$modalidad = $_POST['modalidad'];
$turno = $_POST['turno'];
$tlf_fijo = $_POST['tlf_fijo'];
$tlf_movil = $_POST['tlf_movil'];
$email = $_POST['email'];
$modulos = $_POST['modulos'];
$matrizMods = json_decode($matrizMods);
$subidopor="usuario";
$desc= array();
$otra_doc="";



if (isset($_POST["desc"])){
    foreach($_POST["desc"] as $value) {
        $desc[]=$value;
        if ($value!="Certificación de estar matriculado en los estudios de Formación Profesional cuya convalidación solicita" &&
            $value!="Fotocopia compulsada de la certificación académica de los estudios realizados" &&
            $value!="Fotocopia compulsada del título") $otra_doc.=$value.", ";
    }
    if (substr($otra_doc, -2) === ', ') {
        $otra_doc = substr_replace($otra_doc, "", -2);
    }
    $docs=$_FILES['docs'];
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

///Parametro de bind sss por la siguiente tabla
//"i": Entero (integer)
//"d": Decimal (double)
//"s": Cadena (string) y fechas
//"b": Blob (para datos binarios)

// Iniciar una transacción para asegurar la integridad de los datos
$mysqli->begin_transaction();

try {
    // Insertar registro en la primera tabla
    $stmt1 = $mysqli->prepare("INSERT INTO convalidaciones (id_nie,fecha_registro,registro,curso,nombre,apellidos,id_nif,direccion,localidad,provincia,cp,tlf_fijo,tlf_movil,email,
                                                            grado,ciclo,curso_ciclo,modalidad,turno,modulos) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt1->bind_param("ssssssssssssssssssss", $id_nie,$fecha_registro,$registro,$anno_curso,$nombre,$apellidos,$id_nif,$direccion,
                                                $localidad,$provincia,$cp,$tlf_fijo,$tlf_movil,$email,$grado,$ciclo,$curso,$modalidad,$turno,$modulos);
    $stmt1->execute();
    $stmt1->close();
    // Insertar registros en la segunda tabla
    $stmt2 = $mysqli->prepare("INSERT INTO convalidaciones_docs (id_nie, registro, descripcion, ruta, subidopor) VALUES (?, ?, ?, ?, ?)");
    $contador_docs=0;
    for($i=0;$i<count($desc);$i++) {
        $contador_docs=$i+1;
        $indice=sprintf("%02d", $i+1)."_";
        $rutaTb="docs/".$id_nie."/convalidaciones"."/".$anno_curso."/".$dirRegistro."/docs"."/".$indice.$_FILES["docs"]["name"][$i];
        $stmt2->bind_param("sssss", $id_nie, $registro, $desc[$i], $rutaTb, $subidopor);
        $stmt2->execute();
        
    }
    if (isset($_FILES["pasaporte"]) || isset($_FILES["dni_anverso"])){
        $check2=true;
        $indice=sprintf("%02d", $contador_docs+1)."_";
        $descDoc="Documento de identificación";
        $rutaTb="docs/".$id_nie."/convalidaciones"."/".$anno_curso."/".$dirRegistro."/docs"."/".$indice."documento_identificacion.pdf";
        $stmt2->bind_param("sssss", $id_nie,$registro,$descDoc , $rutaTb, $subidopor);
        $stmt2->execute();
    }
    $stmt2->close();
    $rutaCompleta=__DIR__."/../../../docs/".$id_nie."/"."convalidaciones/".$anno_curso."/".$dirRegistro."/docs"."/";
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
                rmdir(__DIR__."/../../../docs/".$id_nie."/"."convalidaciones/".$anno_curso."/".$dirRegistro."/");
    
                // Revertir la transacción en la base de datos
                $mysqli->rollback();
    
                // Mostrar mensaje de error o realizar otras acciones necesarias
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
        $pdf_docIdent->Output($rutaCompleta.sprintf("%02d", $contador_docs+1)."_"."documento_identificacion.pdf", 'F');
    }

    //Inserta los módulos en convalidaciones_modulos
    $stmt3 = $mysqli->prepare("INSERT INTO convalidaciones_modulos (id_nie, registro, modulo) VALUES (?, ?, ?)");
    for($i=0;$i<count($matrizMods);$i++) {
        $stmt3->bind_param("sss", $id_nie, $registro, $matrizMods[$i]);
        $stmt3->execute();
    }
    $stmt3->close();
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

$meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
$fecha_actual=getdate();
$dia=$fecha_actual["mday"];
$mes=$meses[$fecha_actual["mon"]-1];
$anno=$fecha_actual["year"];
$anno = substr($anno, -2);

class MYPDF extends TCPDF {

	//Page header
	public function Header() {
		// Logo
		$image_file = '../../../recursos/logo_ccm.jpg';
		$this->Image($image_file, 10, 10, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$image_file = '../../../recursos/mini_escudo.jpg';
		$this->Image($image_file, 140, 10, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

		$this->SetFont('helvetica', 'B', 14);
		$this->SetXY(0,10);
		$this->Cell(0,0,"CONVALIDACIÓN",0,0,'C',0,'',1,false,'T','T');
			
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
$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('IES Universidad Laboral');
$pdf->SetTitle('Convalidaciones');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('ulaboral, PDF, secretaría, Toledo, Convalidaciones');

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

$pdf->SetFont('dejavusans', '', 8, '', true);
$pdf->setFillColor(200);  //Relleno en gris
//Padding dentro de la celda del texto
$pdf->setCellPaddings(0,0,0,0);
//Interlineado
$pdf->setCellHeightRatio(1);


$pdf->AddPage();


$YInicio+=27;
$XInicioRotulo=17;
$XInicio=12;

//DATOS DEL ALUMNO
$YInicio+=9;
$pdf->RoundedRect(10,$YInicio-1,185,35,2,'1111','','','');
$pdf->SetXY($XInicioRotulo,$YInicio);
$pdf->SetFont('dejavusans', 'B', 10, '', true);
$pdf->Cell(0,0,"DATOS DEL ALUMNO",0,0,'L',0,'',1,false,'','');

$YInicio+=6;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Apellidos y Nombre",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(175,0,$apellidos.", ".$nombre,0,0,'L',0,'',1,false,'T','T');

$YInicio+=4;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"NIF/NIE",0,0,'L',0,'',1,false,'','');
$pdf->SetX(32);
$pdf->Cell(0,0,"Email",0,0,'L',0,'',1,false,'','');
$pdf->SetX(115);
$pdf->Cell(0,0,"Tlf. Fijo",0,0,'L',0,'',1,false,'','');
$pdf->SetX(155);
$pdf->Cell(0,0,"Tlf. Móvil",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,$id_nif,0,0,'L',0,'',1,false,'','');
$pdf->SetX(32);
$pdf->Cell(0,0,$email,0,0,'L',0,'',1,false,'','');
$pdf->SetX(115);
$pdf->Cell(0,0,$tlf_fijo,0,0,'L',0,'',1,false,'','');
$pdf->SetX(155);
$pdf->Cell(0,0,$tlf_movil,0,0,'L',0,'',1,false,'','');

$YInicio+=4;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Dirección (calle, plaza, número, escalera, puerta ...",0,0,'L',0,'',1,false,'','');
$pdf->SetX(182);
$pdf->Cell(0,0,"CP",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(168,0,$direccion,0,0,'L',0,'',1,false,'T','T');
$pdf->SetX(182);
$pdf->Cell(10,0,$cp,0,0,'L',0,'',1,false,'T','T');

$YInicio+=4;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Localidad",0,0,'L',0,'',1,false,'','');
$pdf->SetX(77);
$pdf->Cell(0,0,"Provincia",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(61,0,$localidad,0,0,'L',0,'',1,false,'','');
$pdf->SetX(77);
$pdf->Cell(64,0,$provincia,0,0,'L',0,'',1,false,'','');

$YInicio+=9;
$pdf->RoundedRect(10,$YInicio-1,185,22,2,'1111','','','');
$pdf->SetXY($XInicioRotulo,$YInicio);
$pdf->SetFont('dejavusans', 'B', 10, '', true);
$pdf->Cell(0,0,"DATOS ACADÉMICOS Y MÓDULOS QUE SOLICITA CONVALIDAR",0,0,'L',0,'',1,false,'','');

$YInicio+=6;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', '', 8, '', true);
$pdf->Cell(0,0,"Está matriculado en el ciclo formativo de:",0,0,'L',0,'',1,false,'','');
$YInicio+=3;
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->SetXY($XInicio,$YInicio);
$pdf->Cell(0,0,"Grado          Denominación                                      Curso  Turno          Modalidad     ",0,0,'L',0,'',1,false,'','');
$pdf->SetX(15);
$pdf->Cell(0,0,"Denominación",0,0,'L',0,'',1,false,'','');
$pdf->SetX(65);
$pdf->Cell(0,0,"Curso",0,0,'L',0,'',1,false,'','');
$pdf->SetX(72);
$pdf->Cell(0,0,"Turno",0,0,'L',0,'',1,false,'','');
$pdf->SetX(87);
$pdf->Cell(0,0,"Modalidad",0,0,'L',0,'',1,false,'','');
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->Cell(0,0,$grado,0,0,'L',0,'',1,false,'','');
$pdf->setX(15);
$pdf->Cell(0,0,$ciclo,0,0,'L',0,'',1,false,'','');
$pdf->setX(65);
$pdf->Cell(0,0,$curso,0,0,'L',0,'',1,false,'','');
$pdf->setX(72);
$pdf->Cell(0,0,$turno,0,0,'L',0,'',1,false,'','');
$pdf->setX(87);
$pdf->Cell(0,0,$modalidad,0,0,'L',0,'',1,false,'','');

$YInicio+=6;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Solicita la convalidación de los siguientes módulos:",0,0,'L',0,'',1,false,'','');
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$YInicio+=3;
$pdf->Cell(0,0,$modulos,0,0,'L',0,'',1,false,'','');

if (count($desc)>0){
    $docs_aportados="";
    for ($i=0;$i<count($desc);$i++){
        $docs_aportados.=$desc[$i];
        if ($i<count($desc)-1)$docs_aportados.=", ";
    }
    $YInicio+=6;
    $pdf->SetXY($XInicio,$YInicio);
    $pdf->SetFont('dejavusans', 'U', 8, '', true);
    $pdf->Cell(0,0,"Y aporta la siguiente documentación:",0,0,'L',0,'',1,false,'','');
    $pdf->SetFont('dejavusans', 'B', 8, '', true);
    $YInicio+=3;
    $pdf->Cell(0,0,$docs_aportados,0,0,'L',0,'',1,false,'','');
}

$pdf->setCellHeightRatio(1.4);
$pdf->SetXY(65,177);
$pdf->Cell(0,0,"Toledo",0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(99,177);
$pdf->Cell(0,0,$dia,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(120,177);
$pdf->Cell(0,0,$mes,0,0,'L',0,'',1,true,'T','T');
$pdf->SetXY(151,177);
$pdf->Cell(0,0,$anno,0,0,'L',0,'',1,true,'T','T');

$pdf->SetFont('dejavusans', '', 5, '', true);
$pdf->SetXY(30,195);
$pdf->Cell(0,0,$registro,0,0,'L',0,'',1,true,'T','T');

//GENERA EL ARCHIVO NUEVO
$nombre_fichero=$registro . '.pdf';
if(!is_dir(__DIR__."/../../../docs/".$id_nie."/"."convalidaciones/".$anno_curso."/".$dirRegistro))mkdir(__DIR__."/../../../docs/".$id_nie."/"."convalidaciones/".$anno_curso."/".$dirRegistro0777);
$ruta=__DIR__."/../../../docs/".$id_nie."/"."convalidaciones/".$anno_curso."/".$dirRegistro."/". $nombre_fichero;
$pdf->Output($ruta, 'F');
//FIN GENERA PDF
exit("ok");