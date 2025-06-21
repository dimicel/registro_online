<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
$mysqli->set_charset("utf8");
require_once('tcpdf/config/tcpdf_config_alt.php');
require_once('tcpdf/tcpdf.php');
header("Content-Type: text/html;charset=utf-8");


if ($mysqli->errno>0) {
    exit("server");
}

$consultaCentro = $mysqli->query("SELECT * FROM config_centro ");
if ($consultaCentro->num_rows!=1){
    exit("config_centro");
}
else {
    $datosCentro=$consultaCentro->fetch_assoc();
    $nombreDirector=$datosCentro["director"];
    $nombreDirectorMayus=strtoupper($nombreDirector);
    $nombre_centro=$datosCentro["centro"];
    $localidad_centro=$datosCentro["localidad_centro"];
}
$genera_resolucion=$_POST["genera_resolucion"];
$registro=$_POST["registro"];
$modulos=$_POST["modulo_convalid"];
$estados=$_POST["estado_convalid"];
$motivos=$_POST["motivo_no_fav_convalid"];
$elementos_sin_resolver=false;
$resuelto_por=array(
    "FAVORABLE"=>"CENTRO",
    "NO FAVORABLE"=>"CENTRO",
    "NO PROCEDE"=>"CENTRO",
    "CONSEJERIA"=>"CONSEJERIA",
    "MINISTERIO"=>"MINISTERIO",
    ""=>""
);
$tipoPdfParaUsu="";

$res_cen=0;
$res_con=0;
$res_min=0;
$res_fav=0;
$res_nofav=0;
$res_noproc=0;
for ($i=0; $i<count($estados);$i++){
    if($estados[$i]=="FAVORABLE"){
        $res_cen++;
        $res_fav++;
    }
    elseif($estados[$i]=="NO FAVORABLE" ){
        $res_cen++;
        $res_nofav++;
    }
    elseif($estados[$i]=="NO PROCEDE"){
        $res_cen++;
        $res_noproc++;
    } 
    elseif($estados[$i]=="CONSEJERIA") $res_con++;
    elseif($estados[$i]=="MINISTERIO") $res_min++;
}

if ($res_cen>0) $act_rescen=1;
else $act_rescen=0;
if ($res_con>0) $act_rescon=1;
else $act_rescon=0;
if ($res_min>0) $act_resmin=1;
else $act_resmin=0;

$consulta_act_estado_procSI="update convalidaciones set resuelve_cen='$act_rescen', resuelto_cen='$act_rescen', resuelve_con='$act_rescon', resuelve_min='$act_resmin', procesado=1, resolucion='PROCESADA' where registro='$registro'";
$consulta_act_estado_procNO="update convalidaciones set resuelve_cen='$act_rescen', resuelto_cen='$act_rescen', resuelve_con='$act_rescon', resuelve_min='$act_resmin', procesado=0, resolucion='EN ESPERA' where registro='$registro'";

$mysqli->begin_transaction();

try {
    // Iterar sobre los arrays y actualizar los registros en la base de datos
    for ($i = 0; $i < count($modulos); $i++) {
        if ($estados[$i]==""){
            if ($estados[$i]=="") $elementos_sin_resolver=true;
            //continue;
        }
        $sql = "UPDATE convalidaciones_modulos SET resolucion = '" . $estados[$i] . "', motivo_no_favorable = '" . $motivos[$i] . "', resuelto_por = '" . $resuelto_por[$estados[$i]] . "' WHERE registro = '$registro' AND modulo='$modulos[$i]'";

        if ($mysqli->query($sql) !== TRUE) {
            throw new Exception("error_db");
        }
    }
    if ($elementos_sin_resolver){
        if ($mysqli->query($consulta_act_estado_procNO) !== TRUE) {
            throw new Exception("error_db_conval");
        }
    }
    else {
        if ($mysqli->query($consulta_act_estado_procSI) !== TRUE) {
            throw new Exception("error_db_conval");
        }
    }
    

    // Confirmar la transacción
    $mysqli->commit();
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $mysqli->rollback();
    $mysqli->close();
    exit ($e->getMessage());
}


