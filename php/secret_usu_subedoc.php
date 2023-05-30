<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");

$id_nie=$_POST["id_nie"];
$anno_curso=$_POST["anno_curso"];
$tipo_doc=$_POST["tipo_doc"];
$nom_doc=$_POST["nom_doc"];
$tipo_envio=$_POST["tipo_envio"];
if(isset($_POST["parte"])) $parte=$_POST["parte"];//Para el DNI Anverso A   Reverso R
//$data=array();
//$data="";
//$data="";

$rutdocs=[
    "Anulación matrícula"=>["ruta"=>"anulacion_matricula","n_archivo"=>"anulmat_"],
    "Anulación módulos (modular)"=>["ruta"=>"anul_mod_modular","n_archivo"=>"anulmodmodular_"],
    "Renuncia a convocatoria"=>["ruta"=>"renuncia_conv","n_archivo"=>"rc_"],
    "Exención FCT"=>["ruta"=>"fct","n_archivo"=>"exenfct_"],
    "Evaluación FCT"=>["ruta"=>"fct","n_archivo"=>"evalfct_"],
    "Pérdida Evaluación Contínua"=>["ruta"=>"perd_eval_cont","n_archivo"=>"pevalcont_"],
    "Homologación Estudios"=>["ruta"=>"homol_est","n_archivo"=>"homolest_"],
    "Título ESO para FPB"=>["ruta"=>"titeso_fpb","n_archivo"=>"esofpb_"],
    "Informes Orientación"=>["ruta"=>"orientacion","n_archivo"=>"infori_"],
    "Transporte Escolar"=>["ruta"=>"transporte_escolar","n_archivo"=>"transpesc_"],
    "Otro"=>["ruta"=>"otros","n_archivo"=>$nom_doc."_"],
    "Certificado Notas"=>["ruta"=>"certificado_notas","n_archivo"=>""]
];

/*
if($tipo_doc=="Certificado Notas"){
    if(is_uploaded_file($_FILES['documento']['tmp_name'])){
        if (!is_dir("../docs/".$id_nie))mkdir("../docs/".$id_nie,0777);
        if(!is_dir("../docs/".$id_nie."/certificado_notas"))mkdir("../docs/".$id_nie."/certificado_notas",0777);
        if(!is_dir("../docs/".$id_nie."/"."certificado_notas/" . $anno_curso))mkdir("../docs/".$id_nie."/"."certificado_notas/" . $anno_curso,0777);
        //$ruta="../docs/".$id_nie."/"."certificado_notas/" . $anno_curso."/".$id_nie.".pdf";
        if (strlen(trim($nom_doc))==0){
            $ruta="../docs/".$id_nie."/"."certificado_notas/" . $anno_curso."/".$nom_doc.".pdf";
        }
        else {
            $ruta="../docs/".$id_nie."/"."certificado_notas/" . $anno_curso."/".$id_nie.".pdf";
        }
        if(!move_uploaded_file($_FILES['documento']['tmp_name'], $ruta)){
            $data="almacenar";
            exit ($data);
        }
        $data="ok";
        exit ($data);
    }
    else{
        $data="archivo";
        exit ($data);
    } 
}
*/

if($tipo_doc=="Fotografía del alumno"){
    if(is_uploaded_file($_FILES['documento']['tmp_name'])){
        $ruta="../docs/fotos/" . $id_nie.".jpeg";
        if(!move_uploaded_file($_FILES['documento']['tmp_name'], $ruta)){
            $data="almacenar";
            exit ($data);
        }
        $data="ok";
        exit ($data);
    }
    else{
        $data="archivo";
        exit ($data);
    } 
}


if($tipo_doc=="Documento de Identificación-Anverso" || $tipo_doc=="Documento de Identificación-Reverso"){
    if(is_uploaded_file($_FILES['documento']['tmp_name'])){
        if (!is_dir("../docs/".$id_nie))mkdir("../docs/".$id_nie,0777);
        if(!is_dir("../docs/".$id_nie."/dni"))mkdir("../docs/".$id_nie."/dni",0777);
        $ruta="../docs/".$id_nie."/"."dni/". $id_nie."-".$parte.".jpeg";
        if(!move_uploaded_file($_FILES['documento']['tmp_name'], $ruta)){
            $data="almacenar";
            exit ($data);
        }
        $data="ok";
        exit ($data);
    }
    else{
        $data="archivo";
        exit ($data);
    } 
}

