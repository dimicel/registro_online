<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");

$id_nie=$_POST["id_down"];
$nombre=$_POST["nombre_down"];

// Ruta del directorio raíz que deseas comprimir
$directorio = '../docs/'.$id_nie;

// Ruta y nombre del archivo ZIP de salida
$archivoZip = '../docs/'.$id_nie.'_'.$nombre.'.zip';

// Crear una instancia de ZipArchive
$zip = new ZipArchive();

// Crear un archivo ZIP
if ($zip->open($archivoZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
    // Función recursiva para agregar archivos y directorios al ZIP
    function agregarDirectorioAlZip($directorio, $zip, $ruta = '') {
        $archivos = glob($directorio . '/*');
        foreach ($archivos as $archivo) {
            if (is_dir($archivo)) {
                agregarDirectorioAlZip($archivo, $zip, $ruta . basename($archivo) . '/');
            } else {
                $zip->addFile($archivo, $ruta . basename($archivo));
            }
        }
    }

    // Agregar el contenido del directorio al ZIP
    agregarDirectorioAlZip($directorio, $zip);

    // Cerrar el archivo ZIP
    $zip->close();

    // Descargar el archivo ZIP
    header('Content-Type: application/zip');
    header('Content-disposition: attachment; filename=' . basename($archivoZip));
    header('Content-Length: ' . filesize($archivoZip));
    readfile($archivoZip);

    // Eliminar el archivo ZIP
    unlink($archivoZip);
} 
?>
