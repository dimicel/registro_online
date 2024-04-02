<?php
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

// Directorio base donde se va a contar
$directorioBase = "docs/7";

// Llama a la función contarArchivos() con el directorio base
$totalArchivos = contarArchivos($directorioBase);

// Muestra el resultado
echo "El número total de archivos en el directorio '$directorioBase' es: $totalArchivos";
?>

