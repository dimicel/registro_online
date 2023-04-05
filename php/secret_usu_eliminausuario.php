<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");
$error=false;
$cont_ok=0;
$cont_error=0;
$error_foto=false;
$error_cert=false;
$error_dni=false;
$error_seguro=false;

if ($mysqli->errno>0) {
    exit("server");
}

$id_nie=$_POST["id"];

$tablas = $mysqli->query("SHOW TABLES FROM ulaboral_imp_sec_online");
if ($tablas){
    while ($fila = $tablas->fetch_row()) {
        $existe_campo=false;
        $campos=$mysqli->query("DESCRIBE $fila[0]");
        while ($campo = $campos->fetch_row()){
            if ($campo[0]=="id_nie"){
                $existe_campo=true;
                break;
            }
        }
        if ($existe_campo){
            $mysqli->query("delete from $fila[0] where id_nie='$id_nie'");
            if ($mysqli->errno>0)$cont_error++;
            else if ($mysqli->affected_rows>0) $cont_ok++;
        }
    }
}


$ruta_destino=$id_nie;
if (is_dir("../docs/usuarios_eliminados/".$ruta_destino)){
    $contador=1;
    while(true){
        if (is_dir("../docs/usuarios_eliminados/".$ruta_destino."(".$contador.")")) $contador++;
        else {
            $ruta_destino=$ruta_destino."(".$contador.")";
            break;
        }
    }
}

copiaArbol("../docs/".$id_nie,"../docs/usuarios_eliminados/".$ruta_destino,$id_nie);
borraArbol("../docs/".$id_nie,$id_nie);


if ($cont_error>0 && $cont_ok==0) exit("error");
else if($cont_error>0 && $cont_ok>0) exit ("error_parcial");
else if($cont_error==0 && ($error_cert || $error_dni || $error_foto || $error_seguro)) exit ("error_imagenes");
else exit("ok");



function copiaArbol($dirOrigen, $dirDestino,$id)
{
	mkdir($dirDestino, 0777, true);
    mkdir($dirDestino."/foto",0777,true);
    copy("../docs/fotos/".$id.".jpeg",$dirDestino."/"."foto/".$id.".jpeg");
	
	if (!$vcarga = opendir($dirOrigen)) return false;
	while(false !== ($file = readdir($vcarga)))
	{
		if ($file != "." && $file != "..") 
		{
			if (!is_dir($dirOrigen."/".$file)) copy($dirOrigen."/".$file, $dirDestino."/".$file);
			else copiaArbol($dirOrigen."/".$file, $dirDestino."/".$file,$id);
		}
	}
	closedir($vcarga);
	return true;
}

function borraArbol($dir,$id) {
    unlink("../docs/fotos/".$id.".jpeg");
    if(!$dh = opendir($dir)) return false;
    while (false !== ($current = readdir($dh))) {
        if($current != '.' && $current != '..') {
            if (!is_dir($dir.'/'.$current)) unlink($dir.'/'.$current);
            else borraArbol($dir.'/'.$current,$id);
        }       
    }
    closedir($dh);
    rmdir($dir);
	return true;
}