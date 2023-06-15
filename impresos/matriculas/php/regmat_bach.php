<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("../../../php/conexion.php");
require_once(__DIR__.'/../../../php/tcpdf/config/tcpdf_config_alt.php');
require_once(__DIR__.'/../../../php/tcpdf/tcpdf.php');

if ($mysqli->errno>0) {
    exit("servidor");
}

$mysqli->set_charset("utf8");
require_once(__DIR__.'/../../../php/tcpdf/config/tcpdf_config_alt.php');
require_once(__DIR__.'/../../../php/tcpdf/tcpdf.php');

$desc_reg = Array(
    "1º Bach. HH.CC.SS."=>"ma1bah",
    "1º Bach. Ciencias y Tecnología"=>"ma1bac",
    "1º Bach. General"=>"ma1bag",
    "2º Bach. HH.CC.SS."=>"ma2bah",
    "2º Bach. Ciencias y Tecnología"=>"ma2bac"
);


$curso=$_POST["curso"];
if ($curso=="1º Bachillerato Humanidades y Ciencias Sociales") $curso="1º Bach. HH.CC.SS.";
elseif($curso=="2º Bachillerato Humanidades y Ciencias Sociales") $curso="2º Bach. HH.CC.SS.";
elseif($curso=="1º Bachillerato Ciencias y Tecnología") $curso="1º Bach. Ciencias y Tecnología";
elseif($curso=="2º Bachillerato Ciencias y Tecnología") $curso="2º Bach. Ciencias y Tecnología";
elseif($curso=="1º Bachillerato General") $curso="1º Bach. General";
$anno_curso=$_POST['anno_curso'];

$tutor=$_POST['tutor'];
$autor_fotos=$_POST['_autor_fotos'];
$alumno_nuevo=$_POST['_alumno_nuevo'];
$al_nuevo_otra_comunidad=$_POST['_nuevo_otra_comunidad'];
$repetidor=$_POST['_repetidor'];
$interno=$_POST['_interno'];
$fecha_registro=date('Y-m-d');
$consolida_premat=$_POST["consolida_premat"];

$id_nie=$_POST['id_nie'];
$email=$_POST['email'];
$apellidos=$_POST['apellidos'];
$nombre=$_POST['nombre'];
$fecha_nac=substr($_POST['fecha_nac'],6,4).'/'.substr($_POST['fecha_nac'],3,2).'/'.substr($_POST['fecha_nac'],0,2);
$fecha_nac=date('Y-m-d',strtotime($fecha_nac));
$sexo=$_POST['sexo'];
$nif_nie=$_POST['nif_nie'];
$email_alumno=$_POST['email_alumno'];
$telef_alumno=$_POST['telef_alumno'];
$direccion=$_POST['direccion'];
$cp=$_POST['cp'];
$localidad=$_POST['localidad'];
$provincia=$_POST['provincia'];
$tutor1=$_POST['tutor1'];
$email_tutor1=$_POST['email_tutor1'];
$tlf_tutor1=$_POST['tlf_tutor1'];
$tutor2=$_POST['tutor2'];
$email_tutor2=$_POST['email_tutor2'];
$tlf_tutor2=$_POST['tlf_tutor2'];
$fecha_registro=date('Y-m-d');

$mysqli->query("delete from mat_eso where id_nie='$id_nie' and curso='$anno_curso'");
$mysqli->query("delete from mat_bach where id_nie='$id_nie' and curso='$anno_curso'");
$mysqli->query("delete from mat_fpb where id_nie='$id_nie' and curso='$anno_curso'");
$mysqli->query("delete from mat_ciclos where id_nie='$id_nie' and curso='$anno_curso'");

$registro=generaRegistro($desc_reg[$curso]);
$repite_registro=true;
while ($repite_registro){
    $res=$mysqli->query("select registro from mat_bach where registro='$registro'");
    if ($mysqli->errno>0) exit("servidor");
    if ($res->num_rows>0){
        $registro= generaRegistro($desc_reg[$curso]); 
    }
    else if ($res->num_rows==0){
        $repite_registro=false;
    }
    $res->free();
}

