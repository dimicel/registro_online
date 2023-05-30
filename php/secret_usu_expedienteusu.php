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
				if (is_dir("../docs/".$id_nie."/".$ruta."/".$dir)){
					$contador=0;
					$anHand=opendir("../docs/".$id_nie."/".$ruta."/".$dir);
					while(false !== ($doc = readdir($anHand))){
						if($tipodoc=="convalidaciones"){
							$subConv=opendir("docs/".$id_nie."/".$ruta."/".$dir."/".$doc);
							$data["error"]=$subConv;
							exit(json_encode($data));
							while(false!=($docConv=readdir($subConv))){
								if ($docConv != "." && $docConv != ".."  && ($dir==$filtro || $filtro=="todos") && !is_dir($docConv)){
									$data["error"]="ok";
									$data["docs"][$tipodoc][$contador]["curso"]=$dir;
									$data["docs"][$tipodoc][$contador]["doc"]=$docConv;
									$data["docs"][$tipodoc][$contador]["enlace"]="docs/".$id_nie."/".$ruta."/".$dir."/".$doc."/".$docConv;
									$contador++;
								}
							}
						}
						else{
							if ($doc != "." && $doc != ".."  && ($dir==$filtro || $filtro=="todos")){
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

