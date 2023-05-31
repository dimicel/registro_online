<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
set_time_limit(0);
ini_set('memory_limit', '-1');

require_once('tcpdf/tcpdf_include.php');
include("conexion.php");
//header('Content-type: application/pdf');

$tabla_mat=[
	'1eso'=>'mat_1eso',
	'2eso'=>'mat_2eso',
	'3eso'=>'mat_3eso',
	'4eso'=>'mat_4eso',
	'2esopmar'=>'mat_2esopmar',
	'3esopmar'=>'mat_3esopmar',
	'1bach_c'=>'mat_1bach_c',
	'1bach_hcs'=>'mat_1bach_hcs',
	'2bach_c'=>'mat_2bach_c',
	'2bach_hcs'=>'mat_2bach_hcs'
];

$estudios=[
	'1eso'=>'1º ESO',
	'2eso'=>'2º ESO',
	'3eso'=>'3º ESO',
	'4eso'=>'4º ESO',
	'2esopmar'=>'2º ESO PMAR',
	'3esopmar'=>'3º ESO PMAR',
	'1bach_c'=>'1º BACHILLERATO CIENCIAS',
	'1bach_hcs'=>'1º BACHILLERATO HH.CC.SS.',
	'2bach_c'=>'2º BACHILLERATO CIENCIAS',
	'2bach_hcs'=>'2º BACHILLERATO HH.CC.SS.'
];

$mat_reg_pag=[
	'1eso'=>3,
	'2eso'=>3,
	'3eso'=>1,
	'4eso'=>1,
	'2esopmar'=>3,
	'3esopmar'=>1,
	'1bach_c'=>1,
	'1bach_hcs'=>1,
	'2bach_c'=>1,
	'2bach_hcs'=>1,
	'ciclos'=>2,
	'fpb'=>2
];

$tipo_listado=$_POST["tipo_listado"];
$formulario=$_POST["formulario"]; 
$registros=json_decode($_POST["registros"]);
$curso=$_POST["curso_listado"];
$orden_campo=$_POST["orden_campo"];
$orden_direccion=$_POST["orden_direccion"];
$orden_texto=$_POST["orden_texto"];
$matriculas_consolidadas=$_POST["mat_consolidadas"];
$curso_mat=$_POST["mat_curso"];
$id_nie="";
$grado=$_POST["grado"];
$ciclo=$_POST["ciclo"];
$curso_ciclo=$_POST["curso_ciclo"];
$turno=$_POST["turno"];
$menor28=true;
$tabla=$tabla_mat[$curso_mat];
$reg_por_pag=$mat_reg_pag[$curso_mat];

$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

