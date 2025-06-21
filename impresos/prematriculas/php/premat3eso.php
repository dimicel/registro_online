<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("../../../php/conexion.php");

if ($mysqli->errno>0) {
    exit("servidor");
}

// Include TCPDF library
require_once(__DIR__.'/../../../php/tcpdf/config/tcpdf_config_alt.php');
require_once(__DIR__.'/../../../php/tcpdf/tcpdf.php');
include("../../../php/cabecera_pdf.php");

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
    return "iesulabto_pm3eso_".date('dmY')."_".$array[0].$array[1].$array[2].$array[3].$array[4].$array[5].$array[6].$array[7];;    
}


$curso=$_POST['curso'];
$anno_curso=$_POST['anno_curso'];
$anno_curso_premat=$_POST['anno_curso_premat'];
if (!isset($_POST['prog_ling'])) $prog_ling="No";
else{
	if (is_null($_POST['prog_ling'])) $prog_ling="No";
	else $prog_ling="Sí";
}
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
//Al haber cambiado la ley (LOMLOE) el campo matematicas de la base de datos no se usa
$religion=$_POST['eso_religion'];
$primer_idioma=$_POST['eso_primer_idioma'];
$opt1=$_POST['eso3_opt1'];
$opt2=$_POST['eso3_opt2'];
$opt3=$_POST['eso3_opt3'];
$opt4=$_POST['eso3_opt4'];

$registro=generaRegistro();
$repite_registro=true;
while ($repite_registro){
    $res=$mysqli->query("select * from premat_eso where registro='$registro'");
    if ($mysqli->errno>0) exit("servidor");
    if ($res->num_rows>0){
       $registro= generaRegistro(); 
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
                                        prog_ling,
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
                                        sexo,
                                        curso_actual,
                                        grupo_curso_actual) 
                                        values ('$id_nie',
                                        '$registro',
                                        '$fecha_registro',
                                        '$curso',
                                        '$anno_curso',
                                        '$anno_curso_premat',
                                        '$prog_ling',
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
                                        '$primer_idioma',
                                        '$religion',
                                        '$opt1',
                                        '$opt2',
                                        '$opt3',
                                        '$opt4',
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
<p>Programa lingüístico: <b>$prog_ling</b></p>
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

	$pdf->RoundedRect($XInicio-2,$YInicio,185,40,2,'1111','','','');
	$p_idioma="1ª Lengua Extranjera (".$primer_idioma.")";
    //$mat="Matemáticas ".$matematicas;
	$mat_comunes=<<<MAT
    - Biología y Geología
    - Física y Química
    - Geografía e Historia
    - Educación Física
    - Lengua Castellana y Literatura
    - Tecnología y Digitalización
    - Matemáticas
    - Educación Plástica, Visual y Audiovisual
    - $p_idioma
    - $religion
MAT;
	$h_mat_comunes=<<<MAT
    3
    3
    3
    2
    4
    2
    4
    2
    3
    1
MAT;
	$h_mat_optativas=<<<MAT
    2
    2
    2
    2
MAT;
	$mat_optativas=<<<MAT
   1   $opt1
   2   $opt2
   3   $opt3
   4   $opt4
MAT;

$pdf->setFillColor(200);

$YInicio+=6;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', '', 8, '', true);
$pdf->Cell(80,4,"MATERIAS COMUNES                             Horas Sem.",1,0,'L',1,'',0,true,'T','T');
$pdf->SetX(95);
$pdf->Cell(99,4," Pref. MATERIAS OPTATIVAS                                               Horas Sem.",1,0,'L',1,'',0,true,'T','T');

$YInicio+=5;
$pdf->SetXY($XInicio,$YInicio);
$fil_1=$pdf->MultiCell(75,0,$mat_comunes,0,'L',0,1,'','',true,0,false,false,0);
$pdf->SetXY(75,$YInicio);
$pdf->MultiCell(15,0,$h_mat_comunes,0,'L',0,1,'','',true,0,false,false,0);
$pdf->SetXY(95,$YInicio);
$fil_2=$pdf->MultiCell(90,0,$mat_optativas,0,'L',0,1,'','',true,0,false,false,0);
$pdf->SetXY(182,$YInicio);
$pdf->MultiCell(15,0,$h_mat_optativas,0,'L',0,1,'','',true,0,false,false,0);

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

