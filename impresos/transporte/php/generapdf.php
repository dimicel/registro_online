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

function generaRegistro($proc){
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
    return "iesulabto_".$proc."_".date('dmY')."_".$array[0].$array[1].$array[2].$array[3].$array[4].$array[5].$array[6].$array[7];;    
}

if (!isset($_POST["id_nie"])) {
    exit ("Acceso denegado");
}

$curso=$_POST['_cursa'];
$anno_curso=$_POST['anno_curso'];
$id_nie=$_POST['id_nie'];
$email=$_POST['email'];
$apellidos=$_POST['apellidos'];
$nombre=$_POST['nombre'];
$nif_nie=$_POST['nif_nie'];
$email_alumno=$_POST['email_alumno'];
$telef_alumno=$_POST['telef_alumno'];
$direccion=$_POST['direccion'];
$cp=$_POST['cp'];
$localidad=$_POST['localidad'];
$provincia=$_POST['provincia'];
$te_nombre_apellidos=$_POST["te_nombre_apellidos"];
$te_nif_nie=$_POST["te_nif_nie"];
$te_direccion=$_POST["te_direccion"];
$te_localidad=$_POST["te_localidad"];
$te_provincia=$_POST["te_provincia"];
$te_cp=$_POST["te_cp"];
$te_tlf_movil=$_POST["te_tlf_movil"];
$te_tlf_fijo=$_POST["te_tlf_fijo"];
$te_email=$_POST["te_email"];
//$te_distancia=$_POST["te_distancia"];
$te_ruta=$_POST["_t_ruta"];
$_t_apartado=$_POST["_t_apartado"];
$_t_modalidad=$_POST["_t_modalidad"];
if (isset($_POST['sillaruedas']) && $_POST['sillaruedas'] == '1') {
    $sillaruedas=1;
  } else {
    $sillaruedas=0;
  }
$_t_aut_acred_iden=$_POST["_t_aut_acred_iden"];
$_t_aut_acred_domic=$_POST["_t_aut_acred_domic"];
$fecha_registro=date('Y-m-d');

$mysqli->query("delete from transporte where id_nie='$id_nie' and curso='$anno_curso'");

$registro=generaRegistro("transp");
$repite_registro=true;
while ($repite_registro){
    $res=$mysqli->query("select registro from transporte where registro='$registro'");
    if ($mysqli->errno>0) exit("servidor");
    if ($res->num_rows>0){
        $registro= generaRegistro("transp"); 
    }
    else if ($res->num_rows==0){
        $repite_registro=false;
    }
    $res->free();
}


$mysqli->query("insert into transporte (id_nie,
registro,
fecha_registro,
cursa,
curso,
apellidos,
nombre,
email,
id_nif,
tlf_movil,
direccion,
cp,
localidad,
provincia,
tut_nombre_apellidos,
tut_nif_nie,
tut_email,
tut_tlf_fijo,
tut_tlf_movil,
tut_direccion,
tut_cp,
tut_localidad,
tut_provincia,
distancia,
ruta,
apartado,
modalidad,
sillaruedas,
autoriz_identidad,
autoriz_domicilio) 
values ('$id_nie',
'$registro',
'$fecha_registro',
'$curso',
'$anno_curso',
'$apellidos',
'$nombre',
'$email_alumno',
'$nif_nie',
'$telef_alumno',
'$direccion',
'$cp',
'$localidad',
'$provincia',
'$te_nombre_apellidos',
'$te_nif_nie',
'$te_email',
'$te_tlf_fijo',
'$te_tlf_movil',
'$te_direccion',
'$te_cp',
'$te_localidad',
'$te_provincia',
'0',
'$te_ruta',
'$_t_apartado',
'$_t_modalidad',
 $sillaruedas,
'$_t_aut_acred_iden',
'$_t_aut_acred_domic')");

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
		$this->Cell(0,0,"TRANSPORTE ESCOLAR",0,0,'C',0,'',1,false,'T','T');
			
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
$pdf->SetTitle('Impreso Transporte Escolar');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('ulaboral, PDF, secretaría, Toledo, Impreso Transporte Escolar');

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
$cabecera = <<<HTML1
<h3>Estudios: $curso</h3>
<h3>Curso: $anno_curso</h3>
HTML1;