class MYPDF extends TCPDF {
	//Page header
	public function Header() {
		if($_POST["formulario"]=="matricula"){
			if ($_POST["mat_consolidadas"]=="consolidadas") $c=" - CONSOLIDADAS";
			elseif ($_POST["mat_consolidadas"]=="no consolidadas") $c=" - MATRÍCULAS NUEVAS";
			else $c='';
		}
		$ctemp=$GLOBALS['estudios'];
		$encab_formulario=[
			"revision_calificacion"=>"REVISIÓN DE CALIFICACIÓN",
			"revision_examen"=>"REVISIÓN DE EXAMEN",
			"matricula"=>"MATRÍCULA ".$ctemp[$_POST['mat_curso']].$c,
			"matricula_ciclos"=>"MATRÍCULA ".$_POST["curso_ciclo"]."-".$_POST['grado']." ".$_POST['ciclo']." (".$_POST['turno'].")",
			"matricula_fpb"=>"MATRÍCULA ".$_POST["curso_ciclo"]."-FPB ".$_POST['ciclo']
		];
		// Logo
		$image_file = '../recursos/mini_escudo.jpg';
		$this->Image($image_file, 10, 10, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

		$this->SetFont('helvetica', '', 8);
		$this->SetXY(-20,10);
		$this->Cell(0, 0, 'Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
				
		$this->SetFont('helvetica', '', 12);
		// Title
		if ($_POST["formulario"]=="matricula"){
			$encab = "LISTADO DE SOLICITUDES - CURSO " . $_POST["curso_listado"] . "<br>" . $encab_formulario[$_POST["formulario"]];
		}
		elseif($_POST["formulario"]=="matricula_ciclos"){
			$encab = "LISTADO DE SOLICITUDES - CURSO " . $_POST["curso_listado"] . "<br>" . $encab_formulario[$_POST["formulario"]];
		}
		elseif($_POST["formulario"]=="matricula_fpb"){
			$encab = "LISTADO DE SOLICITUDES - CURSO " . $_POST["curso_listado"] . "<br>" . $encab_formulario[$_POST["formulario"]];
		}
		else{
			$encab = "LISTADO DE SOLICITUDES - CURSO " . $_POST["curso_listado"] . "<br>" . $encab_formulario[$_POST["formulario"]] . "<br>Ordenado por: " . $_POST["orden_texto"];
		} 
		$this->writeHTMLCell(0, 0, 40, 20, $encab, 0, 1, 0, true, '', true);	
	}
}

if ($formulario=='matricula'){
	if($tipo_listado=="todas") $consulta="select * from $tabla_mat[$curso_mat] where curso='$curso' order by apellidos,nombre ASC";
	else if($tipo_listado=="listadas") $consulta="select * from $tabla_mat[$curso_mat] where listado=true and curso='$curso' order by apellidos,nombre ASC";
	else if($tipo_listado=="no listadas") $consulta="select * from $tabla_mat[$curso_mat] where listado=false and  curso='$curso' order by apellidos,nombre ASC";
	else if ($tipo_listado=="seleccionadas"){
		$consulta="select * from $tabla_mat[$curso_mat] where ";
		for ($i=0;$i<count($registros);$i++){
			if ($i==0) $consulta .= "registro='$registros[$i]'";
			else $consulta .= " or registro='$registros[$i]'";
		}
		$consulta .= " order by apellidos,nombre";
	}	
}
elseif($formulario=='matricula_ciclos'){
	if($tipo_listado=="todas") $consulta="select * from mat_ciclos where curso='$curso' and ciclo='$ciclo' and curso_ciclo='$curso_ciclo' and turno='$turno' order by apellidos,nombre ASC";
	else if($tipo_listado=="listadas") $consulta="select * from mat_ciclos where listado=true and curso='$curso' and ciclo='$ciclo' and curso_ciclo='$curso_ciclo' and turno='$turno'  order by apellidos,nombre ASC";
	else if($tipo_listado=="no listadas") $consulta="select * from mat_ciclos where listado=false and  curso='$curso' and ciclo='$ciclo' and curso_ciclo='$curso_ciclo' and turno='$turno'  order by apellidos,nombre ASC";
	else if ($tipo_listado=="seleccionadas"){
		$consulta="select * from mat_ciclos where ";
		for ($i=0;$i<count($registros);$i++){
			if ($i==0) $consulta .= "registro='$registros[$i]'";
			else $consulta .= " or registro='$registros[$i]'";
		}
		$consulta .= " order by apellidos,nombre";
	}	

}elseif($formulario=='matricula_fpb'){
	if($tipo_listado=="todas") $consulta="select * from mat_fpb where curso='$curso' and ciclo='$ciclo' and curso_ciclo='$curso_ciclo' order by apellidos,nombre ASC";
	else if($tipo_listado=="listadas") $consulta="select * from mat_fpb where listado=true and curso='$curso' and ciclo='$ciclo' and curso_ciclo='$curso_ciclo'  order by apellidos,nombre ASC";
	else if($tipo_listado=="no listadas") $consulta="select * from mat_fpb where listado=false and  curso='$curso' and ciclo='$ciclo' and curso_ciclo='$curso_ciclo' order by apellidos,nombre ASC";
	else if ($tipo_listado=="seleccionadas"){
		$consulta="select * from mat_fpb where ";
		for ($i=0;$i<count($registros);$i++){
			if ($i==0) $consulta .= "registro='$registros[$i]'";
			else $consulta .= " or registro='$registros[$i]'";
		}
		$consulta .= " order by apellidos,nombre";
	}	

}
else {
	if($tipo_listado=="todas") $consulta="select * from $formulario where curso='$curso' order by $orden_campo $orden_direccion";
	else if($tipo_listado=="listadas") $consulta="select * from $formulario where listado=true and curso='$curso' order by $orden_campo $orden_direccion";
	else if($tipo_listado=="no listadas") $consulta="select * from $formulario where listado=false and  curso='$curso' order by $orden_campo $orden_direccion";
	else if ($tipo_listado=="seleccionadas"){
		$consulta="select * from $formulario where ";
		for ($i=0;$i<count($registros);$i++){
			if ($i==0) $consulta .= "registro='$registros[$i]'";
			else $consulta .= " or registro='$registros[$i]'";
		}
		$consulta .= " order by $orden_campo $orden_direccion";
	}	
}


////PREPARA DOCUMENTO PDF/////////////////

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('IES Universidad Laboral');
$pdf->SetTitle('');
$pdf->SetSubject('Secretaría');
$pdf->SetKeywords('ulaboral, PDF, secretaría, Toledo');

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
/////FIN PREPARA DOCUMENTO PDF//////////////////////////////////


$sol=$mysqli->query($consulta);

if ($sol->num_rows<=0) {
	$texto="NO EXISTEN REGISTROS QUE LISTAR";
	$pdf->AddPage();
	$pdf->setCellPaddings(0,0,0,0);
	$pdf->setCellHeightRatio(2);
	$pdf->writeHTMLCell(180,0,80,60,$texto,0,0,false,true,'',true);
}
else{
	$contador=0;
	while($registro=$sol->fetch_array(MYSQLI_ASSOC)){
		if(substr(strtoupper($registro["id_nie"]),0,1)=="P") continue;
		if ($formulario=="revision_examen"){
			$texto=revision_examen($registro);
			$reg_por_pag=3;
		}
		elseif($formulario=="revision_calificacion"){
			$texto=revision_calificacion($registro);
			$reg_por_pag=2;
		}
		elseif($formulario=="matricula"){
			$texto=matricula($registro);
		}
		elseif($formulario=="matricula_ciclos"){
			$texto=matriculaCiclos($registro);
		}
		elseif($formulario=="matricula_fpb"){
			$texto=matriculaFpb($registro);
		}
		elseif($formulario=="convalidaciones"){
			$texto=convalidaciones($registro);
			$reg_por_pag=25;
		}
		if($contador%$reg_por_pag==0) {
			$pdf->AddPage();
			//Padding dentro de la celda del texto
			$pdf->setCellPaddings(0,0,0,0);
			//Interlineado
			$pdf->setCellHeightRatio(2);
			$Y=40;
			$X=10;
		}
		else{
			$Y+=$pdf->getLastH();
		}
	
		if ($formulario!="matricula" && $formulario!="matricula_ciclos" && $formulario!="matricula_fpb"){
			$pdf->writeHTMLCell(180,0,$X,$Y,$texto,0,0,false,true,'',true);
		}
		else {
			$id_nie=$registro["id_nie"];
			if (is_file('../fotos/'.$id_nie.'.jpg')) $pdf->Image('../fotos/'.$id_nie.'.jpg',180,$Y,20,26,'','','T');
			elseif (is_file('../fotos/'.$id_nie.'.jpeg')) $pdf->Image('../fotos/'.$id_nie.'.jpeg',180,$Y,20,26,'','','T');
			$pdf->Rect(180,$Y,20,26,'all');
			$pdf->writeHTMLCell(180,0,$X,$Y,$texto,0,0,false,true,'',true);
			if (strpos($curso_mat,'bach')>=0 ||$curso_mat=='3eso' ||$curso_mat=='4eso'||$curso_mat=='3esopmar' ||$curso_mat=='ciclos' ||$curso_mat=='fpb'){
				$Y+=$pdf->getLastH()+10;
				if(file_exists ("../seguro/".$curso."/".$id_nie.".jpg")){
					$pdf->image("../seguro/".$curso."/".$id_nie.".jpg",$X,$Y,150,75,'','','',true);
					$Y+=90;
					$pdf->SetXY($X,$Y);
					$pdf->writeHTMLCell(180,0,$X,$Y,"#################################################################",0,0,false,true,'',true);
				}
				elseif(file_exists ("../seguro/".$curso."/".$id_nie.".jpeg")){
					$pdf->image("../seguro/".$curso."/".$id_nie.".jpeg",$X,$Y,150,75,'','','',true);
					$Y+=90;
					$pdf->SetXY($X,$Y);
					$pdf->writeHTMLCell(180,0,$X,$Y,"#################################################################",0,0,false,true,'',true);
				}
				else {
					if ($curso_mat="ciclos" && !$menor28){
						$pdf->SetXY($X,$Y);
						$pdf->writeHTMLCell(180,0,$X,$Y,"NO PAGA SEGURO ESCOLAR POR SER MAYOR DE 28 AÑOS",0,0,false,true,'',true);
					}
					else {
						$pdf->SetXY($X,$Y);
						$pdf->writeHTMLCell(180,0,$X,$Y,"NO TIENE SUBIDO RESGUARDO DEL SEGURO ESCOLAR",0,0,false,true,'',true);
					}
				}
			}
		}
		$contador++;
	}
}

$archivo=$formulario."_".$tipo_listado.".pdf";
$pdf->Output($archivo,"I");
if ($sol->num_rows<=0) die();


if ($formulario=="matricula"){
	if ($tipo_listado=="todas") $mysqli->query("update $tabla set listado=true where curso='$curso'");
	elseif($tipo_listado=="no listadas") $mysqli->query("update $tabla set listado=true where listado=false and curso='$curso'");
	elseif($tipo_listado=="listadas");//No hace nada puesto que ya están marcadas como listadas
	elseif($tipo_listado=="seleccionadas"){
		$actualiza="update $tabla set listado=true where curso='$curso' and (";
		for ($i=0;$i<count($registros);$i++){
			if ($i==0) $actualiza .= "registro='$registros[$i]'";
			else $actualiza .= " or registro='$registros[$i]'";
		}
		$actualiza.=")";
		$mysqli->query($actualiza);
	}
}
elseif($formulario=="matricula_ciclos"){
	if ($tipo_listado=="todas") $mysqli->query("update mat_ciclos set listado=true where curso='$curso' and ciclo='$ciclo' and curso_ciclo='$curso_ciclo' and turno='$turno'");
	elseif($tipo_listado=="no listadas") $mysqli->query("update mat_ciclos set listado=true where listado=false and curso='$curso' and ciclo='$ciclo' and curso_ciclo='$curso_ciclo' and turno='$turno'");
	elseif($tipo_listado=="listadas");//No hace nada puesto que ya están marcadas como listadas
	elseif($tipo_listado=="seleccionadas"){
		$actualiza="update mat_ciclos set listado=true where curso='$curso' and (";
		for ($i=0;$i<count($registros);$i++){
			if ($i==0) $actualiza .= "registro='$registros[$i]'";
			else $actualiza .= " or registro='$registros[$i]'";
		}
		$actualiza.=")";
		$mysqli->query($actualiza);
	}
} elseif($formulario=="matricula_fpb"){
	if ($tipo_listado=="todas") $mysqli->query("update mat_fpb set listado=true where curso='$curso' and ciclo='$ciclo' and curso_ciclo='$curso_ciclo' ");
	elseif($tipo_listado=="no listadas") $mysqli->query("update mat_fpb set listado=true where listado=false and curso='$curso' and ciclo='$ciclo' and curso_ciclo='$curso_ciclo' ");
	elseif($tipo_listado=="listadas");//No hace nada puesto que ya están marcadas como listadas
	elseif($tipo_listado=="seleccionadas"){
		$actualiza="update mat_fpb set listado=true where curso='$curso' and (";
		for ($i=0;$i<count($registros);$i++){
			if ($i==0) $actualiza .= "registro='$registros[$i]'";
			else $actualiza .= " or registro='$registros[$i]'";
		}
		$actualiza.=")";
		$mysqli->query($actualiza);
	}
}
else{
	if ($tipo_listado=="todas") $mysqli->query("update $formulario set listado=true where curso='$curso'");
	elseif($tipo_listado=="no listadas") $mysqli->query("update $formulario set listado=true where listado=false and  curso='$curso'");
	elseif($tipo_listado=="listadas");//No hace nada puesto que ya están marcadas como listadas
	elseif($tipo_listado=="seleccionadas"){
		$actualiza="update $formulario set listado=true where curso='$curso' and (";
		for ($i=0;$i<count($registros);$i++){
			if ($i==0) $actualiza .= "registro='$registros[$i]'";
			else $actualiza .= " or registro='$registros[$i]'";
		}
		$actualiza.=")";
		$mysqli->query($actualiza);
	}
}




#///////////////////////////////////////////////////////////////
#//////CUERPO REVISIÓN EXAMEN
#///////////////////////////////////////////////////////////////

function revision_examen($r){
	$registro=$r["registro"];
	$fecha_actual=$r["fecha_registro"];
	$curso=$r["curso"];
	$usuario=$_POST['nombre'];
	$id_nif=strtoupper($r['id_nif']);
	$tratamiento=strtoupper($r['tratamiento']);
	$nombre=strtoupper($r['nombre']);
	$tipo_doc=strtoupper($r['tipo_doc']);
	$numero_doc=strtoupper($r['numero_doc']);
	$en_calidad_de=$r["en_calidad_de"];
	$del_alumno=$r["del_alumno"];
	$cursa=$r["cursa"];
	$departamento=$r["departamento"];
	$profesor=$r["profesor"];
	$asignatura=$r["asignatura"];
	$fecha_examen=$r["fecha"];
	if ($tipo_doc=="NIF"){
		$num_documento="NIF/NIE NÚMERO " . $numero_doc;
	}
	elseif ($tipo_doc=="PASS"){
		$num_documento="NÚMERO DE PASAPORTE " . $numero_doc;
	}
	if (trim($r["incidencias"]=="")){
		$incidencias="";
		$incid_cabecera="";
	}
	else {
		$incidencias="<b>INCIDENCIAS:</b>".trim($r["incidencias"]);
		$incid_cabecera="<b>¡¡¡ATENCIÓN!!! ESTA SOLICITUD TIENE INCIDENCIAS</b><br>";
	}

	if ($en_calidad_de=="ALUMNO"){
$t=<<<EOD
$incid_cabecera
<b>Nº registro: $registro</b><br>
<b>Nombre:$nombre</b><br>
<b>$num_documento  Teléfono:$telefono</b><br>
<b>Esta cursando: $cursa</b><br>
<b>Departamento al que reclama: $departamento</b><br>
<b>Profesor: $profesor</b><br>
<b>Asignatura:  $asignatura  Fecha del examen: $fecha_examen</b><br>
$incidencias

<br>#######################################################<br>
EOD;

	}
	else {
$t=<<<EOD
$incid_cabecera
<b>Nº registro: $registro</b><br>
<b>Nombre:$nombre</b><br>
<b>En calidad de: $en_calidad_de del alumno $del_alumno<b><br>
<b>$num_documento  Teléfono:$telefono</b><br>
<b>Esta cursando: $cursa</b><br>
<b>Departamento al que reclama: $departamento</b><br>
<b>Profesor: $profesor</b><br>
<b>Asignatura:  $asignatura  Fecha del examen: $fecha_examen</b><br>
$incidencias

<br>#######################################################<br>
EOD;
	}
	
	return $t;
}



#///////////////////////////////////////////////////////////////
#//////CUERPO REVISIÓN CALIFICACIÓN
#///////////////////////////////////////////////////////////////

function revision_calificacion($r){
	$registro=$r["registro"];
	$fecha_actual=$r["fecha_registro"];
	$curso=$r["curso"];
	$usuario=$_POST['nombre'];
	$id_nif=strtoupper($r['id_nif']);
	$tratamiento=strtoupper($r['tratamiento']);
	$nombre=strtoupper($r['nombre']);
	$tipo_doc=strtoupper($r['tipo_doc']);
	$numero_doc=strtoupper($r['numero_doc']);
	$domicilio=strtoupper($r['domicilio']);
	$telefono=strtoupper($r['telefono']);
	$poblacion=strtoupper($r['poblacion']);
	$cp=strtoupper($r['cp']);
	$provincia=strtoupper($r['provincia']);
	$ciclo_grado=strtoupper($r['ciclo_grado']);
	$ciclo_nombre=strtoupper($r['ciclo_nombre']);
	$modulo=strtoupper($r["modulo"]);
	$nota=strtoupper($r["nota"]);
	$motivos=$r["motivos"];
	$num_documento="";
	if ($tipo_doc=="NIF"){
		$num_documento="NIF/NIE NÚMERO " . $numero_doc;
	}
	elseif ($tipo_doc=="PASS"){
		$num_documento="NÚMERO DE PASAPORTE " . $numero_doc;
	}
	if (trim($r["incidencias"]=="")){
		$incidencias="";
		$incid_cabecera="";
	}
	else {
		$incidencias="<b>INCIDENCIAS:</b>".trim($r["incidencias"]);
		$incid_cabecera="<b>¡¡¡ATENCIÓN!!! ESTA SOLICITUD TIENE INCIDENCIAS</b>";
	}
$t=<<<EOD
$incid_cabecera
<b>Nº registro: $registro</b><br>
<b>Nombre:</b>$nombre<br>
<b>$num_documento  Teléfono:</b>$telefono<br>
<b>domicilio:</b>$domicilio<br>
<b>Población:</b>$poblacion<br>
<b>C.P.:</b>$cp  <b>Provincia:</b>$provincia<br>
<b>Ciclo de Grado</b> $ciclo_grado  <b>Nombre: </b>$ciclo_nombre<br>
<b>Módulo: </b>$modulo  <b>Nota: </b>$nota<br>
<b>Motivos por los que reclama:</b><br>
$motivos<br>
$incidencias

<br>#######################################################<br>
EOD;
	
	return $t;
}


#///////////////////////////////////////////////////////////////
#//////CUERPO CONVALIDACIONES
#///////////////////////////////////////////////////////////////

function convalidaciones($r){
	$id_nie=$r['id_nie'];
	$registro=$r["registro"];
	$usuario=$r['apellidos'].", ".$r['nombre'];
	$organismoDestino=$r['organismo_destino'];
$t=<<<EOD
<b>NIE:</b> $id_nie <b>  Nº registro:</b> $registro<b>  Convalidación para:</b> $organismoDestino<br>
<b>Alumno:</b>$usuario<br>
<br>#######################################################<br>
EOD;
	
	return $t;
}



#///////////////////////////////////////////////////////////////
#//////CUERPO MATRÍCULA
#///////////////////////////////////////////////////////////////

function matricula($r){
	$registro=$r["registro"];
	$consolida=$r["consolida_premat"];
	$id_nie=$r["id_nie"];
	$curso=$r["curso"];
	$apellidos=$r["apellidos"];
	$nombre=$r["nombre"];
	$c_mat=$_POST["mat_curso"];
	$GLOBALS['id_nie']=$id_nie;

	if ($consolida=="Si")$rotulo_consolida="<b>¡¡¡PREMATRÍCULA CONSOLIDADA!!!</b><br>";
	else $rotulo_consolida="";

	$t1=$rotulo_consolida;
	$t1.="NIE: <b>$id_nie</b>&nbsp;&nbsp;&nbsp;&nbsp;Nº registro: <b>$registro</b><br>";
	$t1.="Apellidos y Nombre:<b>$apellidos, $nombre</b><br>";
	
	//////////////////////////////////////////////////////////////////////////////////
	///////1º ESO
	//////////////////////////////////////////////////////////////////////////////////
	if($c_mat=="1eso"){
		$curso_anterior=$r["curso_anterior"];
		$grupo_curso_anterior=$r["grupo_curso_anterior"];
		$prog_ling=$r["prog_ling"];
		$rel_valores_et=$r["rel_valores_et"];
		$primer_idioma=$r["1_lengua_extr"];
		$optativa1=$r["optativa1"];
		$optativa2=$r["optativa2"];
		$optativa3=$r["optativa3"];
		$optativa4=$r["optativa4"];

		$t2=<<<EOD
Curso Anterior: <b>$curso_anterior</b>  Grupo: <b>$grupo_curso_anterior</b><br>
Programa Lingüístico: <b>$prog_ling</b><br>
<b>MATERIAS</b><br>
- Religión/Valores Éticos: <b>$rel_valores_et</b><br>
- 1ª Lengua Extranjera:<b>$primer_idioma</b><br>
<table>
<tr>
	<td width="60px">OPTATIVAS:</td>
	<td>
		<ol>
			<li value="1"><b>$optativa1</b></li>
			<li><b>$optativa2</b></li>
			<li><b>$optativa3</b></li>
			<li><b>$optativa4</b></li>
		</ol>
	</td>
</tr>
</table>

<br>########################################################################<br>
EOD;

	}
	//////////////////////////////////////////////////////////////////////////////////
	/////////////2º ESO
	//////////////////////////////////////////////////////////////////////////////////
	elseif($c_mat=="2eso"){
		$curso_anterior=$r["curso_anterior"];
		$grupo_curso_anterior=$r["grupo_curso_anterior"];
		$prog_ling=$r["prog_ling"];
		$rel_valores_et=$r["rel_valores_et"];
		$primer_idioma=$r["1_lengua_extr"];
		$optativa1=$r["optativa1"];
		$optativa2=$r["optativa2"];
		$optativa3=$r["optativa3"];
		$optativa4=$r["optativa4"];
		$optativa5=$r["optativa5"];
		$t2=<<<EOD
Curso Anterior: <b>$curso_anterior</b>  Grupo: <b>$grupo_curso_anterior</b><br>
Programa Lingüístico: <b>$prog_ling</b><br>
<b>MATERIAS</b><br>
- Religión/Valores Éticos: <b>$rel_valores_et</b><br>
- 1ª Lengua Extranjera:<b>$primer_idioma</b><br>
<table>
	<tr>
		<td width="60px">OPTATIVAS:</td>
		<td>
			<ol>
				<li value="1"><b>$optativa1</b></li>
				<li><b>$optativa2</b></li>
				<li><b>$optativa3</b></li>
				<li><b>$optativa4</b></li>
				<li><b>$optativa5</b></li>
			</ol>
		</td>
	</tr>
</table>

<br>########################################################################<br>
EOD;
	}
	//////////////////////////////////////////////////////////////////////////////////
	////////////3º ESO
	//////////////////////////////////////////////////////////////////////////////////
	elseif($c_mat=="3eso"){
		$curso_anterior=$r["curso_anterior"];
		$grupo_curso_anterior=$r["grupo_curso_anterior"];
		$prog_ling=$r["prog_ling"];
		$rel_valores_et=$r["rel_valores_et"];
		$primer_idioma=$r["1_lengua_extr"];
		$matematicas=$r["matematicas"];
		$optativa1=$r["optativa1"];
		$optativa2=$r["optativa2"];
		$optativa3=$r["optativa3"];
		$optativa4=$r["optativa4"];

		$t2=<<<EOD
Curso Anterior: <b>$curso_anterior</b>  Grupo: <b>$grupo_curso_anterior</b><br>
Programa Lingüístico: <b>$prog_ling</b><br>
<b>MATERIAS</b><br>
- Religión/Valores Éticos: <b>$rel_valores_et</b><br>
- 1ª Lengua Extranjera:<b>$primer_idioma</b><br>
- Matemáticas <b>$matematicas</b><br>
<table>
<tr>
	<td width="60px">OPTATIVAS:</td>
	<td>
		<ol>
			<li value="1"><b>$optativa1</b></li>
			<li><b>$optativa2</b></li>
			<li><b>$optativa3</b></li>
			<li><b>$optativa4</b></li>
		</ol>
	</td>
</tr>
</table>

EOD;
	}
	//////////////////////////////////////////////////////////////////////////////////
	////////////4º ESO
	//////////////////////////////////////////////////////////////////////////////////
	elseif($c_mat=="4eso"){
		$curso_anterior=$r["curso_anterior"];
		$grupo_curso_anterior=$r["grupo_curso_anterior"];
		$prog_ling=$r["prog_ling"];
		$rel_valores_et=$r["rel_valores_et"];
		$primer_idioma=$r["1_lengua_extr"];
		$modalidad=$r["modalidad"];
		$espec_oblig=$r["espec_oblig"];
		$troncales_opcion1=$r["troncales_opcion1"];
		$troncales_opcion2=$r["troncales_opcion2"];
		$espec_opcion1=$r["espec_opcion1"];
		$espec_opcion2=$r["espec_opcion2"];
		$espec_opcion3=$r["espec_opcion3"];
		$espec_opcion4=$r["espec_opcion4"];
		$espec_opcion5=$r["espec_opcion5"];
		$espec_opcion6=$r["espec_opcion6"];
		$espec_opcion7=$r["espec_opcion7"];
		$espec_opcion8=$r["espec_opcion8"];
		$espec_opcion9=$r["espec_opcion9"];

		$t2=<<<EOD
Curso Anterior: <b>$curso_anterior</b>  Grupo: <b>$grupo_curso_anterior</b><br>
Programa Lingüístico: <b>$prog_ling</b><br>
<b>MATERIAS</b><br>
- Modalidad: <b>$modalidad</b> <br>
- 1ª Lengua Extranjera:<b>$primer_idioma</b><br>
- Espec.Obligat.: <b>$rel_valores_et</b> y <b>$espec_oblig</b><br>
- Troncales de Opción: <b>$troncales_opcion1</b> y <b>$troncales_opcion2</b><br>
<table>
<tr><td colspan="2">Espec.Opción y Libre Conf.</td></tr>
<tr>
	<td>
		<ol>
			<li value="1"><b>$espec_opcion1</b></li>
			<li><b>$espec_opcion2</b></li>
			<li><b>$espec_opcion3</b></li>
			<li><b>$espec_opcion4</b></li>
			<li><b>$espec_opcion5</b></li>
		</ol>
	</td>
	<td>
		<ol>
			<li value="6"><b>$espec_opcion6</b></li>
			<li><b>$espec_opcion7</b></li>
			<li><b>$espec_opcion8</b></li>
EOD;
		if ($modalidad!="Aplicadas") $t2.="<li><b>$espec_opcion9</b></li>";
		$t2.="</ol></td></tr></table>";
	}
	//////////////////////////////////////////////////////////////////////////////////
	////////////////////2º ESO PMAR
	//////////////////////////////////////////////////////////////////////////////////
	elseif($c_mat=="2esopmar"){
		$curso_anterior=$r["curso_anterior"];
		$grupo_curso_anterior=$r["grupo_curso_anterior"];
		$rel_valores_et=$r["rel_valores_et"];
		$optativa1=$r["optativa1"];
		$optativa2=$r["optativa2"];
		$optativa3=$r["optativa3"];
		$optativa4=$r["optativa4"];
		
		$t2=<<<EOD
Curso Anterior: <b>$curso_anterior</b>  Grupo: <b>$grupo_curso_anterior</b><br>
<b>MATERIAS</b><br>
- Religión/Valores Éticos: <b>$rel_valores_et</b><br>
<table>
<tr>
	<td width="60px">OPTATIVAS:</td>
	<td>
		<ol>
			<li value="1"><b>$optativa1</b></li>
			<li><b>$optativa2</b></li>
			<li><b>$optativa3</b></li>
			<li><b>$optativa4</b></li>
		</ol>
	</td>
</tr>
</table>

<br>########################################################################<br>
EOD;
	}
	//////////////////////////////////////////////////////////////////////////////////
	////////////3º ESO PMAR
	//////////////////////////////////////////////////////////////////////////////////
	elseif($c_mat=="3esopmar"){
		$curso_anterior=$r["curso_anterior"];
		$grupo_curso_anterior=$r["grupo_curso_anterior"];
		$rel_valores_et=$r["rel_valores_et"];
		$optativa1=$r["optativa1"];
		$optativa2=$r["optativa2"];
		$optativa3=$r["optativa3"];
		$optativa4=$r["optativa4"];

		$t2=<<<EOD
Curso Anterior: <b>$curso_anterior</b>  Grupo: <b>$grupo_curso_anterior</b><br>
<b>MATERIAS</b><br>
- Religión/Valores Éticos: <b>$rel_valores_et</b><br>
<table>
<tr>
	<td width="60px">OPTATIVAS:</td>
	<td>
		<ol>
			<li value="1"><b>$optativa1</b></li>
			<li><b>$optativa2</b></li>
			<li><b>$optativa3</b></li>
			<li><b>$optativa4</b></li>
		</ol>
	</td>
</tr>
</table>
EOD;
	}
	//////////////////////////////////////////////////////////////////////////////////
	///////////1º BACH CIENCIAS
	//////////////////////////////////////////////////////////////////////////////////
	elseif($c_mat=="1bach_c"){
		$itinerario=$r["itinerario"];
		$primer_idioma=$r["primer_idioma"];
		$tronc_gen1=$r["tronc_gen1"];
		$tronc_gen2=$r["tronc_gen2"];
		$espec_itin1=$r["espec_itin1"];
		$espec_itin2=$r["espec_itin2"];
		$espec_itin3=$r["espec_itin3"];
		$espec_itin4=$r["espec_itin4"];
		$espec_itin5=$r["espec_itin5"];
		$espec_itin6=$r["espec_itin6"];
		$espec_itin7=$r["espec_itin7"];
		$espec_itin8=$r["espec_itin8"];
		$espec_itin9=$r["espec_itin9"];
		$espec_itin10=$r["espec_itin10"];
		$espec_itin11=$r["espec_itin11"];
		$espec_com1=$r["espec_com1"];
		$espec_com2=$r["espec_com2"];
		$espec_com3=$r["espec_com3"];
		$espec_com4=$r["espec_com4"];
		$espec_com5=$r["espec_com5"];
		$espec_com6=$r["espec_com6"];
		$espec_com7=$r["espec_com7"];

		$t2=<<<EOD
<b>MATERIAS</b><br>
- Primer Idioma:<b>$primer_idioma</b><br>
- Itinerario: <b>$itinerario</b><br>
- Tronc.Gen.y de Opción: <b>$tronc_gen1</b> y <b>$tronc_gen2</b><br>
<table>
<tr>
	<td>
		Específicas de Itinerario
	</td>
	<td>
		Específicas Comunes
	</td>
</tr>
<tr>
	<td>
		<ol>
			<li value="1"><b>$espec_itin1</b></li>
			<li><b>$espec_itin2</b></li>
			<li><b>$espec_itin3</b></li>
			<li><b>$espec_itin4</b></li>
			<li><b>$espec_itin5</b></li>
			<li><b>$espec_itin6</b></li>
			<li><b>$espec_itin7</b></li>
			<li><b>$espec_itin8</b></li>
			<li><b>$espec_itin9</b></li>
			<li><b>$espec_itin10</b></li>
			<li><b>$espec_itin11</b></li>
		</ol>
	</td>
	<td>
		<ol>
			<li value="1"><b>$espec_com1</b></li>
			<li><b>$espec_com2</b></li>
			<li><b>$espec_com3</b></li>
			<li><b>$espec_com4</b></li>
			<li><b>$espec_com5</b></li>
			<li><b>$espec_com6</b></li>
			<li><b>$espec_com7</b></li>
		</ol>
	</td>
</tr>
</table>

EOD;
	}
	//////////////////////////////////////////////////////////////////////////////////
	////////////1º BACH HH.CC.SS.
	//////////////////////////////////////////////////////////////////////////////////
	elseif($c_mat=="1bach_hcs"){
		$itinerario=$r["itinerario"];
		$primer_idioma=$r["primer_idioma"];
		$tronc_gen1=$r["tronc_gen1"];
		$tronc_gen2=$r["tronc_gen2"];
		$tronc_opcion=$r["tronc_opcion"];
		$espec_itin1=$r["espec_itin1"];
		$espec_itin2=$r["espec_itin2"];
		$espec_itin3=$r["espec_itin3"];
		$espec_itin4=$r["espec_itin4"];
		$espec_itin5=$r["espec_itin5"];
		$espec_itin6=$r["espec_itin6"];
		$espec_itin7=$r["espec_itin7"];
		$espec_itin8=$r["espec_itin8"];
		$espec_com1=$r["espec_com1"];
		$espec_com2=$r["espec_com2"];
		$espec_com3=$r["espec_com3"];
		$espec_com4=$r["espec_com4"];
		$espec_com5=$r["espec_com5"];
		$espec_com6=$r["espec_com6"];
		$espec_com7=$r["espec_com7"];

		$t2=<<<EOD
<b>MATERIAS</b><br>
- Primer Idioma: <b>$primer_idioma</b><br> 
- Itinerario: <b>$itinerario</b><br>
- Troncales Generales: <b>$tronc_gen1</b> y <b>$tronc_gen2</b><br>
- Troncal de Opción: <b>$tronc_opcion</b><br>
<table>
<tr>
	<td>
		Específicas de Itinerario
	</td>
	<td>
		Específicas Comunes
	</td>
</tr>
<tr>
	<td>
		<ol>
			<li value="1"><b>$espec_itin1</b></li>
			<li><b>$espec_itin2</b></li>
			<li><b>$espec_itin3</b></li>
			<li><b>$espec_itin4</b></li>
			<li><b>$espec_itin5</b></li>
			<li><b>$espec_itin6</b></li>
			<li><b>$espec_itin7</b></li>
			<li><b>$espec_itin8</b></li>
		</ol>
	</td>
	<td>
		<ol>
			<li value="1"><b>$espec_com1</b></li>
			<li><b>$espec_com2</b></li>
			<li><b>$espec_com3</b></li>
			<li><b>$espec_com4</b></li>
			<li><b>$espec_com5</b></li>
			<li><b>$espec_com6</b></li>
			<li><b>$espec_com7</b></li>
		</ol>
	</td>
</tr>
</table>
					 
EOD;

	}
	//////////////////////////////////////////////////////////////////////////////////
	///////////2º BACH CIENCIAS
	//////////////////////////////////////////////////////////////////////////////////
	elseif($c_mat=="2bach_c"){
		$itinerario=$r["itinerario"];
		$primer_idioma=$r["primer_idioma"];
		$tronc_opc1=$r["tronc_opc1"];
		$tronc_opc2=$r["tronc_opc2"];
		$espec_itin_com1=$r["espec_itin_com1"];
		$espec_itin_com2=$r["espec_itin_com2"];
		$espec_itin_com3=$r["espec_itin_com3"];
		$espec_itin_com4=$r["espec_itin_com4"];
		$espec_itin_com5=$r["espec_itin_com5"];
		$espec_itin_com6=$r["espec_itin_com6"];
		$espec_itin_com7=$r["espec_itin_com7"];
		$espec_itin_com8=$r["espec_itin_com8"];
		$espec_itin_com9=$r["espec_itin_com9"];
		$espec_itin_com10=$r["espec_itin_com10"];
		$espec_itin_com11=$r["espec_itin_com11"];
		$espec_itin_com12=$r["espec_itin_com12"];
		$espec_itin_com13=$r["espec_itin_com13"];
		$espec_itin_com14=$r["espec_itin_com14"];
		$espec_itin_com15=$r["espec_itin_com15"];
		$espec_itin_com16=$r["espec_itin_com16"];
		$espec_itin_com17=$r["espec_itin_com17"];
		$espec_itin_com18=$r["espec_itin_com18"];

		$t2=<<<EOD
<b>MATERIAS</b><br>
- Primer Idioma II: <b>$primer_idioma</b><br> 
- Itinerario: <b>$itinerario</b><br>
- Troncales de Opción: <b>$tronc_opc1</b> y <b>$tronc_opc2</b><br>
<table>
<tr>
	<td colspan="2">
		Específicas de Itinerario y Comunes
	</td>
</tr>
<tr>
	<td>
		<ol>
			<li value="1"><b>$espec_itin_com1</b></li>
			<li><b>$espec_itin_com2</b></li>
			<li><b>$espec_itin_com3</b></li>
			<li><b>$espec_itin_com4</b></li>
			<li><b>$espec_itin_com5</b></li>
			<li><b>$espec_itin_com6</b></li>
			<li><b>$espec_itin_com7</b></li>
			<li><b>$espec_itin_com8</b></li>
			<li><b>$espec_itin_com9</b></li>
		</ol>
	</td>
	<td>
		<ol>
			<li value="10"><b>$espec_itin_com10</b></li>
			<li><b>$espec_itin_com11</b></li>
			<li><b>$espec_itin_com12</b></li>
			<li><b>$espec_itin_com13</b></li>
			<li><b>$espec_itin_com14</b></li>
			<li><b>$espec_itin_com15</b></li>
			<li><b>$espec_itin_com16</b></li>
			<li><b>$espec_itin_com17</b></li>
			<li><b>$espec_itin_com18</b></li>
		</ol>
	</td>
</tr>
</table>
					 
EOD;
	}
	//////////////////////////////////////////////////////////////////////////////////
	////////////2º BACH HH.CC.SS.
	//////////////////////////////////////////////////////////////////////////////////
	elseif($c_mat=="2bach_hcs"){
		$itinerario=$r["itinerario"];
		$primer_idioma=$r["primer_idioma"];
		$tronc_gen=$r["tronc_gen"];
		$tronc_opc1=$r["tronc_opc1"];
		$tronc_opc2=$r["tronc_opc2"];
		$espec_itin_com1=$r["espec_itin_com1"];
		$espec_itin_com2=$r["espec_itin_com2"];
		$espec_itin_com3=$r["espec_itin_com3"];
		$espec_itin_com4=$r["espec_itin_com4"];
		$espec_itin_com5=$r["espec_itin_com5"];
		$espec_itin_com6=$r["espec_itin_com6"];
		$espec_itin_com7=$r["espec_itin_com7"];
		$espec_itin_com8=$r["espec_itin_com8"];
		$espec_itin_com9=$r["espec_itin_com9"];
		$espec_itin_com10=$r["espec_itin_com10"];
		$espec_itin_com11=$r["espec_itin_com11"];
		$espec_itin_com12=$r["espec_itin_com12"];
		$espec_itin_com13=$r["espec_itin_com13"];
		$espec_itin_com14=$r["espec_itin_com14"];
		$espec_itin_com15=$r["espec_itin_com15"];
		$espec_itin_com16=$r["espec_itin_com16"];
		$espec_itin_com17=$r["espec_itin_com17"];

		$t2=<<<EOD
<b>MATERIAS</b><br>
<table>
<tr>
	<td width="20%">Troncales Generales:</td>
	<td>- Primer Idioma II: <b>$primer_idioma</b></td>
</tr>
<tr>
	<td></td>
	<td>- Itinerario: <b>$itinerario - $tronc_gen</b></td>
</tr>
</table><br>
Troncales de Opción: <b>$tronc_opc1</b> y <b>$tronc_opc2</b><br>
<table>
<tr>
<td colspan="2">
	Específicas de Itinerario y Comunes
</td>
</tr>
<tr>
<td>
	<ol>
		<li value="1"><b>$espec_itin_com1</b></li>
		<li><b>$espec_itin_com2</b></li>
		<li><b>$espec_itin_com3</b></li>
		<li><b>$espec_itin_com4</b></li>
		<li><b>$espec_itin_com5</b></li>
		<li><b>$espec_itin_com6</b></li>
		<li><b>$espec_itin_com7</b></li>
		<li><b>$espec_itin_com8</b></li>
		<li><b>$espec_itin_com9</b></li>
	</ol>
</td>
<td>
	<ol>
		<li value="10"><b>$espec_itin_com10</b></li>
		<li><b>$espec_itin_com11</b></li>
		<li><b>$espec_itin_com12</b></li>
		<li><b>$espec_itin_com13</b></li>
		<li><b>$espec_itin_com14</b></li>
		<li><b>$espec_itin_com15</b></li>
		<li><b>$espec_itin_com16</b></li>
		<li><b>$espec_itin_com17</b></li>
	</ol>
</td>
</tr>
</table>
EOD;
	}
	return $t1 . $t2;
}


function matriculaCiclos($r){
	$registro=$r["registro"];
	$id_nie=$r["id_nie"];
	$apellidos=$r["apellidos"];
	$nombre=$r["nombre"];
	$anno_nac=(int)substr($r["fecha_nac"],0,4);
    $anno=(int)date("Y");
	$GLOBALS['id_nie']=$id_nie;
	$t="NIE: <b>$id_nie</b>&nbsp;&nbsp;&nbsp;&nbsp;Nº registro: <b>$registro</b><br>";
	$t.="Apellidos y Nombre:<b>$apellidos, $nombre</b><br>";
	if($anno-$actualiza<28){
		$t.="Alumno menor de 28 (requiere seguro escolar):<b>SI</b><br>";
		$GLOBALS['menor28']=true;
	}
	else {
		$t.="Alumno menor de 28 (requiere seguro escolar):<b>NO</b><br>";
		$GLOBALS['menor28']=false;
	}
	return $t;
}

function matriculaFpb($r){
	$registro=$r["registro"];
	$id_nie=$r["id_nie"];
	$apellidos=$r["apellidos"];
	$nombre=$r["nombre"];
	$GLOBALS['id_nie']=$id_nie;
	$t="NIE: <b>$id_nie</b>&nbsp;&nbsp;&nbsp;&nbsp;Nº registro: <b>$registro</b><br>";
	$t.="Apellidos y Nombre:<b>$apellidos, $nombre</b><br>";
	return $t;
}