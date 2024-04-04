<?php
//Este script limpia los usuarios que no han tenido actividad ni tienen documentos agregados
//El borrado se hace en las tablas usuarios y usuarios_dat


include("conexion.php");

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

function borraCarpetas($directorioBase) {
    if (!is_dir($directorioBase)) {
        echo "pasa algo";
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
$directorioBase = "../docs/2";
if(borraCarpetas($directorioBase)) echo "Borrao";
else echo "fallo";
exit();
$consulta="select * from usuarios where no_ha_entrado=1 order by id_nie";
$result = $mysqli->query($consulta);


// Verificar si hay resultados
if ($result->num_rows > 0) {
    // Recorrer cada fila de resultados
    while($row = $result->fetch_assoc()) {
        $id_nie=$row["id_nie"];
        $directorioBase = "docs/".$id_nie;
        $totalArchivos = contarArchivos($directorioBase);
        if ($totalArchivos===0){
            if ($mysqli->query("delete from usuarios where id_nie=$id_nie") === TRUE) {
                $usuarios_borrados++;
                $mysqli->query("delete from usuarios_dat where id_nie=$id_nie");
                if(borraCarpetas($directorioBase)) $carpetas_borradas++;
            }
        }
    }
    echo "Usaurios borrados: " . $usuarios_borrados . "<br>Carpetas borradas: " . $carpetas_borradas;
        
}

