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
    return "iesulabto_pm2bac_".date('dmY')."_".$array[0].$array[1].$array[2].$array[3].$array[4].$array[5].$array[6].$array[7];;    
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
$curso_actual=$_POST['sel_curso_act'];
$grupo_curso_actual=$_POST['sel_grupo_curso_act'];
$tutor1=$_POST['tutor1'];
$email_tutor1=$_POST['email_tutor1'];
$tlf_tutor1=$_POST['tlf_tutor1'];
$tutor2=$_POST['tutor2'];
$email_tutor2=$_POST['email_tutor2'];
$tlf_tutor2=$_POST['tlf_tutor2'];
$fecha_registro=date('Y-m-d');


//Parte especifica de BACH	
$primer_idioma=$_POST['primer_idioma'];
$modalidad1=$_POST['modalidad1'];
$modalidad2=$_POST['modalidad2'];
$modalidad3=$_POST['modalidad3'];
$espitin1=$_POST['b2c_eitin11'];
$espitin2=$_POST['b2c_eitin12'];
$espitin3=$_POST['b2c_eitin13'];
$espitin4=$_POST['b2c_eitin14'];
$espitin5=$_POST['b2c_eitin15'];
$espitin6=$_POST['b2c_eitin16'];
$espitin7=$_POST['b2c_eitin17'];
$espitin8=$_POST['b2c_eitin18'];
$espitin9=$_POST['b2c_eitin19'];
$espitin10=$_POST['b2c_eitin20'];
$espitin11=$_POST['b2c_eitin21'];
$espitin12=$_POST['b2c_eitin22'];
$espitin13=$_POST['b2c_eitin23'];
$espitin14=$_POST['b2c_eitin24'];
$espitin15=$_POST['b2c_eitin25'];




$registro=generaRegistro();
$repite_registro=true;
while ($repite_registro){
    $res=$mysqli->query("select * from premat_2bach_hcs where registro='$registro'");
    if ($mysqli->errno>0) exit("servidor");
    if ($res->num_rows>0){
       $registro= generaRegistro(); 
    }
    else if ($res->num_rows==0){
        $repite_registro=false;
    }
    $res->free();
}

$mysqli->query("delete from premat_1eso where id_nie='$id_nie' and curso='$anno_curso'");
$mysqli->query("delete from premat_2eso where id_nie='$id_nie' and curso='$anno_curso'");
$mysqli->query("delete from premat_3eso where id_nie='$id_nie' and curso='$anno_curso'");
$mysqli->query("delete from premat_4eso where id_nie='$id_nie' and curso='$anno_curso'");
$mysqli->query("delete from premat_2esopmar where id_nie='$id_nie' and curso='$anno_curso'");
$mysqli->query("delete from premat_3esopmar where id_nie='$id_nie' and curso='$anno_curso'");
$mysqli->query("delete from premat_1bach_lomloe where id_nie='$id_nie' and curso='$anno_curso'");
$mysqli->query("delete from premat_2bach_hcs where id_nie='$id_nie' and curso='$anno_curso'");
$mysqli->query("delete from premat_2bach_c where id_nie='$id_nie' and curso='$anno_curso'");
$mysqli->query("delete from premat_bach where id_nie='$id_nie' and curso='$anno_curso'");
$mysqli->query("delete from premat_eso where id_nie='$id_nie' and curso='$anno_curso'");


//MATERIA 1->Primer Idioma. MATERIAS 2,3 Y 4->Modalidad1, 2 y 3
//MATERIAS 5 a 19 - Optativas
$mysqli->query("insert into premat_bach (id_nie,
                                        registro,
                                        fecha_registro,
                                        grupo,
                                        curso,
                                        curso_premat,
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
                                        materia19) 
                                        values ('$id_nie',
                                        '$registro',
                                        '$fecha_registro',
                                        '$curso',
                                        '$anno_curso',
                                        '$anno_curso_premat',
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
                                        '$primer_idioma',
                                        '$modalidad1',
                                        '$modalidad2',
                                        '$modalidad3',
                                        '$espitin1',
                                        '$espitin2',
                                        '$espitin3',
                                        '$espitin4',
                                        '$espitin5',
                                        '$espitin6',
                                        '$espitin7',
                                        '$espitin8',
                                        '$espitin9',
                                        '$espitin10',
                                        '$espitin11',
                                        '$espitin12',
                                        '$espitin13',
                                        '$espitin14',
                                        '$espitin15')");
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
<h4>Curso $anno_curso_premat</h4><br>
HTML1;

