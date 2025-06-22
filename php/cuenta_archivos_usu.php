<?php
//Este script cuenta los archivos asociados a cada usuario dado de alta, hayan entrado o no


include("conexion.php");
include("funciones.php");
set_time_limit(3600);  //Si el script se ejecuta más del número de segundos especificado en el parámetro, casca

$usuarios_borrados=0;
$carpetas_borradas=0;




$consulta="select * from usuarios where no_ha_entrado=1 order by id_nie";
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

