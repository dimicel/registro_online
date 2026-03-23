<?php

/**
 * SCRIPT DE FUSIÓN DE EXPEDIENTES - VERSIÓN FINAL
 * Origen: $idOrigen -> Destino: $idDestino
 * Ubicación de archivos: ../docs
 */

$idOrigen = 'ID_ORIGEN'; // Cambiar por el ID real
$idDestino = 'ID_DESTINO';  // Cambiar por el ID real
$basePath = __DIR__ . '/../docs'; 

// 1. Ejecutar fusión de la estructura de carpetas
fusionarExpedientes($idOrigen, $idDestino, $basePath);

// 2. Ejecutar lógica específica de la carpeta fotos
procesarCarpetaFotos($idOrigen, $idDestino, $basePath);

echo "Proceso finalizado con éxito.\n";

/**
 * Recorre la estructura del alumno origen y la fusiona con el destino
 */
function fusionarExpedientes($origen, $destino, $basePath) {
    $pathOrigen = rtrim($basePath, '/') . '/' . $origen;
    $pathDestino = rtrim($basePath, '/') . '/' . $destino;

    if (!is_dir($pathOrigen)) {
        echo "Aviso: El expediente de origen no existe en $pathOrigen.\n";
        return;
    }

    if (!is_dir($pathDestino)) {
        mkdir($pathDestino, 0755, true);
    }

    $directory = new RecursiveDirectoryIterator($pathOrigen, RecursiveDirectoryIterator::SKIP_DOTS);
    $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST);

    foreach ($iterator as $item) {
        $relativeSubPath = str_replace($pathOrigen, '', $item->getPathname());
        $targetPath = $pathDestino . $relativeSubPath;

        if ($item->isDir()) {
            if (!is_dir($targetPath)) {
                mkdir($targetPath, 0755, true);
            }
        } else {
            procesarArchivoConfigurado($item, $pathOrigen, $pathDestino, $origen, $destino);
        }
    }
}

/**
 * Gestiona la copia, renombrado y comparación de fechas
 */
function procesarArchivoConfigurado($fileInfo, $pathOrigen, $pathDestino, $idOri, $idDest) {
    $fileName = $fileInfo->getFilename();
    $relativeSubPath = str_replace($pathOrigen, '', $fileInfo->getPathname());
    
    // Identificar carpeta del formulario (ej: matriculas)
    $parts = explode(DIRECTORY_SEPARATOR, ltrim($relativeSubPath, DIRECTORY_SEPARATOR));
    $folderType = $parts[0];

    $soloUno = [
        'anulacion_matricula', 'certificado_notas', 'matriculas', 
        'prematriculas', 'residencia', 'seguro', 'transporte'
    ];

    // Renombrar el archivo si contiene el ID (ej: seguro o dni)
    $newFileName = str_replace($idOri, $idDest, $fileName);
    $targetDir = $pathDestino . dirname($relativeSubPath);
    $targetFile = $targetDir . DIRECTORY_SEPARATOR . $newFileName;

    if (in_array($folderType, $soloUno)) {
        // Lógica: Solo un archivo por subcarpeta de curso
        $archivosEnDestino = glob($targetDir . DIRECTORY_SEPARATOR . '*');

        if (!empty($archivosEnDestino)) {
            $archivoExistente = $archivosEnDestino[0];
            
            $fechaOri = obtenerTimestamp($fileInfo);
            $fechaDest = obtenerTimestamp(new SplFileInfo($archivoExistente));

            if ($fechaOri > $fechaDest) {
                // El de origen es más nuevo: Borrar destino y poner origen (renombrado)
                unlink($archivoExistente);
                copy($fileInfo->getPathname(), $targetFile);
            }
        } else {
            // Destino vacío: copiar directamente
            copy($fileInfo->getPathname(), $targetFile);
        }
    } else {
        // Lógica para el resto (convalidaciones, etc.): acumulativo o actualización por nombre
        if (!file_exists($targetFile) || obtenerTimestamp($fileInfo) > obtenerTimestamp(new SplFileInfo($targetFile))) {
            copy($fileInfo->getPathname(), $targetFile);
        }
    }
}

/**
 * Lógica para docs/fotos/ID.jpeg
 */
function procesarCarpetaFotos($idOri, $idDest, $basePath) {
    $dirFotos = rtrim($basePath, '/') . '/fotos';
    if (!is_dir($dirFotos)) return;

    $fotoOri = $dirFotos . DIRECTORY_SEPARATOR . $idOri . '.jpeg';
    $fotoDest = $dirFotos . DIRECTORY_SEPARATOR . $idDest . '.jpeg';

    if (file_exists($fotoOri) && file_exists($fotoDest)) {
        if (filemtime($fotoOri) > filemtime($fotoDest)) {
            copy($fotoOri, $fotoDest);
        }
    } elseif (file_exists($fotoOri) && !file_exists($fotoDest)) {
        copy($fotoOri, $fotoDest);
    }
}

/**
 * Extrae la fecha del nombre o del sistema y devuelve un timestamp comparable
 * Formato esperado: iesulabto_XXXXXX_DDMMAAAA_XXXXXXXX.pdf
 */
function obtenerTimestamp($fileInfo) {
    $name = $fileInfo->getFilename();

    // Regex para capturar DDMMAAAA después del segundo guion bajo
    // iesulabto_ (9 chars) + tipo (6 chars) + _ + fecha (8 chars)
    if (preg_match('/^iesulabto_[a-zA-Z0-0]{6}_(\d{2})(\d{2})(\d{4})_/', $name, $matches)) {
        $dia = $matches[1];
        $mes = $matches[2];
        $anio = $matches[3];
        // Convertimos a timestamp Unix para que la comparación sea numérica y exacta
        return mktime(0, 0, 0, (int)$mes, (int)$dia, (int)$anio);
    }

    // Si no es un archivo iesulabto, usamos la fecha de modificación del sistema
    return $fileInfo->getMTime();
}