$YInicio=45;

//$pdf->RoundedRect(82,$YInicio,45,15,2,'1111','','','');
$pdf->writeHTMLCell(0, 0, '', $YInicio+2, $cabecera, 0, 1, false, true, 'C', true);

//Padding dentro de la celda del texto
$pdf->setCellPaddings(0,0,0,0);
//Interlineado
$pdf->setCellHeightRatio(1);

$YInicio+=15;
$XInicioRotulo=17;
$XInicio=12;

//DATOS DEL ALUMNO
$YInicio+=8;
$pdf->RoundedRect(10,$YInicio-1,185,40,2,'1111','','','');
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
$pdf->Cell(0,0,"Dirección  de residencia durante el curso",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,$te_direccion,0,0,'L',0,'',1,false,'','');

$YInicio+=4;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"C.P.",0,0,'L',0,'',1,false,'','');
$pdf->SetX(35);
$pdf->Cell(0,0,"Localidad",0,0,'L',0,'',1,false,'','');
$pdf->SetX(105);
$pdf->Cell(0,0,"Provincia",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,$te_cp,0,0,'L',0,'',1,false,'','');
$pdf->SetX(35);
$pdf->Cell(0,0,$te_localidad,0,0,'L',0,'',1,false,'','');
$pdf->SetX(105);
$pdf->Cell(0,0,$te_provincia,0,0,'L',0,'',1,false,'','');

$YInicio+=4;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'BU', 8, '', true);
$pdf->Cell(0,0,"Ruta de transporte y parada: ".$te_ruta,0,0,'L',0,'',1,false,'','');

$YInicio+=4;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'U', 8, '', true);
$pdf->Cell(0,0,"NIF/NIE",0,0,'L',0,'',1,false,'','');
$pdf->SetX(35);
$pdf->Cell(0,0,"Teléfono",0,0,'L',0,'',1,false,'','');
$pdf->SetX(75);
$pdf->Cell(0,0,"Email",0,0,'L',0,'',1,false,'','');

$YInicio+=3;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
$pdf->Cell(0,0,$nif_nie,0,0,'L',0,'',1,false,'','');
$pdf->SetX(35);
$pdf->Cell(0,0,$telef_alumno,0,0,'L',0,'',1,false,'','');
$pdf->SetX(75);
$pdf->Cell(0,0,$email_alumno,0,0,'L',0,'',1,false,'','');


//DATOS TUTOR LEGAL
$YInicio+=7;
$pdf->RoundedRect($XInicio-2,$YInicio,185,25,2,'1111','','','');
$YInicio+=1;
$pdf->SetXY($XInicioRotulo,$YInicio);
$pdf->SetFont('dejavusans', 'B', 10, '', true);
$pdf->Cell(0,0,"DATOS DEL TUTOR/A LEGAL",0,0,'L',0,'',1,false,'','');

$YInicio+=6;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans','U', 8, '', true);
$pdf->Cell(0,0,"Nombre y Apellidos",0,0,'L',0,'',1,false,'','');
$pdf->SetX(95);
$pdf->Cell(0,0,"NIF/NIE",0,0,'L',0,'',1,false,'','');
$pdf->SetFont('dejavusans','B', 8, '', true);

$YInicio+=4;
$pdf->SetXY($XInicio,$YInicio);
$pdf->Cell(0,0,$te_nombre_apellidos,0,0,'L',0,'',1,false,'','');
$pdf->SetX(95);
$pdf->Cell(64,0,$te_nif_nie,0,0,'L',0,'',1,false,'','');

$YInicio+=4;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans','U', 8, '', true);
$pdf->Cell(0,0,"Tlf. Móvil",0,0,'L',0,'',1,false,'','');
$pdf->SetX(50);
$pdf->Cell(0,0,"Tlf. Fijo",0,0,'L',0,'',1,false,'','');
$pdf->SetX(80);
$pdf->Cell(0,0,"Email",0,0,'L',0,'',1,false,'','');