//Salida del script
if ($elementos_sin_resolver) exit("elementos_sin_resolver");
if($res_cen==0){
    if($res_con==0 && $res_min>0) $tipoPdfParaUsu="ministerio";
    elseif($res_con>0 && $res_min==0) $tipoPdfParaUsu="consejeria";
    elseif($res_con>0 && $res_min>0) $tipoPdfParaUsu="consejeria_ministerio";
}

//Recuperación de datos de la tabla convalidaciones

$concov=$mysqli->query("select * from convalidaciones where registro='$registro'");
if($concov->num_rows!=1){
    exit("no_datospdf");
}
$dr=$concov->fetch_assoc();

// Cerrar conexión
$mysqli->close();
if($genera_resolucion==0) exit("ok");
//Se genera el pdf para el alumno si están todos los módulos resueltos y, al menos, hay uno que resuelve el centro
$Yinicio=70;
$margen_derecho=20;
if($res_fav>0 || $res_nofav>0 || $res_noproc>0){
    class MYPDF extends TCPDF {

        //Page header
        public function Header() {
            $this->SetFont('helvetica', '', 8);
            $this->SetXY(10,10);
            $this->Cell(0,0,"AÑO XXIX Núm. 166",0,0,'L',0,'',1,false,'T','T');
            $this->SetXY(10,10);
            $this->Cell(0,0,"27 de agosto de 2010",0,0,'C',0,'',1,false,'T','T');
            $this->SetXY(10,10);
            $this->Cell(0,0,"39635",0,0,'R',0,'',1,false,'T','T');
            $this->Line(10, 13, $this->getPageWidth() - 10, 13);
            // Logo
            $image_file = '../recursos/logo_ccm.jpg';
            $this->Image($image_file, 10, 20, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
            
            $this->SetFont('helvetica', 'B', 11);
            $this->SetXY(0,40);
            $this->Cell(0,0,"ANEXO IX",0,0,'C',0,'',1,false,'T','T');
            $this->SetXY(0,46);
            $this->Cell(0,0,"ENSEÑANZAS DE FORMACIÓN PROFESIONAL",0,0,'C',0,'',1,false,'T','T');
            $this->SetXY(0,52);
            $this->Cell(0,0,"RECONOCIMIENTO DE CONVALIDACIÓN DE ESTUDIOS",0,0,'C',0,'',1,false,'T','T');
                
        }
    }

    // create new PDF document
    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor($datos_cen["centro"]);
    $pdf->SetTitle('Resolución Convalidaciones');
    $pdf->SetSubject('Secretaría');
    $pdf->SetKeywords('PDF, secretaría, '. $datos_cen["localidad_centro"].', Resolución Convalidaciones');

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


    $pdf->SetXY(0,$Yinicio);
    $pdf->SetFont('helvetica', '', 8);
    
    $html="<p>D. <b>$nombreDirectorMayus</b>, director del centro educativo <b>$nombre_centro ($localidad_centro)</b>, una vez examinada la documentación presentada por ";
    $html.="<b>".strtoupper($dr["nombre"])." ".strtoupper($dr["apellidos"])."</b> en la solicitud con nº de registro " . $registro . " por la que solicita la convalidación de módulos de Formación Profesional correspondientes al ";
    if ($dr["grado"]=="Curso de Especialización"){
        $html.=$dr["grado"] . " " . "<b>".strtoupper($dr["ciclo"])."</b>";
    }
    else{
        $html.="ciclo formativo de ". "<b>".strtoupper($dr["ciclo"])." de GRADO ".strtoupper($dr["grado"])."</b> ";
    }
    $html.=" con sus estudios de ".$dr["estudios_superados"]."</p>";
    $html.="<br><br><h3><b>RESUELVE</b></h3><br>";
    if($res_fav>0){
        $html.="<b>Reconocerle</b> las convalidaciones de los siguientes módulos profesionales del ciclo formativo correspondiente:<br> <b>";
        for ($i=0;$i<count($estados);$i++){
            if ($estados[$i]=="FAVORABLE"){
                $html.=$modulos[$i];
                if ($i<count($estados)-2) $html.="; ";
            }
        }
        $html.="</b><br><br>";
    }
    if($res_nofav>0){
        $html.="<b>No Reconocerle</b> las convalidaciones de los siguientes módulos profesionales del ciclo formativo correspondiente:<br> <b>";
        for ($i=0;$i<count($estados);$i++){
            if ($estados[$i]=="NO FAVORABLE"){
                $html.=$modulos[$i] ." (".$motivos[$i].")";
                if ($i<count($estados)-2) $html.="; ";
            }
        }
        $html.="</b><br><br>";
    }
    if($res_noproc>0){
        $html.="<b>No PROCEDE</b> el reconocimiento de las convalidaciones de los siguientes módulos profesionales del ciclo formativo correspondiente:<br> <b>";
        for ($i=0;$i<count($estados);$i++){
            if ($estados[$i]=="NO PROCEDE"){
                $html.=$modulos[$i] ." (".$motivos[$i].")";
                if ($i<count($estados)-2) $html.="; ";
            }
        }
        $html.="</b><br><br>";
    }
    if($res_con>0){
        $html.="<b>No Reconocerle</b> la convalidación de los siguientes módulos porque debe ser resuelta por la Consejería de Educación:<br><b>";
        for ($i=0;$i<count($estados);$i++){
            if ($estados[$i]=="CONSEJERIA"){
                $html.=$modulos[$i];
                if ($i<count($estados)-2) $html.="; ";
            }
        }
        $html.="</b><br>El centro educativo se pondrá en contacto con usted para darle instrucciones de cómo proceder.<br><br>";
    }
    if ($res_min>0){
        $html.="<b>No Reconocerle</b> la convalidación de los siguientes módulos porque debe ser resuelta por el Ministerio de Educación:<br><b>";
        for ($i=0;$i<count($estados);$i++){
            if ($estados[$i]=="MINISTERIO"){
                $html.=$modulos[$i];
                if ($i<count($estados)-2) $html.="; ";
            }
        }
        $html.="</b><br>La solicitud deberá cumplimentarla online en el siguiente enlace:<br>https://www.educacionfpydeportes.gob.es/servicios-al-ciudadano/catalogo/general/05/050210/ficha/050210-alumnos.html.<br><br>";
    }
    $html.="<br><br> En caso de que existan dos resoluciones asociadas a solicitudes con el mismo número de registro, sólo será válida la resolución de fecha más reciente, considerándose no válidas aquellas cuya fecha sea anterior."; 
    $pdf->SetRightMargin($margen_derecho);
    $pdf->writeHTML($html, true, false, true, false, '');


    $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
    $fecha_actual=getdate();
    $dd=$fecha_actual["mday"];
    $mm=$meses[$fecha_actual["mon"]-1];
    $yyyy=$fecha_actual["year"];
    $fecha_firma=$localidad_centro.", a ".$dd." de ".$mm." de ".$yyyy;
    $pdf->SetFont('helvetica', '', 8);
    //$Yinicio=$pdf->GetY();
    //$pdf->SetXY(0,$Yinicio);
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Cell(0,0,$fecha_firma,0,0,'C',0,'',1,false,'T','T');
    
    $Yinicio=$pdf->GetY()+4;
    //$Yinicio+=4;
    $anchoSello=50;
    $altoSello=40;

    // Calcular las coordenadas para centrar la imagen
    $image_x = ($pdf->GetPageWidth() - $anchoSello) / 2;

    // Insertar la imagen centrada
    $pdf->Image('../recursos/sello_firma.jpg', $image_x, $Yinicio, $anchoSello, $altoSello, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    
    $Yinicio+=$altoSello+4;
    $pdf->SetY($Yinicio);
    $pdf->Cell(0,0,"Fdo.: ".$nombreDirector,0,0,'C',0,'',1,false,'T','T');

    
    

    //--------FINAL

    //GENERA EL ARCHIVO NUEVO
    $dirRegistro=substr($dr["registro"], 17);
    $nombre_fichero='resolucion.pdf';
    $ruta=__DIR__."/../docs/".$dr["id_nie"]."/"."convalidaciones/".$dr["curso"]."/".$dirRegistro."/docs/resolucion";
    if(!is_dir($ruta))mkdir($ruta,0777,true);
    $pdf->Output($ruta."/". $nombre_fichero, 'F');
}
elseif($res_min>0 || $res_con>0) {
    class MYPDF extends TCPDF {
        private $datosCentro;

        public function __construct($datosCentro, $orientation = PDF_PAGE_ORIENTATION, $unit = PDF_UNIT, $format = PDF_PAGE_FORMAT, $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false) {
            parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
            $this->datosCentro = $datosCentro;
        }
        //Page header
        public function Header() {
            // Logo
            $image_file = '../recursos/logo_ccm.jpg';
            $this->Image($image_file, 10, 20, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
            $image_file = '../recursos/mini_escudo.jpg';
		    $this->Image($image_file, 140, 20, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

            $this->SetFont('helvetica', 'B', 12);
            $this->SetXY(0,50);
            $this->Cell(0,0,"INFORMACIÓN SOBRE LOS MÓDULOS QUE SOLICITA CONVALIDAR",0,0,'C',0,'',1,false,'T','T');
                
            $this->SetFont('helvetica', '', 8);
            // Title
            //$this->setCellHeightRatio(1.75);
		$encab = "<label><strong>".$this->datosCentro["centro"]."</strong><br>".$this->datosCentro["direccion_centro"]."<br>".$this->datosCentro["cp_centro"]."-".$this->datosCentro["localidad_centro"]."<br>Tlf.:".$this->datosCentro["tlf_centro"]."<br>Fax:".$this->datosCentro["fax_centro"]."</label>";
            $this->writeHTMLCell(0, 0, 160, 20, $encab, 0, 1, 0, true, 'C', true);
            //$this->Ln();
            //$this->writeHTMLCell(0, 0, '', '', '', 'B', 1, 0, true, 'L', true);
        }
    }

    // create new PDF document
    $pdf = new MYPDF($datosCentro);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor($nombre_centro);
    $pdf->SetTitle('Resolución Convalidaciones');
    $pdf->SetSubject('Secretaría');
    $pdf->SetKeywords('PDF, secretaría, '.$localidad_centro.', Resolución Convalidaciones');

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

    
    $pdf->SetXY(0,$Yinicio);
    $pdf->SetFont('helvetica', '', 8);
    
    $html="";
    if($res_con>0){
        $html.="<p>La convalidación de los siguientes módulos debe ser resuelta por la Consejería de Educación:<br><b>";
        for ($i=0;$i<count($estados);$i++){
            if ($estados[$i]=="CONSEJERIA"){
                $html.=$modulos[$i];
                if ($i<count($estados)-2) $html.="; ";
            }
        }
        $html.="</b></p><br><br><br>";
    }
    if ($res_min>0){
        $html.="<p>La convalidación de los siguientes módulos debe ser resuelta por el Ministerio de Educación:<br><b>";
        for ($i=0;$i<count($estados);$i++){
            if ($estados[$i]=="MINISTERIO"){
                $html.=$modulos[$i];
                if ($i<count($estados)-2) $html.="; ";
            }
        }
        $html.="</b></p><br><br><br>";
    }
    $html.="<p>El centro educativo se pondrá en contacto con usted para darle instrucciones de cómo proceder.</p><br><br><br>";
    $pdf->SetRightMargin($margen_derecho);
    $pdf->writeHTML($html, true, false, true, false, '');

    $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
    $fecha_actual=getdate();
    $dd=$fecha_actual["mday"];
    $mm=$meses[$fecha_actual["mon"]-1];
    $yyyy=$fecha_actual["year"];
    $fecha_firma="Toledo, a ".$dd." de ".$mm." de ".$yyyy;
    $pdf->SetFont('helvetica', '', 8);
    $Yinicio=$pdf->GetY();
    $pdf->SetXY(0,$Yinicio);
    $pdf->Cell(0,0,$fecha_firma,0,0,'C',0,'',1,false,'T','T');
    


    $dirRegistro=substr($dr["registro"], 17);
    $nombre_fichero='resolucion.pdf';
    $ruta=__DIR__."/../docs/".$dr["id_nie"]."/"."convalidaciones/".$dr["curso"]."/".$dirRegistro."/docs/resolucion";
    if(!is_dir($ruta))mkdir($ruta,0777,true);
    $pdf->Output($ruta."/". $nombre_fichero, 'F');
}
//Salida OK
exit("ok");

/*
$sql = "UPDATE convalidaciones SET resolucion='$estado' WHERE registro='$registro'";
$result = $mysqli->query($sql);
if ($mysqli->affected_rows > 0) {
    $mysqli->close();
    exit("ok");
}
else {
    $mysqli->close();
    exit("no_registro");
}
*/
