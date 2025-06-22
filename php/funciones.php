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