$YInicio+=4;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans','B', 8, '', true);
$pdf->Cell(0,0,$te_tlf_movil,0,0,'L',0,'',1,false,'','');
$pdf->SetX(50);
$pdf->Cell(0,0,$te_tlf_fijo,0,0,'L',0,'',1,false,'','');
$pdf->SetX(80);
$pdf->Cell(0,0,$te_email,0,0,'L',0,'',1,false,'','');

//TIPO DE TRASNPORTE SOLICITADO Y MODALIDAD
$YInicio+=8;

$pdf->SetXY($XInicioRotulo,$YInicio+1);
$pdf->SetFont('dejavusans', 'B', 10, '', true);
$pdf->Cell(0,0,"TIPO DE TRANSPORTE QUE SOLICITA Y MODALIDAD",0,0,'L',0,'',1,false,'','');
$pdf->RoundedRect($XInicio-2,$YInicio,185,30,2,'1111','','','');

if ($_t_apartado=="Art. 3 pto. 2 aptdo. c)"){
    $texto_trans_solic="Artículo 3, punto 2, apartado c) del decreto 119/2012, por el que se regula la organización y funcionamiento del transporte escolar: ";
    $texto_trans_solic.="Residir en pedanía o población rural dispersa perteneciente a la misma localidad donde éste está ubicado, siempre que cumpla el requisito de distancia superior a cinco kilómetros, desde su domicilio al límite del casco urbano, contemplándose la consideración excepcional de casos puntuales debidamente justificados.";
}
elseif ($_t_apartado=="Art. 3 pto. 2 aptdo. d)"){
    $texto_trans_solic="Artículo 3, punto 2, apartado d) del decreto 119/2012, por el que se regula la organización y funcionamiento del transporte escolar: ";
    $texto_trans_solic.="Haber sido escolarizados de oficio por la Consejería con competencias en materia de educación no universitaria en centros públicos de la Comunidad localizados en municipios distintos al de su residencia.";
}
elseif ($_t_apartado=="Art. 3 pto. 2 aptdo. e)"){
    $texto_trans_solic="Artículo 3, punto 2, apartado e) del decreto 119/2012, por el que se regula la organización y funcionamiento del transporte escolar: ";
    $texto_trans_solic.="Estar interno en residencias no universitarias dependientes de la Consejería con competencias en materia de educación no universitaria, por no contar en su localidad de origen, con centro o plazas vacantes en su nivel de escolarización. En este caso el derecho a este servicio de transporte será exclusivamente los fines de semana.";
}

if ($_t_modalidad=="diario")$texto_modalidad="Transporte diario";
elseif ($_t_modalidad=="semana")$texto_modalidad="Transporte de fin de semana";

$YInicio+=6;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans','U', 8, '', true);
if($sillaruedas==1) $pdf->Cell(0,0,"Persona usuaria de silla de ruedas: SI",0,0,'L',0,'',1,false,'','');
else  $pdf->Cell(0,0,"Persona usuaria de silla de ruedas: NO",0,0,'L',0,'',1,false,'','');


$YInicio+=6;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans','U', 8, '', true);
$pdf->Cell(0,0,"Tipo de transporte solicitado",0,0,'L',0,'',1,false,'','');

$YInicio+=4;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans','', 8, '', true);
$pdf->MultiCell(0,0,$texto_trans_solic,0,'L',0,1,'','',true,0,false,false,'');

$YInicio+=15;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans','U', 8, '', true);
$pdf->Cell(0,0,"Modalidad:",0,0,'L',0,'',1,false,'','');
$pdf->SetX(30);
$pdf->SetFont('dejavusans','B', 8, '', true);
$pdf->Cell(0,0,$texto_modalidad,0,0,'L',0,'',1,false,'','');


//DECLARACIONES RESPONSABLES

$YInicio+=7;
$pdf->RoundedRect($XInicio-2,$YInicio,185,38,2,'1111','','','');
$pdf->SetXY($XInicioRotulo,$YInicio+1);
$pdf->SetFont('dejavusans', 'B', 10, '', true);
$pdf->Cell(0,0,"DECLARACIONES RESPONSABLES",0,0,'L',0,'',1,false,'','');