if($tipo_doc=="Seguro Escolar"){
    if(is_uploaded_file($_FILES['documento']['tmp_name'])){
        if (!is_dir("../docs/".$id_nie))mkdir("../docs/".$id_nie,0777);
        if(!is_dir("../docs/".$id_nie."/seguro"))mkdir("../docs/".$id_nie."/seguro",0777);
        if(!is_dir("../docs/".$id_nie."/seguro"."/".$anno_curso))mkdir("../docs/".$id_nie."/seguro"."/".$anno_curso,0777);
        $ruta="../docs/".$id_nie."/"."seguro/".$anno_curso."/". $id_nie.".jpeg";
        if(!move_uploaded_file($_FILES['documento']['tmp_name'], $ruta)){
            $data="almacenar";
            exit ($data);
        }
        $data="ok";
        exit ($data);
    }
    else{
        $data="archivo";
        exit ($data);
    } 
}


if($tipo_envio=="simple"){
    if(is_uploaded_file($_FILES['documento']['tmp_name'])){
        if (!is_dir("../docs/".$id_nie))mkdir("../docs/".$id_nie,0777);
        if (!is_dir("../docs/".$id_nie."/".$rutdocs[$tipo_doc]["ruta"]))mkdir("../docs/".$id_nie."/".$rutdocs[$tipo_doc]["ruta"],0777);
        if (!is_dir("../docs/".$id_nie."/".$rutdocs[$tipo_doc]["ruta"]."/".$anno_curso))mkdir("../docs/".$id_nie."/".$rutdocs[$tipo_doc]["ruta"]."/".$anno_curso,0777);
        $ruta= "../docs/".$id_nie."/".$rutdocs[$tipo_doc]["ruta"]."/".$anno_curso;
        if (strlen(trim($nom_doc))==0){
            $destino=$ruta."/".$rutdocs[$tipo_doc]["n_archivo"].$anno_curso."_".$id_nie;
        }
        else {
            $destino=$ruta."/".$nom_doc;
        }
        
        $contador=0;
        $d=$destino;
        while(is_file($d.".pdf")){
            $contador++;
            $d=$destino."_".$contador;
        }
        $destino=$d.".pdf";
        /*$destino=$destino.".pdf";
        if (is_file("$destino")){
            $data="existe";
            exit ($data);
        }*/

        if(!move_uploaded_file($_FILES['documento']['tmp_name'], $destino)){
            $data="almacenar";
            exit ($data);
        }
        else if($contador>0){
            exit("existe.".$contador);
        }
        else {
            $data="ok";
            exit ($data);
        } 
    }
    else{
        $data="archivo";
        exit ($data);
    } 
}
elseif($tipo_envio=="multiple"){
    $archivos_mal=array();
    if (count($_FILES["documento"]['tmp_name'])>0){
        ob_start();
        $contador_archivos=0;
        foreach($_FILES["documento"]['tmp_name'] as $key => $tmp_name){
            $contador_archivos++;
            if($_FILES["documento"]["name"][$key]){
                $ubicacionTemporal = $_FILES["documento"]["tmp_name"][$key];
                $nombreArchivo = $_FILES["documento"]["name"][$key];
                $extension = trim(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
                $id_nie=trim(pathinfo($nombreArchivo, PATHINFO_FILENAME));
                
                if(!is_dir("../docs/".$id_nie)){
                    $archivos_mal[$nombreArchivo]="El NIE no existe.";
                }
                else{
                    if (!is_dir("../docs/".$id_nie."/".$rutdocs[$tipo_doc]["ruta"]))mkdir("../docs/".$id_nie."/".$rutdocs[$tipo_doc]["ruta"],0777);
                    if (!is_dir("../docs/".$id_nie."/".$rutdocs[$tipo_doc]["ruta"]."/".$anno_curso))mkdir("../docs/".$id_nie."/".$rutdocs[$tipo_doc]["ruta"]."/".$anno_curso,0777);        
                    $ruta= "../docs/".$id_nie."/".$rutdocs[$tipo_doc]["ruta"]."/".$anno_curso;
                    if (strlen(trim($nom_doc))==0){
                        $destino=$ruta."/".$rutdocs[$tipo_doc]["n_archivo"].$anno_curso."_".$id_nie;
                    }
                    else {
                        $destino=$ruta."/".$nom_doc;
                    }
                    
                    $contador=0;
                    $d=$destino;
                    while(is_file($d.".pdf")){
                        $contador++;
                        $d=$destino."_".$contador;
                    }
                    $destino=$d.".pdf";

                    if(!move_uploaded_file($ubicacionTemporal, $destino)){
                        $archivos_mal[$nombreArchivo]="No se ha podido añadir al expediente. Ha habido un problema al copiarlo a la ruta destino";
                    }
                    else if ($contador>0){
                        $archivos_mal[$nombreArchivo]="AVISO: Ya existe un documento con el mismo nombre. El archivo se ha grabado con el nombre ".$destino;
                    }
                }
            }
            echo $contador_archivos."\n";
            ob_flush();
            flush();
        }
        ob_end_clean();
    }
    else{
        $data="archivo";
        exit ($data);
    }
    sleep(1);
    if (count($archivos_mal)==0){
        $data="ok";
        exit ($data);
    }
    else{
        require_once(__DIR__.'/tcpdf/config/tcpdf_config_alt.php');
        require_once(__DIR__.'/tcpdf/tcpdf.php');
        
        class MYPDF extends TCPDF {

            //Page header
            public function Header() {
                // Logo
                $image_file = '../recursos/logo_ccm.jpg';
                $this->Image($image_file, 10, 10, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
                $image_file = '../recursos/mini_escudo.jpg';
                $this->Image($image_file, 140, 10, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        
                $this->SetFont('helvetica', 'B', 14);
                $this->SetXY(0,10);
                $this->Cell(0,0,"INFORME ERRORES",0,0,'C',0,'',1,false,'T','T');
                    
                $this->SetFont('helvetica', '', 8);
                
                $encab = "<label><strong>IES Universidad Laboral</strong><br>Avda. Europa, 28<br>45003-TOLEDO<br>Tlf.:925 22 34 00<br>Fax:925 22 24 54</label>";
                $this->writeHTMLCell(0, 0, 160, 11, $encab, 0, 1, 0, true, 'C', true);
                
                
            }
        }

        // create new PDF document
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('IES Universidad Laboral');
        $pdf->SetTitle('Informe Errores');
        $pdf->SetSubject('Secretaría');
        $pdf->SetKeywords('ulaboral, PDF, secretaría, Toledo, Informe Errores');

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
        $pdf->setFontSubsetting(true);

        $pdf->SetFont('dejavusans', '', 8, '', true);
        $pdf->setFillColor(200);  //Relleno en gris
        
        $inicio_pagina=true;
        foreach($archivos_mal as $clave=>$valor){
            if ($inicio_pagina){
                $pdf->AddPage();
                $YInicio=45;
                $pdf->SetXY(15,$YInicio);
                $pdf->SetFont('dejavusans', 'B', 8, '', true);
                $pdf->Cell(0,0,"ARCHIVO                      ERROR GENERADO",0,0,'L',0,'',1,false,'','');
                $inicio_pagina=false;
            }
            $YInicio+=5;
            $pdf->SetFont('dejavusans', '', 8, '', true);
            $pdf->SetXY(15,$YInicio);
            $pdf->Cell(0,0,$clave,0,0,'L',0,'',1,false,'','');
            $pdf->SetX(51);
            $pdf->Cell(0,0,$valor,0,0,'L',0,'',1,false,'','');  
            
            if ($YInicio>=280){
                $inicio_pagina=true;
            } 
        }

        $pdfLog="informe_".time().".pdf";
        $pdf->Output(__DIR__."/"."../"."logs/".$pdfLog, 'F');
        //$data="informe";
        $data="logs/".$pdfLog;
        exit($data);
    }
}
