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
    if($_POST['b1_modalidad']=='Ciencias y Tecnología') return "iesulabto_pm1bac_".date('dmY')."_".$array[0].$array[1].$array[2].$array[3].$array[4].$array[5].$array[6].$array[7];   
    else if($_POST['b1_modalidad']=='Humanidades y Ciencias Sociales') return "iesulabto_pm1bah_".date('dmY')."_".$array[0].$array[1].$array[2].$array[3].$array[4].$array[5].$array[6].$array[7];   
    else if($_POST['b1_modalidad']=='General') return "iesulabto_pm1bag_".date('dmY')."_".$array[0].$array[1].$array[2].$array[3].$array[4].$array[5].$array[6].$array[7];   
}


$curso=$_POST['curso'];
$anno_curso=$_POST['anno_curso'];
$anno_curso_premat=$_POST['anno_curso_premat'];
$id_nie=$_POST['id_nie'];
$email=$_POST['email'];
$apellidos=$_POST['apellidos'];
$nombre=$_POST['nombre'];
$fecha_nac=substr($_POST['fecha_nac'],6,4).'/'.substr($_POST['fecha_nac'],3,2).'/'.substr($_POST['fecha_nac'],0,2);
$fecha_nac=date('Y-m-d',strtotime($fecha_nac));
$sexo=$_POST['sexo'];
$email_alumno=$_POST['email_alumno'];
$telef_alumno=$_POST['telef_alumno'];
$curso_actual="4º ESO";
$grupo_curso_actual=$_POST['sel_grupo_curso_act'];
$tutor1=$_POST['tutor1'];
$email_tutor1=$_POST['email_tutor1'];
$tlf_tutor1=$_POST['tlf_tutor1'];
$tutor2=$_POST['tutor2'];
$email_tutor2=$_POST['email_tutor2'];
$tlf_tutor2=$_POST['tlf_tutor2'];
$fecha_registro=date('Y-m-d');


//Parte especifica de BACH
$modalidad=$_POST['b1_modalidad'];	
$primer_idioma=$_POST['primer_idioma'];
$religion=$_POST['religion'];
$obligatoria1=$_POST['obligatoria1'];
$obligatoria2=$_POST['obligatoria2'];
$obligatoria3=$_POST['obligatoria3'];	
$optativa1=$_POST['optativa1'];
$optativa2=$_POST['optativa2'];
$optativa3=$_POST['optativa3'];
$optativa4=$_POST['optativa4'];
$optativa5=$_POST['optativa5'];
$optativa6=$_POST['optativa6'];
$optativa7=$_POST['optativa7'];
$optativa8=$_POST['optativa8'];
$optativa9=$_POST['optativa9'];
$optativa10=$_POST['optativa10'];
$optativa11=$_POST['optativa11'];
$optativa12=$_POST['optativa12'];
$optativa13=$_POST['optativa13'];
$optativa14=$_POST['optativa14'];
$optativa15=$_POST['optativa15'];
$optativa16=$_POST['optativa16'];
$optativa17=$_POST['optativa17'];


$registro=generaRegistro();
$repite_registro=true;
while ($repite_registro){
    $res=$mysqli->query("select * from premat_bach where registro='$registro'");
    if ($mysqli->errno>0) exit("servidor");
    if ($res->num_rows>0){
       $registro= generaRegistro(); 
    }
    else if ($res->num_rows==0){
        $repite_registro=false;
    }
    $res->free();
}


$mysqli->query("delete from premat_bach where id_nie='$id_nie' and curso='$anno_curso'");
$mysqli->query("delete from premat_eso where id_nie='$id_nie' and curso='$anno_curso'");