$mysqli->query("insert into mat_bach (id_nie,
                                    registro,
                                    fecha_registro,
                                    consolida_premat,
                                    curso,
                                    grupo,
                                    al_nuevo,
                                    al_nuevo_otracomunidad,
                                    repite,
                                    interno,
                                    autoriza_fotos,
                                    tutor_autorizaciones,
                                    apellidos,
                                    nombre,
                                    email,
                                    id_nif,
                                    fecha_nac,
                                    sexo,
                                    telef_alumno,
                                    direccion,
                                    cp,
                                    localidad,
                                    provincia,
                                    tutor1,
                                    email_tutor1,
                                    tlf_tutor1,
                                    tutor2,
                                    email_tutor2,
                                    tlf_tutor2) 
                                    values ('$id_nie',
                                    '$registro',
                                    '$fecha_registro',
                                    '$consolida_premat',
                                    '$anno_curso',
                                    '$curso',
                                    '$alumno_nuevo',
                                    '$al_nuevo_otra_comunidad',
                                    '$repetidor',
                                    '$interno',
                                    '$autor_fotos',
                                    '$tutor',
                                    '$apellidos',
                                    '$nombre',
                                    '$email_alumno',
                                    '$nif_nie',
                                    '$fecha_nac',
                                    '$sexo',
                                    '$telef_alumno',
                                    '$direccion',
                                    '$cp',
                                    '$localidad',
                                    '$provincia',
                                    '$tutor1',
                                    '$email_tutor1',
                                    '$tlf_tutor1',
                                    '$tutor2',
                                    '$email_tutor2',
                                    '$tlf_tutor2')");
                               
