<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("../../../php/conexion.php");

if ($mysqli->errno>0) {
    exit("servidor");
}
include("../../../php/funciones.php");
require_once(__DIR__.'/../../../php/tcpdf/config/tcpdf_config_alt.php');
require_once(__DIR__.'/../../../php/tcpdf/tcpdf.php');
include("../../../php/cabecera_pdf.php");


$curso=$_POST['curso'];
$anno_curso=$_POST['anno_curso'];
$anno_curso_premat=$_POST['anno_curso_premat'];
$id_nie=$_POST['id_nie'];
$email=$_POST['email'];
$apellidos=$_POST['apellidos'];
$nombre=$_POST['nombre'];
$fecha_nac=substr($_POST['fecha_nac'],6,4).'/'.substr($_POST['fecha_nac'],3,2).'/'.substr($_POST['fecha_nac'],0,2);
$fecha_nac=date('Y-m-d',strtotime($fecha_nac));
$email_alumno=$_POST['email_alumno'];
$telef_alumno=$_POST['telef_alumno'];
$tutor1=$_POST['tutor1'];
$email_tutor1=$_POST['email_tutor1'];
$tlf_tutor1=$_POST['tlf_tutor1'];
$tutor2=$_POST['tutor2'];
$email_tutor2=$_POST['email_tutor2'];
$tlf_tutor2=$_POST['tlf_tutor2'];
$fecha_registro=date('Y-m-d');

$curso_actual=$_POST['curso_actual'];
$grupo_curso_actual=$_POST['grupo_curso_actual'];
$sexo=$_POST['sexo'];

//Parte especifica de ESO	
$religion=$_POST['eso_religion'];

$bloque1=$_POST['eso4_opt1'];
$bloque2=$_POST['eso4_opt2'];
$bloque3=$_POST['eso4_opt3'];
$bloque4=$_POST['eso4_opt4'];
$bloque5=$_POST['eso4_opt5'];
$bloque6=$_POST['eso4_opt6'];
$optativa1=$_POST['eso4_opt7'];
$optativa2=$_POST['eso4_opt8'];
$optativa3=$_POST['eso4_opt9'];
$optativa4=$_POST['eso4_opt10'];

$registro=generaRegistro("iesulabto_pm4esd_");
$repite_registro=true;
while ($repite_registro){
    $res=$mysqli->query("select * from premat_eso where registro='$registro'");
    if ($mysqli->errno>0) exit("servidor");
    if ($res->num_rows>0){
       $registro= generaRegistro("iesulabto_pm4esd_"); 
    }
    else if ($res->num_rows==0){
        $repite_registro=false;
    }
    $res->free();
}


$mysqli->query("delete from premat_eso where id_nie='$id_nie' and curso='$anno_curso'");
$mysqli->query("delete from premat_bach where id_nie='$id_nie' and curso='$anno_curso'");

$mysqli->query("insert into premat_eso (id_nie,
                                        registro,
                                        fecha_registro,
                                        grupo,
                                        curso,
                                        curso_premat,
                                        apellidos,
                                        nombre,
                                        email,
                                        fecha_nac,
                                        telef_alumno,
                                        tutor1,
                                        email_tutor1,
                                        tlf_tutor1,
                                        tutor2,
                                        email_tutor2,
                                        tlf_tutor2,
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
                                        sexo,
                                        curso_actual,
                                        grupo_curso_actual) 
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
                                        '$telef_alumno',
                                        '$tutor1',
                                        '$email_tutor1',
                                        '$tlf_tutor1',
                                        '$tutor2',
                                        '$email_tutor2',
                                        '$tlf_tutor2',
                                        '$religion',
                                        '$bloque1',
                                        '$bloque2',
                                        '$bloque3',
                                        '$bloque4',
                                        '$bloque5',
                                        '$bloque6',
                                        '$optativa1',
                                        '$optativa2',
                                        '$optativa3',
                                        '$optativa4',
                                        '$sexo',
                                        '$curso_actual',
                                        '$grupo_curso_actual')");