$YInicio+=6;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', '', 7, '', true);
$declarac_resp=<<<EOT
EL SOLICITANTE DECLARA:
<ul>
    <li>Ser ciertos los datos consignados en la presente solicitud, compremetiéndose a probar documentalmente los mismos, si se le requiere para ello.</li>
    <li>Conocer sus derechos y deberes en relación al transporte, relacionados en el Anexo III del decreto 119/2012 por el que se regula dicho servicio.</li>
    <li>Conocer que el incumplimiento de las normas básicas de convivencia, puede dar lugar a la suspensión cautelar de asistencia al transporte.</li>
    <li>Conocer la obligatoriedad del uso del cinturón de seguridad, en los autobuses que lo tengan instalado.</li>
    <li>Comprometerse a cumplir el horario en el uso del servicio de transporte escolar y comunicar al conductor o acompañante de la ruta la no asistencia al Centro.</li>
</ul>
EOT;
$pdf->writeHTMLCell(0,0,'',$YInicio,$declarac_resp, 0, 1, false, true, 'L', true);

//AUTORIZACIONES
$YInicio+=34;
$pdf->RoundedRect($XInicio-2,$YInicio,185,19,2,'1111','','','');
$pdf->SetXY($XInicio,$YInicio+1);
$pdf->SetFont('dejavusans', 'B', 10, '', true);
$pdf->Cell(0,0,"AUTORIZACIONES",0,0,'L',0,'',1,false,'','');
$texto_autor=<<<EOT
El solicitantes AUTORIZA a la Dirección Provincial de Educación, Cultura y Deportes en la provincia de Toledo para que pueda proceder a la comprobación y verificación de los siguientes datos:
                    
                    $_t_aut_acred_iden: Los acreditativos de identidad.
                    $_t_aut_acred_domic: Los acreditativos del domicilio o residencia.
EOT;
$pdf->SetXY($XInicio,$YInicio+5);
$pdf->SetFont('dejavusans', '', 7, '', true);
$pdf->MultiCell(0,0,$texto_autor,0,'L',0,1,'','',true,0,false,false,'');

//FINAL
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$fecha_firma="Toledo, a ".substr($fecha_registro,8,2)." de ".$meses[intval(substr($fecha_registro,6,2))-1]." de ".substr($fecha_registro,0,4);
$texto=<<<EOD
<p style="text-align:center">$fecha_firma<br>Nº de registro: $registro</p>
EOD;
$YInicio+=37;
$pdf->SetXY($XInicio,$YInicio);
$pdf->SetFont('dejavusans', 'B', 8, '', true);
//$pdf->Cell(0,0,"Nº registro " . $texto,0,0,'C',0,'',1,false,'T','T');
$pdf->writeHTMLCell(180,0,$XInicio,$YInicio,$texto,0,0,false,true,'',true);

//SI YA HAY ALGUNA SOLICITUD BORRA EL ARCHIVO
$dir = __DIR__."/../../../docs/".$id_nie."/transporte_escolar"."/".$anno_curso.'/';     
$handle = opendir($dir);
while ($file = readdir($handle)) {
	if (is_file($dir.$file)) unlink($dir.$file);
}
closedir($dir);
//GENERA EL ARCHIVO NUEVO
$nombre_fichero=$registro . '.pdf';
if (!is_dir(__DIR__."/../../../docs/".$id_nie))mkdir(__DIR__."/../../../docs/".$id_nie,0777);
if(!is_dir(__DIR__."/../../../docs/".$id_nie."/transporte_escolar"))mkdir(__DIR__."/../../../docs/".$id_nie."/transporte_escolar",0777);
if(!is_dir(__DIR__."/../../../docs/".$id_nie."/transporte_escolar"."/".$anno_curso))mkdir(__DIR__."/../../../docs/".$id_nie."/transporte_escolar"."/".$anno_curso,0777);
$ruta=__DIR__."/../../../docs/".$id_nie."/"."transporte_escolar/".$anno_curso."/". $nombre_fichero;
$pdf->Output($ruta, 'F');
//FIN GENERA PDF

exit ("envio_ok");