$YInicio=40;

$pdf->RoundedRect(73,$YInicio,56,15,2,'1111','','','');
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

$pdf->RoundedRect($XInicio-2,$YInicio,185,61,2,'1111','','','');
$comunes=<<<MAT
- Historia de España
- Lengua Castellana y Literatura II
- Historia de la Filosofía
- 1ª Lengua extranjera II ($primer_idioma)
MAT;
$h_comunes=<<<MAT
3
4
3
4
MAT;
$_modalidad=<<<MAT
- $modalidad1
- $modalidad2
- $modalidad3
MAT;
$h_modalidad=<<<MAT
4
4
4
MAT;

$optativas_col1=<<<MAT
1  $espitin1
2  $espitin2
3  $espitin3
4  $espitin4
5  $espitin5
6  $espitin6
7  $espitin7
8  $espitin8
MAT;

$optativas_col2=<<<MAT
 9  $espitin9
10  $espitin10
11  $espitin11
12  $espitin12
13  $espitin13
14  $espitin14
15  $espitin15
MAT;

$YInicio+=6;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', '', 8, '', true);
$pdf->Cell(50,4,"1ª Lengua Extranjera: ".$primer_idioma,0,0,'L',0,'',0,true,'T','T');

$YInicio+=5;
$pdf->SetXY($XInicio,$YInicio);
$pdf->Cell(88,4,"MATERIAS COMUNES                                    Horas Semanales",1,0,'L',1,'',0,true,'T','T');
$pdf->SetX(105);
$pdf->Cell(88,4,"MATERIAS DE MODALIDAD                            Horas Semanales",1,0,'L',1,'',0,true,'T','T');
$YInicio+=5;
$pdf->SetXY($XInicio,$YInicio);	
$pdf->MultiCell(88,4,$comunes,0,'L',0,1,'','',true,0,false,false,0);
$pdf->SetXY(85,$YInicio);
$pdf->MultiCell(15,0,$h_comunes,0,'L',0,1,'','',true,0,false,false,0);
$pdf->SetXY(105,$YInicio);	
$pdf->MultiCell(88,0,$_modalidad,0,'L',0,1,'','',true,0,false,false,0);
$pdf->SetXY(180,$YInicio);
$pdf->MultiCell(88,4,$h_modalidad,0,'L',0,1,'','',true,0,false,false,0);
$YInicio+=13;
$pdf->SetXY($XInicio,$YInicio);
$pdf->Cell(88,4,"OPTATIVAS (4h)",1,0,'L',1,'',0,true,'T','T');
$pdf->SetX(105);
$pdf->Cell(88,4,"OPTATIVAS (4h)",1,0,'L',1,'',0,true,'T','T');
$YInicio+=5;
$pdf->SetXY($XInicio,$YInicio);		
$pdf->MultiCell(88,0,$optativas_col1,0,'L',0,1,'','',true,0,false,false,0);
$pdf->SetXY(105,$YInicio);
$pdf->MultiCell(88,0,$optativas_col2,0,'L',0,1,'','',true,0,false,false,0);

$YInicio+=40;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,"Nº registro " . $registro,0,0,'C',0,'',1,false,'T','T');

$YInicio+=20;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 16, '', true);
$pdf->Cell(0,0,"RECUERDE QUE ESTE NO ES UN FORMULARIO DE MATRICULA",0,0,'C',0,'',1,false,'T','T');

//SI YA HAY ALGUNA MATRÍCULA BORRA EL ARCHIVO
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