$mysqli->query("insert into premat_bach (id_nie,
                                        registro,
                                        fecha_registro,
                                        curso,
                                        curso_premat,
                                        grupo,
                                        apellidos,
                                        nombre,
                                        email,
                                        fecha_nac,
                                        sexo,
                                        telef_alumno,
                                        tutor1,
                                        email_tutor1,
                                        tlf_tutor1,
                                        tutor2,
                                        email_tutor2,
                                        tlf_tutor2,
                                        curso_actual,
                                        grupo_curso_actual,
                                        modalidad,
                                        materia1,
                                        materia2,
                                        materia3,
                                        materia4,
                                        materia5,
                                        materia6,
                                        materia7,
                                        materia8,
                                        materia9,
                                        materia10,
                                        materia11,
                                        materia12,
                                        materia13,
                                        materia14,
                                        materia15,
                                        materia16,
                                        materia17,
                                        materia18,
                                        materia19,
                                        materia20,
                                        materia21,
                                        materia22) 
                                        values ('$id_nie',
                                        '$registro',
                                        '$fecha_registro',
                                        '$anno_curso',
                                        '$anno_curso_premat',
                                        '$curso',
                                        '$apellidos',
                                        '$nombre',
                                        '$email_alumno',
                                        '$fecha_nac',
                                        '$sexo',
                                        '$telef_alumno',
                                        '$tutor1',
                                        '$email_tutor1',
                                        '$tlf_tutor1',
                                        '$tutor2',
                                        '$email_tutor2',
                                        '$tlf_tutor2',
                                        '$curso_actual',
                                        '$grupo_curso_actual',
                                        '$modalidad',
                                        '$primer_idioma',
                                        '$religion',
                                        '$obligatoria1',
                                        '$obligatoria2',
                                        '$obligatoria3',
                                        '$optativa1',
                                        '$optativa2',
                                        '$optativa3',
                                        '$optativa4',
                                        '$optativa5',
                                        '$optativa6',
                                        '$optativa7',
                                        '$optativa8',
                                        '$optativa9',
                                        '$optativa10',
                                        '$optativa11',
                                        '$optativa12',
                                        '$optativa13',
                                        '$optativa14',
                                        '$optativa15',
                                        '$optativa16',
                                        '$optativa17')");
if ($mysqli->errno>0){
    exit("registro_erroneo ".$mysqli->errno);
}

//GENERA EL PDF Y LO GUARDA EN EL SERVIDOR

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
		$this->Cell(0,0,"P R E M A T R Í C U L A",0,0,'C',0,'',1,false,'T','T');
			
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
$pdf->SetTitle('Impreso Prematrícula');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('ulaboral, PDF, secretaría, Toledo, Impreso Prematrícula');

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

//Curso y año
$html1 = <<<HTML1
<h3>$curso</h3><br>
<h3>$modalidad</h3><br>
<h4>Curso $anno_curso_premat</h4><br>
HTML1;

$YInicio=40;

$pdf->RoundedRect(70,$YInicio,70,20,2,'1111','','','');
$pdf->writeHTMLCell(0, 0, '', $YInicio+2, $html1, 0, 1, false, true, 'C', true);

//Padding dentro de la celda del texto
$pdf->setCellPaddings(0,0,0,0);
//Interlineado
$pdf->setCellHeightRatio(1);

$YInicio+=24;
$XInicioRotulo=17;
$XInicio=12;

//DATOS DEL ALUMNO
$YInicio+=8;
$pdf->RoundedRect(10,$YInicio-1,185,22,2,'1111','','','');
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
$pdf->Cell(0,0,"Fecha Nacimiento",0,0,'L',0,'',1,false,'','');
$pdf->SetX(45);
$pdf->Cell(0,0,"Email",0,0,'L',0,'',1,false,'','');
$pdf->SetX(115);
$pdf->Cell(0,0,"Teléfono",0,0,'L',0,'',1,false,'','');
$pdf->SetX(140);
$pdf->Cell(0,0,"Sexo",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,$fecha_nac,0,0,'L',0,'',1,false,'','');
$pdf->SetX(45);
$pdf->Cell(0,0,$email_alumno,0,0,'L',0,'',1,false,'','');
$pdf->SetX(115);
$pdf->Cell(0,0,$telef_alumno,0,0,'L',0,'',1,false,'','');
$pdf->SetX(142);
$pdf->Cell(0,0,$sexo,0,0,'L',0,'',1,false,'','');