if ($mysqli->errno>0){
    exit("registro_erroneo ".$mysqli->errno);
}
$rrr=$mysqli->query("select * from matriculas_delphos where id_nie='$id_nie' and curso='$anno_curso'");
if ($rrr->num_rows>0){
    $mysqli->query("update matriculas_delphos set avisado_email=1 where id_nie='$id_nie' and curso='$anno_curso'");
}
else{
    $mysqli->query("insert into matriculas_delphos (id_nie,curso,avisado_email) values ('$id_nie','$anno_curso',1)");
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
		$this->Cell(0,0,"M A T R Í C U L A",0,0,'C',0,'',1,false,'T','T');
			
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
$pdf->SetTitle('Impreso Matrícula');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('ulaboral, PDF, secretaría, Toledo, Impreso Matrícula');

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

//Curso y año <h3>$curso</h3><br>
$html1 = <<<HTML1
<h3>$curso</h3><br>
<h4>Curso $anno_curso</h4><br>
HTML1;


if (is_file('../../../docs/fotos/'.$id_nie.'.jpg')) $pdf->Image('../../../docs/fotos/'.$id_nie.'.jpg',15,35,25,33,'','','T');
elseif (is_file('../../../docs/fotos/'.$id_nie.'.jpeg')) $pdf->Image('../../../docs/fotos/'.$id_nie.'.jpeg',15,35,25,33,'','','T');
$pdf->Rect(15,35,25,33,'all');

if($consolida_premat=="Si"){
    $pdf->SetXY(15,40);
    $pdf->Cell(0,0,"(PREMAT. CONSOLIDADA)",0,0,'C',0,'',1,false,'T','T');
}
$YInicio=45;
$pdf->RoundedRect(70,$YInicio,70,18,2,'1111','','','');
$pdf->writeHTMLCell(0, 0, '', $YInicio+2, $html1, 0, 1, false, true, 'C', true);

//Padding dentro de la celda del texto
$pdf->setCellPaddings(0,0,0,0);
//Interlineado
$pdf->setCellHeightRatio(1);

$YInicio+=27;
$XInicioRotulo=17;
$XInicio=12;

//DATOS INICIALES
$pdf->RoundedRect(35,$YInicio-2,135,8,2,'1111','','','');

$pdf->SetXY(40,$YInicio);
$pdf->SetFont('dejavusans', '', 8, '', true);
$pdf->Cell(0,0,"Alumno Nuevo:       Inicia enseñanza en otra com.:        Repite Curso:        Interno:",0,0,'L',0,'',1,false,'','');

$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->SetX(62);
$pdf->Cell(0,0,$alumno_nuevo,0,0,'L',0,'',1,false,'','');
$pdf->SetX(112);
$pdf->Cell(0,0,$al_nuevo_otra_comunidad,0,0,'L',0,'',1,false,'','');
$pdf->SetX(138);
$pdf->Cell(0,0,$repetidor,0,0,'L',0,'',1,false,'','');
$pdf->SetX(156);
$pdf->Cell(0,0,$interno,0,0,'L',0,'',1,false,'','');


//DATOS DEL ALUMNO
$YInicio+=9;
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
$pdf->Cell(0,0,"NIF/NIE",0,0,'L',0,'',1,false,'','');
$pdf->SetX(70);
$pdf->Cell(0,0,"Email",0,0,'L',0,'',1,false,'','');
$pdf->SetX(140);
$pdf->Cell(0,0,"Teléfono",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,$fecha_nac,0,0,'L',0,'',1,false,'','');
$pdf->SetX(45);
$pdf->Cell(0,0,$nif_nie,0,0,'L',0,'',1,false,'','');
$pdf->SetX(70);
$pdf->Cell(0,0,$email_alumno,0,0,'L',0,'',1,false,'','');
$pdf->SetX(142);
$pdf->Cell(0,0,$telef_alumno,0,0,'L',0,'',1,false,'','');


//DATOS DEL DOMICILIO FAMILIAR
$YInicio+=7;
$pdf->RoundedRect($XInicio-2,$YInicio,185,66,2,'1111','','','');
$YInicio+=1;
$pdf->SetXY($XInicioRotulo,$YInicio);
$pdf->SetFont('dejavusans', 'B', 10, '', true);
$pdf->Cell(0,0,"DATOS DEL DOMICILIO FAMILIAR",0,0,'L',0,'',1,false,'','');

$YInicio+=6;
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


//-------AUTORIZACIONES MATRÍCULA
$YInicio+=7;
$YInicioAutorizaciones=$YInicio;
$pdf->RoundedRect($XInicio-2,$YInicio,185,51,2,'1111','','','');
$pdf->SetXY($XInicioRotulo,$YInicio+1);
$pdf->SetFont('dejavusans', 'B', 10, '', true);
$pdf->Cell(0,0,"AUTORIZACIONES",0,0,'L',0,'',1,false,'','');

$YInicio+=6;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', '', 7, '', true);
$texto_1_consent=<<<EOT
D./Dña. $tutor, como tutor/a legal del alumno/a $nombre $apellidos, mediante este formulario formaliza su matrícula en el Centro para el año escolar $anno_curso para cursar las enseñanzas de $curso.
EOT;
$nf_consent=$pdf->MultiCell(180,0,$texto_1_consent,0,'L',0,1,'','',true,0,false,false,'');

$texto_2_consent=<<<EOT
Así mismo, <b>$autor_fotos</b> autoriza al IES Universidad Laboral a la  toma de fotografías y de vídeo para que con motivo de las actividades Docentes - y especialmente las del 50  Aniversario- puedan ser usadas  en documentos oficiales o impresos, páginas webs, blogs redes sociales (Twitter Facebook)  o en cualquier otro soporte online del centro o medio de comunicación común y siempre que se use bajo la supervisión del equipo directivo.
Autorización en  conformidad a lo establecido en el artículo 5.1 de la Ley Orgánica 15/1999 de protección de datos de carácter personal y Art. 6.1.a) Reglamento (UE) 2016/679  general de protección de datos.
Esta autorización podrá ser anulada en cualquier instante siempre que se comunique por escrito este aspecto a la Dirección del Centro.
EOT;

