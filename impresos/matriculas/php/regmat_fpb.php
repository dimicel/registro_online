<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("../../../php/conexion.php");

if ($mysqli->errno>0) {
    exit("servidor");
}
include("../../../php/funciones.php");
// Requiere TCPDF y la cabecera del PDF
require_once(__DIR__.'/../../../php/tcpdf/config/tcpdf_config_alt.php');
require_once(__DIR__.'/../../../php/tcpdf/tcpdf.php');
include("../../../php/cabecera_pdf.php");

$ciclo=$_POST['sel_ciclos'];
$curso_ciclo=$_POST['sel_curso'];
$al_nuevo_otra_comunidad=$_POST['_nuevo_otra_comunidad'];

$anno_curso=$_POST['anno_curso'];
/*
if (!isset($_POST['_autor_fotos'])) $autor_fotos="No";
else{
    if (is_null($_POST['_autor_fotos'])) $autor_fotos="No";
    else $autor_fotos=$_POST['_autor_fotos'];
}*/
$autor_fotos=$_POST['_autor_fotos'];
$fecha_registro=date('Y-m-d');

$id_nie=$_POST['id_nie'];
$email=$_POST['email'];
$transporte=$_POST['transporte'];
$apellidos=$_POST['apellidos'];
$nombre=$_POST['nombre'];
$fecha_nac=substr($_POST['fecha_nac'],6,4).'/'.substr($_POST['fecha_nac'],3,2).'/'.substr($_POST['fecha_nac'],0,2);
$fecha_nac=date('Y-m-d',strtotime($fecha_nac));
$nif_nie=$_POST['nif_nie'];
$email_alumno=$_POST['email_alumno'];
$telef_alumno=$_POST['telef_alumno'];
$direccion=$_POST['direccion'];
$cp=$_POST['cp'];
$localidad=$_POST['localidad'];
$provincia=$_POST['provincia'];
$tutor=$_POST['tutor'];
$fecha_registro=date('Y-m-d');

$registro=generaRegistro($mysqli, "mat_fpb", "iesulabto_matfpb_");

$mysqli->query("delete from mat_eso where id_nie='$id_nie' and curso='$anno_curso'");
$mysqli->query("delete from mat_bach where id_nie='$id_nie' and curso='$anno_curso'");
$mysqli->query("delete from mat_fpb where id_nie='$id_nie' and curso='$anno_curso'");
$mysqli->query("delete from mat_ciclos where id_nie='$id_nie' and curso='$anno_curso'");

$mysqli->query("insert into mat_fpb (id_nie,
                                        registro,
                                        fecha_registro,
                                        curso,
                                        ciclo,
                                        curso_ciclo,
                                        transporte,
                                        apellidos,
                                        nombre,
                                        email,
                                        id_nif,
                                        fecha_nac,
                                        telefono,
                                        direccion,
                                        cp,
                                        localidad,
                                        provincia,
                                        autoriza_fotos,
                                        tutor_autorizaciones) 
                                        values ('$id_nie',
                                        '$registro',
                                        '$fecha_registro',
                                        '$anno_curso',
                                        '$ciclo',
                                        '$curso_ciclo',
                                        '$transporte',
                                        '$apellidos',
                                        '$nombre',
                                        '$email_alumno',
                                        '$nif_nie',
                                        '$fecha_nac',
                                        '$telef_alumno',
                                        '$direccion',
                                        '$cp',
                                        '$localidad',
                                        '$provincia',
                                        '$autor_fotos',
                                        '$tutor')");
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
// create new PDF document
$titulo_PDF = "M A T R Í C U L A";
$pdf = new MYPDF($datos_cen, $titulo_PDF);


// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($datos_cen["centro"]);
$pdf->SetTitle('Impreso Matrícula');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('PDF, secretaría, '. $datos_cen["localidad_centro"].', Impreso Matrícula');

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
<h3>Curso Académico $anno_curso</h3><br>
<h4>$curso_ciclo - GRADO BÁSICO $ciclo</h4><br><br>
<span>Solicita Transporte Escolar: <b>$transporte</b></span><br>
<span>Ha iniciado los estudios en otra comunidad autónoma: <b>$al_nuevo_otra_comunidad</b></span>
HTML1;

if (is_file('../../../docs/fotos/'.$id_nie.'.jpg')) $pdf->Image('../../../docs/fotos/'.$id_nie.'.jpg',15,35,25,33,'','','T');
elseif (is_file('../../../docs/fotos/'.$id_nie.'.jpeg')) $pdf->Image('../../../docs/fotos/'.$id_nie.'.jpeg',15,35,25,33,'','','T');
$pdf->Rect(15,35,25,33,'all');

