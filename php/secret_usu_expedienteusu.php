<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
//include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

$data=array();
/*
if ($mysqli->errno>0) {
    $data["error"]="server";
    exit(json_encode($data));
}
*/

$id_nie=$_POST["id_nie"];
$data["error"]="sin_registros";
$filtro=$_POST["filtro"];

/*
$tablas=array("mat_1eso",
			  "mat_2eso",
			  "mat_2esopmar",
			  "mat_3eso",
			  "mat_3esopmar",
			  "mat_4eso",
			  "mat_1bach_c",
			  "mat_1bach_hcs",
			  "mat_2bach_c",
			  "mat_2bach_hcs",
			  "mat_fpb",
			  "mat_ciclos",
			  "transporte");
$data["docs"]=array();
for($i=0;$i<count($tablas);$i++){
	$data["docs"][$tablas[$i]]=array();
	if ($filtro=="todos") $res=$mysqli->query("SELECT registro,curso FROM $tablas[$i] WHERE id_nie='$id_nie' ORDER BY curso ASC");
	else $res=$mysqli->query("SELECT registro,curso FROM $tablas[$i] WHERE id_nie='$id_nie' AND curso='$filtro' ORDER BY curso ASC");
	if ($res->num_rows>0){
		$data["error"]="ok";
		$contador=0;
		//$data["docs"][$tablas[$i]]["num_registros"]=$res->num_rows;
		while ($reg=$res->fetch_assoc()){
			$data["docs"][$tablas[$i]][$contador]["curso"]= $reg["curso"];
			$data["docs"][$tablas[$i]][$contador]["registro"]= $reg["registro"];
			$contador++;
		}
		$res->free();
	}
	else{
		$data["docs"][$tablas[$i]]["num_registros"]=0;
	}
}
*/

$tipos_doc=[
	"certificado_notas"=>"certificado_notas",//par índice del array y directorio de ubicación de los docs
	"convalidaciones"=>"convalidaciones",
	"exencion_fct"=>"exencion_form_emp",
	"anulacion_matricula"=>"anulacion_matricula",
	"anulacion_modulos_modular"=>"anul_mod_modular",
	"renuncia_convocatoria"=>"renuncia_conv",
	"perdida_eval_continua"=>"perd_eval_cont",
	"homologacion_estudios"=>"homol_est",
	"titulo_eso_fpb"=>"titeso_fpb",
	"informes_orientacion"=>"orientacion",
	"fct"=>"fct",
	"transporte_escolar"=>"transporte_escolar",
	"matriculas"=>"matriculas",
	"prematriculas"=>"prematriculas",
	"otros"=>"otros"
];