//DATOS CONTACTO TUTORES LEGALES
$YInicio+=7;
$pdf->RoundedRect($XInicio-2,$YInicio,185,50,2,'1111','','','');
$YInicio+=1;
$pdf->SetXY($XInicioRotulo,$YInicio);
$pdf->SetFont('dejavusans', 'B', 10, '', true);
$pdf->Cell(0,0,"DATOS CONTACTO TUTORES LEGALES",0,0,'L',0,'',1,false,'','');

$YInicio+=6;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'UB', 8, '', true);
$pdf->Cell(0,0,"Datos tutor/a legal 1",0,0,'L',0,'',1,false,'','');

$YInicio+=4;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans','U', 8, '', true);
$pdf->Cell(0,0,"Nombre y Apellidos",0,0,'L',0,'',1,false,'','');
$pdf->SetFont('dejavusans','B', 8, '', true);
$YInicio+=4;
$pdf->SetXY($XInicio,$YInicio);
$pdf->Cell(0,0,$tutor1,0,0,'L',0,'',1,false,'','');

$YInicio+=4;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans','U', 8, '', true);
$pdf->Cell(0,0,"Teléfono",0,0,'L',0,'',1,false,'','');
$pdf->SetX(50);
$pdf->Cell(0,0,"Email",0,0,'L',0,'',1,false,'','');
$YInicio+=4;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans','B', 8, '', true);
$pdf->Cell(0,0,$tlf_tutor1,0,0,'L',0,'',1,false,'','');
$pdf->SetX(50);
$pdf->Cell(0,0,$email_tutor1,0,0,'L',0,'',1,false,'','');


$YInicio+=6;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'UB', 8, '', true);
$pdf->Cell(0,0,"Datos tutor/a legal 2",0,0,'L',0,'',1,false,'','');

$YInicio+=4;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans','U', 8, '', true);
$pdf->Cell(0,0,"Nombre y Apellidos",0,0,'L',0,'',1,false,'','');
$pdf->SetFont('dejavusans','B', 8, '', true);
$YInicio+=4;
$pdf->SetXY($XInicio,$YInicio);
$pdf->Cell(0,0,$tutor2,0,0,'L',0,'',1,false,'','');

$YInicio+=4;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans','U', 8, '', true);
$pdf->Cell(0,0,"Teléfono",0,0,'L',0,'',1,false,'','');
$pdf->SetX(50);
$pdf->Cell(0,0,"Email",0,0,'L',0,'',1,false,'','');
$YInicio+=4;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans','B', 8, '', true);
$pdf->Cell(0,0,$tlf_tutor2,0,0,'L',0,'',1,false,'','');
$pdf->SetX(50);
$pdf->Cell(0,0,$email_tutor2,0,0,'L',0,'',1,false,'','');


//DATOS DEL CURSO ACTUAL
$YInicio+=7;

$pdf->SetXY($XInicioRotulo,$YInicio+1);
$pdf->SetFont('dejavusans', 'B', 10, '', true);
$pdf->Cell(0,0,"DATOS DEL CURSO ACTUAL",0,0,'L',0,'',1,false,'','');
$pdf->RoundedRect($XInicio-2,$YInicio,185,13,2,'1111','','','');