$texto_3_consent=<<<EOT
INFORMACIÓN BÁSICA DE PROTECCIÓN DE DATOS
-Responsable: Viceconsejería de Educación.
-Finalidad: Gestión administrativa y educativa del alumnado de centros docentes de Castilla-La Manchaa, así como el uso de los recursos educativos digitales por parte de la comunidad educativa.
-Legitimación: 6.1.c) Cumplimiento de una obligación legal del Reglamento General de Protección de Datos; 6.1.e) Misión en interés público o ejercicio de poderes públicos del Reglamento General de Protección de Datos. Datos de categoría especial: 9.2.g) el tratamiento es necesario por razones de un interés público esencial del Reglamento General de Protección de Datos. Ley Orgánica 2/2006, de 3 de mayo, de Educación / Ley 7/2010, de 20 de julio, de Educación de Castilla-La Mancha
-Origen de los datos: El propio interesado o su representante legal, administraciones públicas.
-Categoría de los datos: Datos de carácter identificativo: NIF/DNI, nombre y apellidos, dirección, teléfono, firma, firma electrónica, correo electrónico; imagen/voz. Datos especialmente protegidos: Salud. Datos de infracciones administrativas. Otros datos tipificados: Características personales; académicos y profesionales; detalles del empleo; económicos, financieros y de seguros.
-Destinatarios: Existe cesión de datos.
-Derechos: Puede ejercer los derechos de acceso, rectificación o supresión de sus datos, así como otros derechos, tal y como se explica en la información adicional.
-Información adicional: Disponible en la dirección electrónica: https://rat.castillalamancha.es/info/0372
EOT;

$YInicio+=$nf_consent*3;
$pdf->SetXY($XInicio,$YInicio);
$nf2_consent=$pdf->MultiCell(180,0,$texto_2_consent,0,'J',0,1,'','',true,0,true,false,0);
$YInicio+=$nf2_consent*4+12;
$pdf->SetXY($XInicio,$YInicio);
$pdf->MultiCell(180,0,$texto_3_consent,0,'L',0,1,'','',true,0,false,false,0);

//--------FINAL
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$yyyy=substr($fecha_registro,0,4);
$mm=$meses[intval(substr($fecha_registro,5,2))-1];
$dd=substr($fecha_registro,8,2);
$fecha_firma="Toledo, a ".$dd." de ".$mm." de ".$yyyy;
$texto=<<<EOD
<p style="text-align:center">$fecha_firma<br>Nº de registro: $registro</p>
EOD;
$YInicio+=28;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
//$pdf->Cell(0,0,"Nº registro " . $texto,0,0,'C',0,'',1,false,'T','T');
$pdf->writeHTMLCell(180,0,$XInicio,$YInicio,$texto,0,0,false,true,'',true);

//SI YA HAY ALGUNA MATRÍCULA BORRA EL ARCHIVO
$dir = __DIR__."/../../../docs/".$id_nie."/matriculas"."/".$anno_curso.'/';     
$handle = opendir($dir);
while ($file = readdir($handle)) {
	if (is_file($dir.$file)) unlink($dir.$file);
}
closedir($dir);
//GENERA EL ARCHIVO NUEVO
$nombre_fichero=$registro . '.pdf';
if (!is_dir(__DIR__."/../../../docs/".$id_nie))mkdir(__DIR__."/../../../docs/".$id_nie,0777);
if(!is_dir(__DIR__."/../../../docs/".$id_nie."/matriculas"))mkdir(__DIR__."/../../../docs/".$id_nie."/matriculas",0777);
if(!is_dir(__DIR__."/../../../docs/".$id_nie."/matriculas"."/".$anno_curso))mkdir(__DIR__."/../../../docs/".$id_nie."/matriculas"."/".$anno_curso,0777);
$ruta=__DIR__."/../../../docs/".$id_nie."/"."matriculas/".$anno_curso."/". $nombre_fichero;
$pdf->Output($ruta, 'F');
//FIN GENERA PDF

exit ("envio_ok");


function generaRegistro($c){
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
    return "iesulabto_".$c."_".date('dmY')."_".$array[0].$array[1].$array[2].$array[3].$array[4].$array[5].$array[6].$array[7];;    
}