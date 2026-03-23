<?php

// Configuración de IDs y Rutas
$idOrigen = $_POST["id_origen"]; 
$idDestino = $_POST["id_destino"];  
$basePath = __DIR__ . '/../docs'; 

fusionarExpedientes($idOrigen, $idDestino, $basePath);
procesarCarpetaFotos($idOrigen, $idDestino, $basePath);

function fusionarExpedientes($origen, $destino, $basePath) {
    $pathOrigen = rtrim($basePath, '/') . '/' . $origen;
    $pathDestino = rtrim($basePath, '/') . '/' . $destino;

    if (!is_dir($pathOrigen)) {
        echo "Aviso: El expediente de origen no existe en $pathOrigen. Saltando fusión de carpetas.\n";
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
            procesarLogicaFicheros($item, $pathOrigen, $pathDestino, $origen, $destino);
        }
    }
}

function procesarLogicaFicheros($fileInfo, $pathOrigen, $pathDestino, $idOri, $idDest) {
    $fileName = $fileInfo->getFilename();
    $relativeSubPath = str_replace($pathOrigen, '', $fileInfo->getPathname());
    
    $parts = explode(DIRECTORY_SEPARATOR, ltrim($relativeSubPath, DIRECTORY_SEPARATOR));
    $folderType = $parts[0];

    $soloUno = [
        'anulacion_matricula', 'certificado_notas', 'matriculas', 
        'prematriculas', 'residencia', 'seguro', 'transporte'
    ];

    $newFileName = str_replace($idOri, $idDest, $fileName);
    $targetDir = $pathDestino . dirname($relativeSubPath);
    $targetFile = $targetDir . DIRECTORY_SEPARATOR . $newFileName;

    if (in_array($folderType, $soloUno)) {
        $archivosEnDestino = glob($targetDir . DIRECTORY_SEPARATOR . '*');

        if (!empty($archivosEnDestino)) {
            $archivoExistente = $archivosEnDestino[0];
            if (obtenerFecha($fileInfo) > obtenerFecha(new SplFileInfo($archivoExistente))) {
                unlink($archivoExistente);
                copy($fileInfo->getPathname(), $targetFile);
            }
        } else {
            copy($fileInfo->getPathname(), $targetFile);
        }
    } else {
        if (!file_exists($targetFile) || obtenerFecha($fileInfo) > obtenerFecha(new SplFileInfo($targetFile))) {
            copy($fileInfo->getPathname(), $targetFile);
        }
    }
}

/**
 * Lógica específica para docs/fotos/ID.jpeg
 */
function procesarCarpetaFotos($idOri, $idDest, $basePath) {
    $dirFotos = rtrim($basePath, '/') . '/fotos';
    
    if (!is_dir($dirFotos)) return;

    $fotoOri = $dirFotos . DIRECTORY_SEPARATOR . $idOri . '.jpeg';
    $fotoDest = $dirFotos . DIRECTORY_SEPARATOR . $idDest . '.jpeg';

    $existeOri = file_exists($fotoOri);
    $existeDest = file_exists($fotoDest);

    if ($existeOri && $existeDest) {
        // Si existen ambas, dejamos la más nueva en el destino
        if (filemtime($fotoOri) > filemtime($fotoDest)) {
            copy($fotoOri, $fotoDest);
        }
    } elseif ($existeOri && !$existeDest) {
        // Si solo existe origen, la renombramos/copiamos al destino
        copy($fotoOri, $fotoDest);
    }
    // Si solo existe destino, no se hace nada según tu instrucción
}

function obtenerFecha($fileInfo) {
    $name = $fileInfo->getFilename();
    if (preg_match('/_(\d{8})_/', $name, $matches)) {
        return (int)$matches[1]; 
    }
    return $fileInfo->getMTime();
}

echo "ok";