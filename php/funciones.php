<?php


function normalizar_nombre($nombre) {
    $nombre = strtolower($nombre);

    // Reemplazar vocales acentuadas y diéresis por vocales simples
    $nombre = strtr($nombre, [
        'á' => 'a', 'à' => 'a', 'ä' => 'a',
        'é' => 'e', 'è' => 'e', 'ë' => 'e',
        'í' => 'i', 'ì' => 'i', 'ï' => 'i',
        'ó' => 'o', 'ò' => 'o', 'ö' => 'o',
        'ú' => 'u', 'ù' => 'u', 'ü' => 'u',
        // También puedes incluir mayúsculas si quieres tratar entradas sin `strtolower`
    ]);

    // Reemplazar cualquier carácter que no sea letra, número, espacio, ñ o ç por un espacio
    $nombre = preg_replace('/[^a-z0-9ñç\s]/u', ' ', $nombre);

    // Limpiar espacios redundantes
    $nombre = trim($nombre);
    $nombre = preg_replace('/\s+/', ' ', $nombre);

    // Lista de palabras vacías comunes (stopwords)
    $stopwords = ['el', 'la', 'los', 'las', 'un', 'una', 'unos', 'unas', 'y', 'o', 'ni', 'que', 'de', 'del', 'en', 'con', 'por', 'para', 'a'];

    // Eliminar stopwords
    $palabras = explode(' ', $nombre);
    $palabras = array_diff($palabras, $stopwords);

    // Unir todo en una sola cadena sin espacios
    return implode('', $palabras);
}

function calculaCurso_ini(){
    $mes=(int)date("n");
    $anno=(int)date("Y");
    if ($mes>=7 && $mes<=12) 
        return $anno;
    else
        return $anno-1;
}



function getRealIPAddr()
{
	//check ip from share internet
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
	{
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}
	//to check ip is pass from proxy
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))  
	{
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else
	{
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	return $ip;
}

function contarArchivos($dir) {
    $contador = 0;

    // Obtener lista de archivos en el directorio
    $archivos = glob($dir . '/*');

    // Iterar sobre cada archivo encontrado
    foreach ($archivos as $archivo) {
        // Si es un archivo, incrementa el contador
        if (is_file($archivo)) {
            $contador++;
        }
        // Si es un directorio, llama recursivamente a la función
        elseif (is_dir($archivo)) {
            $contador += contarArchivos($archivo);
        }
    }

    return $contador;
}

function borraCarpetas($directorioBase) {
    if (!is_dir($directorioBase)) {
        return false;
    }
    
    // Abrir el directorio
    $dirHandle = opendir($directorioBase);
    
    // Recorrer los contenidos del directorio
    while (($file = readdir($dirHandle)) !== false) {
        if ($file != "." && $file != "..") {
            $filePath = $directorioBase . DIRECTORY_SEPARATOR . $file;
            
            // Si es un directorio, llamar a la función recursivamente
            if (is_dir($filePath)) {
                borraCarpetas($filePath);
            } else {
                // Si es un archivo, eliminarlo
                unlink($filePath);
            }
        }
    }
    
    // Cerrar el manejador de directorio
    closedir($dirHandle);
    
    // Eliminar el directorio base
    
    return rmdir($directorioBase);
}

function password(){
    $mayus="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $minus="abcdefghijklmnopqrstuvwxyz";
    $nums="0123456789";
    $array=array("","","","","","","","");
    $password="";
    $array[0]=substr($mayus,mt_rand(0,strlen("mayus")-1),1);
    $array[1]=substr($minus,mt_rand(0,strlen("minus")-1),1);
    $array[2]=substr($nums,mt_rand(0,strlen("nums")-1),1);
    $array[3]=substr($mayus,mt_rand(0,strlen("mayus")-1),1);
    $array[4]=substr($minus,mt_rand(0,strlen("minus")-1),1);
    $array[5]=substr($nums,mt_rand(0,strlen("nums")-1),1);
    $array[6]=substr($mayus,mt_rand(0,strlen("mayus")-1),1);
    $array[7]=substr($minus,mt_rand(0,strlen("signos")-1),1);
    shuffle($array);
    $password=$array[0].$array[1].$array[2].$array[3].$array[4].$array[5].$array[6].$array[7];
    return $password;
}

function generaRegistro($conexion_db,$tabla,$raiz){
    $repite_registro=true;
    $registro="";
    while($repite_registro){
        $registro=numRegistro($raiz);
        $vReg=$conexion_db->query("select * from ".$tabla." where registro='$registro'");
        if ($conexion_db->errno>0){
            exit("database");
        }
        if ($vReg->num_rows==0) {
            $repite_registro=false;
        }
        $vReg->free();
    }
    return $registro;
}

function numRegistro($raiz){
    $raiz_centro="iesulabto_";
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
    return $raiz_centro.$raiz.date('dmY')."_".$array[0].$array[1].$array[2].$array[3].$array[4].$array[5].$array[6].$array[7];   
}