foreach($tipos_doc as $tipodoc=>$ruta){
	$data["docs"][$tipodoc]=array();
	if (is_dir("../docs/".$id_nie."/".$ruta)){
		$rutaHand=opendir("../docs/".$id_nie."/".$ruta);
		while(false !== ($dir = readdir($rutaHand)))
		{
			if ($dir != "." && $dir != "..") 
			{
				if (is_dir("../docs/".$id_nie."/".$ruta."/".$dir))
				{
					$contador=0;
					$anHand=opendir("../docs/".$id_nie."/".$ruta."/".$dir);
					while(false !== ($doc = readdir($anHand)))
					{
						if($tipodoc=="convalidaciones")
						{
							if ($doc != "." && $doc != ".." && ($dir==$filtro || $filtro=="todos"))
							{
								if (is_file("../docs/".$id_nie."/".$ruta."/".$dir."/".$doc."/docs/resolucion/resolucion.pdf"))
								{
									$data["docs"][$tipodoc][$contador]["resolucion"]="docs/".$id_nie."/".$ruta."/".$dir."/".$doc."/docs/resolucion/resolucion.pdf";
								}
								else 
								{
									$data["docs"][$tipodoc][$contador]["resolucion"]="";
								}
								$docs_conv=opendir("../docs/".$id_nie."/".$ruta."/".$dir."/".$doc."/docs");
								$conval_min=false;
								$conval_con=false;
								while (false != ($listaDocs=readdir($docs_conv))){
									if ($listaDocs!="." && $listaDocs!=".." && $listaDocs!="resolucion"){
										if (substr($listaDocs, -28) === "Resolución del Ministerio.pdf"){
											$contador++;
											$data["docs"][$tipodoc][$contador]["resolucion_min"]="docs/".$id_nie."/".$ruta."/".$dir."/".$doc."/docs"."/".$listaDocs;
											$conval_min=true;
										}
										elseif (substr($listaDocs, -28) === "Resolución de Consejería.pdf"){
											$contador++;
											$data["docs"][$tipodoc][$contador]["resolucion_con"]="docs/".$id_nie."/".$ruta."/".$dir."/".$doc."/docs"."/".$listaDocs;
											$conval_con=true;
										}
									}
								}
								if(!$conval_min) $data["docs"][$tipodoc][$contador]["resolucion_min"]="";
								if(!$conval_con) $data["docs"][$tipodoc][$contador]["resolucion_con"]="";
								$subConv=opendir("../docs/".$id_nie."/".$ruta."/".$dir."/".$doc);
								while(false!=($docConv=readdir($subConv)))
								{
									if ($docConv!="." && $docConv!=".." && $docConv!="docs")
									{
										$data["error"]="ok";
										$data["docs"][$tipodoc][$contador]["curso"]=$dir;
										$data["docs"][$tipodoc][$contador]["doc"]=$docConv;
										$data["docs"][$tipodoc][$contador]["enlace"]="docs/".$id_nie."/".$ruta."/".$dir."/".$doc."/".$docConv;
										$contador++;
									}
								}
							}
						}
						elseif($tipodoc=="exencion_fct"){
							if ($doc != "." && $doc != ".." && ($dir==$filtro || $filtro=="todos"))
							{
								if (is_file("../docs/".$id_nie."/".$ruta."/".$dir."/".$doc."/docs/resolucion/resolucion.pdf"))
								{
									$data["docs"][$tipodoc][$contador]["resolucion"]="docs/".$id_nie."/".$ruta."/".$dir."/".$doc."/docs/resolucion/resolucion.pdf";
								}
								else 
								{
									$data["docs"][$tipodoc][$contador]["resolucion"]="";
								}
								if (is_file("../docs/".$id_nie."/".$ruta."/".$dir."/".$doc."/docs/informe_jd/informe_jd.pdf"))
								{
									$data["docs"][$tipodoc][$contador]["informe_jd"]="docs/".$id_nie."/".$ruta."/".$dir."/".$doc."/docs/informe_jd/informe_jd.pdf";
								}
								else 
								{
									$data["docs"][$tipodoc][$contador]["informe_jd"]="";
								}
								$subConv=opendir("../docs/".$id_nie."/".$ruta."/".$dir."/".$doc);
								while(false!=($docConv=readdir($subConv)))
								{
									if ($docConv!="." && $docConv!=".." && $docConv!="docs")
									{
										$data["error"]="ok";
										$data["docs"][$tipodoc][$contador]["curso"]=$dir;
										$data["docs"][$tipodoc][$contador]["doc"]=$docConv;
										$data["docs"][$tipodoc][$contador]["enlace"]="docs/".$id_nie."/".$ruta."/".$dir."/".$doc."/".$docConv;
										$contador++;
									}
								}
							}
						}
						else
						{
							if ($doc != "." && $doc != ".."  && ($dir==$filtro || $filtro=="todos"))
							{
								$data["error"]="ok";
								$data["docs"][$tipodoc][$contador]["curso"]=$dir;
								$data["docs"][$tipodoc][$contador]["doc"]=$doc;
								$data["docs"][$tipodoc][$contador]["enlace"]="docs/".$id_nie."/".$ruta."/".$dir."/".$doc;
								$contador++;
							}
						}
					}
					closedir($anHand);
				}
			}
		}
		closedir($rutaHand);
	}
}
exit(json_encode($data));