$YInicio=45;
$pdf->RoundedRect(55,$YInicio,100,25,2,'1111','','','');
$pdf->writeHTMLCell(0, 0, '', $YInicio+2, $html1, 0, 1, false, true, 'C', true);

//Padding dentro de la celda del texto
$pdf->setCellPaddings(0,0,0,0);
//Interlineado
$pdf->setCellHeightRatio(1);

$YInicio+=35;
$XInicioRotulo=17;
$XInicio=12;

//DATOS DEL ALUMNO
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
$pdf->RoundedRect($XInicio-2,$YInicio,185,22,2,'1111','','','');
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

//-------AUTORIZACIONES MATRÍCULA
$YInicio+=7;
$YInicioAutorizaciones=$YInicio;
$pdf->RoundedRect($XInicio-2,$YInicio,185,70,2,'1111','','','');
$pdf->SetXY($XInicioRotulo,$YInicio+1);
$pdf->SetFont('dejavusans', 'B', 10, '', true);
$pdf->Cell(0,0,"AUTORIZACIONES",0,0,'L',0,'',1,false,'','');

$YInicio+=6;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', '', 7, '', true);
$texto_consent=<<<EOT
D./Dña. $tutor, como tutor/a legal del alumno/a $nombre $apellidos, <strong>$autor_fotos</strong> autorizo al IES Universidad Laboral a la  toma de fotografías y de vídeo para que con motivo de las actividades Docentes - y especialmente las del 50  Aniversario- puedan ser usadas  en documentos oficiales o impresos, páginas webs, blogs redes sociales (Twitter Facebook)  o en cualquier otro soporte online del centro o medio de comunicación común y siempre que se use bajo la supervisión del equipo directivo.
Autorización en  conformidad a lo establecido en el artículo 5.1 de la Ley Orgánica 15/1999 de protección de datos de carácter personal y Art. 6.1.a) Reglamento (UE) 2016/679  general de protección de datos.
Esta autorización podrá ser anulada en cualquier instante siempre que se comunique por escrito este aspecto a la Dirección del Centro.
<br><br>    
INFORMACIÓN BÁSICA DE PROTECCIÓN DE DATOS
<br>-Responsable: Viceconsejería de Educación.
<br>-Finalidad: Gestión administrativa y educativa del alumnado de centros docentes de Castilla-La Manchaa, así como el uso de los recursos educativos digitales por parte de la comunidad educativa.
<br>-Legitimación: 6.1.c) Cumplimiento de una obligación legal del Reglamento General de Protección de Datos; 6.1.e) Misión en interés público o ejercicio de poderes públicos del Reglamento General de Protección de Datos. Datos de categoría especial: 9.2.g) el tratamiento es necesario por razones de un interés público esencial del Reglamento General de Protección de Datos. Ley Orgánica 2/2006, de 3 de mayo, de Educación / Ley 7/2010, de 20 de julio, de Educación de Castilla-La Mancha
<br>-Origen de los datos: El propio interesado o su representante legal, administraciones públicas.
<br>-Categoría de los datos: Datos de carácter identificativo: NIF/DNI, nombre y apellidos, dirección, teléfono, firma, firma electrónica, correo electrónico; imagen/voz. Datos especialmente protegidos: Salud. Datos de infracciones administrativas. Otros datos tipificados: Características personales; académicos y profesionales; detalles del empleo; económicos, financieros y de seguros.
<br>-Destinatarios: Existe cesión de datos.
<br>-Derechos: Puede ejercer los derechos de acceso, rectificación o supresión de sus datos, así como otros derechos, tal y como se explica en la información adicional.
<br>-Información adicional: Disponible en la dirección electrónica: https://rat.castillalamancha.es/info/0372
EOT;


$pdf->writeHTMLCell(0, 0, '', $YInicio+2, $texto_consent, 0, 1, false, true, '', true);

//--------FINAL
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$fecha_actual=getdate();
$fecha_firma=$datos_cen["localidad_centro"].", a ".$fecha_actual["mday"]." de ".$meses[$fecha_actual["mon"]-1]." de ".$fecha_actual["year"];
$texto=<<<EOD
<p style="text-align:center">$fecha_firma<br>Nº de registro: $registro</p>
EOD;
$YInicio+=88;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
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


