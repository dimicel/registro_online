<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");

if ($_POST["usuario"]!="secretaria") exit("Acceso denegado");

// Ruta del directorio raíz que deseas comprimir
$directorio = '../docs/fotos';

// Ruta y nombre del archivo ZIP de salida
$archivoZip = '../docs/tmp/fotos_alumnos'.time().".zip";

// Crear una instancia de ZipArchive
$zip = new ZipArchive();

// Crear un archivo ZIP
if ($zip->open($archivoZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
    $archivos = glob($directorio . '/*.jpg');
    $archivos = array_merge($archivos, glob($directorio . '/*.jpeg'));
    foreach ($archivos as $archivo) {
        if (!is_dir($archivo)) {
            $zip->addFile($archivo, $ruta . basename($archivo));
        } 
    }

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