if ($mysqli->errno>0){
    exit("registro_erroneo ".$mysqli->errno);
}

//GENERA EL PDF Y LO GUARDA EN EL SERVIDOR

// create new PDF document
$titulo_PDF = "P R E M A T R Í C U L A";
$pdf = new MYPDF($datos_cen, $titulo_PDF);


// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($datos_cen["centro"]);
$pdf->SetTitle('Impreso Prematrícula');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('PDF, secretaría,  '. $datos_cen["localidad_centro"].', Impreso Prematrícula');

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
<p>Programa lingüístico: <b>No</b></p>
HTML1;

$YInicio=40;

$pdf->RoundedRect(82,$YInicio,45,21,2,'1111','','','');
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
$pdf->RoundedRect($XInicio-2,$YInicio,185,51,2,'1111','','','');
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
$pdf->SetX(40);
$pdf->Cell(0,0,"Grupo",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(168,0,$curso_actual,0,0,'L',0,'',1,false,'T','T');
$pdf->SetX(43);
$pdf->Cell(10,0,$grupo_curso_actual,0,0,'L',0,'',1,false,'T','T');

//SELECCIÓN DE MATERIAS
$YInicio+=6;

$pdf->SetXY($XInicioRotulo,$YInicio+1);
$pdf->SetFont('dejavusans', 'B', 10, '', true);
$pdf->Cell(0,0,"SELECCIÓN DE MATERIAS",0,0,'L',0,'',1,false,'','');

$p_idioma="1ª Lengua Extranjera (".$primer_idioma.")";
$tronc_gen=<<<MAT
    - Ámbito Lingüístico y Social
    - Ámbito Científico-Tecnológico
    - 1ª Lengua Extranjera (Inglés)
    - Educación Física
    - $religion
    - Tutoría
MAT;
$h_tronc_gen=<<<MAT
    8
    9
    4
    2
    1
    1
MAT;


$bloque=<<<MAT
    1 $bloque1
    2 $bloque2
    3 $bloque3
    4 $bloque4
    5 $bloque5
MAT;

$optativas=<<<MAT3
    1 $bloque6<br>
    2 $optativa1<br>
    3 $optativa2<br>
    4 $optativa3<br>
    5 $optativa4<br>
MAT3;

$pdf->RoundedRect($XInicio-2,$YInicio,185,55,2,'1111','','','');
$pdf->SetFont('dejavusans', '', 8, '', true);
$YInicio+=5;
$YInicio_seccion=$YInicio;
$pdf->SetXY($XInicio,$YInicio);      
$pdf->Cell(90,4,"  OBLIGATORIAS                                            Horas Semanales",1,0,'L',1,'',0,true,'T','T');
$pdf->SetX(105);          
$pdf->Cell(85,4,"  MATERIAS OPTATIVAS (3h semanales)",1,0,'L',1,'',0,true,'T','T');

$YInicio+=5;
$pdf->SetXY($XInicio,$YInicio);
$fil_1=$pdf->MultiCell(75,0,$tronc_gen,0,'L',0,1,'','',true,0,false,false,0);
$pdf->SetXY(87,$YInicio);
$pdf->MultiCell(15,0,$h_tronc_gen,0,'L',0,1,'','',true,0,false,false,0);
$YInicio+=$fil_1*3+2;

$pdf->SetXY($XInicio,$YInicio);      
$pdf->Cell(90,4,"  MATERIAS DE OPCIÓN (3h semanales)",1,0,'L',1,'',0,true,'T','T');
$YInicio+=5;
$pdf->SetXY($XInicio,$YInicio);
$pdf->MultiCell(75,0,$bloque,0,'L',0,1,'','',true,0,false,false,0);

$YInicio=$YInicio_seccion+5;
$pdf->SetXY(105,$YInicio);
$pdf->MultiCell(75,0,$optativas,0,'L',0,1,'','',true,0,true,false,0);
$YInicio+=$fil_1*3-2;

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

