<?php
include "php/conexion.php";

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

$consulta=$mysqli->query("select * from usuarios where no_ha_entrado=1 order by id_nie");
$result = $conn->query($consulta);

// Verificar si hay resultados
if ($result->num_rows > 0) {
    // Recorrer cada fila de resultados
    while($row = $result->fetch_assoc()) {
        $id_nie=$row["id_nie"];
        $directorioBase = "docs/".$id_nie;
        $totalArchivos = contarArchivos($directorioBase);
        if ($totalArchivos===0){
            $sql = "DELETE usuarios, usuarios_dat
            FROM usuarios
            INNER JOIN usuarios_dat ON usuarios.id_nie = usuarios_dat.id_nie
            WHERE usuarios.id_nie = $id_nie";
            if ($conn->query($sql) === TRUE) {
                borraCarpetas($directorioBase);
            }
        }
    }
        
}

