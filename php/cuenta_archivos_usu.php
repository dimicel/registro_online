<?php
//Este script cuenta los archivos asociados a cada usuario dado de alta, hayan entrado o no


include("conexion.php");
set_time_limit(3600);  //Si el script se ejecuta más del número de segundos especificado en el parámetro, casca

$usuarios_borrados=0;
$carpetas_borradas=0;

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


$consulta="select * from usuarios order by id_nie";
$result = $mysqli->query($consulta);


// Verificar si hay resultados
if ($result->num_rows > 0) {
    // Recorrer cada fila de resultados
    while($row = $result->fetch_assoc()) {
        $id_nie=$row["id_nie"];
        $directorioBase = "../docs/".$id_nie;
        $totalArchivos = contarArchivos($directorioBase);
        echo "NIE: " . $id_nie . " Nº archivos asociados: " .  $totalArchivos . " No ha entrado: " . $row["no_ha_entrado"] . "<br>";
    }
    
        
}