$YInicio+=6;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"Curso Actual",0,0,'L',0,'',1,false,'','');
$pdf->SetX(50);
$pdf->Cell(0,0,"Grupo",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(168,0,$curso_actual,0,0,'L',0,'',1,false,'T','T');
$pdf->SetX(53);
$pdf->Cell(10,0,$grupo_curso_actual,0,0,'L',0,'',1,false,'T','T');


//SELECCIÓN DE MATERIAS
$YInicio+=7;

$pdf->SetXY($XInicioRotulo,$YInicio+1);
$pdf->SetFont('dejavusans', 'B', 10, '', true);
$pdf->Cell(0,0,"SELECCIÓN DE MATERIAS",0,0,'L',0,'',1,false,'','');

$pdf->RoundedRect($XInicio-2,$YInicio,185,70,2,'1111','','','');
	$p_idioma="1ª Lengua Extranjera (".$primer_idioma.")";
    $obligatorias=<<<MAT
- $obligatoria1
- $obligatoria2
- $obligatoria3
MAT;
	

$YInicio+=6;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', '', 8, '', true);
$pdf->Cell(50,4,"- 1ª Lengua Extranjera: ".$primer_idioma,0,0,'L',0,'',0,true,'T','T');

$YInicio+=5;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', '', 8, '', true);
$pdf->Cell(50,4,"- ".$religion,0,0,'L',0,'',0,true,'T','T');

$YInicio+=5;
$pdf->SetXY($XInicio,$YInicio);
$pdf->Cell(50,4,"OBLIGATORIAS",1,0,'L',1,'',0,true,'T','T');
$YInicio+=5;
$pdf->SetXY($XInicio,$YInicio);	
$pdf->MultiCell(88,0,$obligatorias,0,'L',0,1,'','',true,0,false,false,0);

$YInicio+=12;
$pdf->SetXY($XInicio,$YInicio);
$pdf->Cell(180,4,"OPTATIVAS PRIORIZADAS",1,0,'L',1,'',0,true,'T','T');

$optativas1=<<<OPT
1 $optativa1
2 $optativa2
3 $optativa3
4 $optativa4
5 $optativa5
6 $optativa6
7 $optativa7
8 $optativa8
9 $optativa9
OPT;

$optativas2=<<<OPT
10 $optativa10
11 $optativa11
12 $optativa12
13 $optativa13
14 $optativa14
15 $optativa15
16 $optativa16
17 $optativa17
OPT;
$YInicio+=5;
$pdf->SetXY($XInicio,$YInicio);	
$pdf->MultiCell(80,0,$optativas1,0,'L',0,1,'','',true,0,false,false,0);
$pdf->SetXY($XInicio+85,$YInicio);	
$pdf->MultiCell(80,0,$optativas2,0,'L',0,1,'','',true,0,false,false,0);
	
	
$YInicio+=40;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,"Nº registro " . $registro,0,0,'C',0,'',1,false,'T','T');

$YInicio+=20;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 16, '', true);
$pdf->Cell(0,0,"RECUERDE QUE ESTE NO ES UN FORMULARIO DE MATRICULA",0,0,'C',0,'',1,false,'T','T');

//SI YA HAY ALGUNA PREMATRÍCULA BORRA EL ARCHIVO
$dir = __DIR__."/../../../docs/".$id_nie."/prematriculas"."/".$anno_curso.'/';     
$handle = opendir($dir);
while ($file = readdir($handle)) {
	if (is_file($dir.$file)) unlink($dir.$file);
}
closedir($dir);
//GENERA EL ARCHIVO NUEVO
$nombre_fichero=$registro . '.pdf';
if (!is_dir(__DIR__."/../../../docs/".$id_nie))mkdir(__DIR__."/../../../docs/".$id_nie,0777);
if(!is_dir(__DIR__."/../../../docs/".$id_nie."/prematriculas"))mkdir(__DIR__."/../../../docs/".$id_nie."/prematriculas",0777);
if(!is_dir(__DIR__."/../../../docs/".$id_nie."/prematriculas"."/".$anno_curso))mkdir(__DIR__."/../../../docs/".$id_nie."/prematriculas"."/".$anno_curso,0777);
$ruta=__DIR__."/../../../docs/".$id_nie."/"."prematriculas/".$anno_curso."/". $nombre_fichero;
$pdf->Output($ruta, 'F');
//FIN GENERA PDF
exit("envio_ok ");

