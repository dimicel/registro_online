<?php
//Este script limpia los usuarios que no han tenido actividad ni tienen documentos agregados
//El borrado se hace en las tablas usuarios y usuarios_dat


exit(); //Para que no se ejecute hasta que haga falta

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
        if ($totalArchivos===0){
            if ($mysqli->query("delete from usuarios where id_nie=$id_nie") === TRUE) {
                $usuarios_borrados++;
                echo "Usuarios eliminados: " . $usuarios_borrados . "<br>";
                $mysqli->query("delete from usuarios_dat where id_nie=$id_nie");
                if(borraCarpetas($directorioBase)){
                    $carpetas_borradas++;
                    echo "Carpetas borradas: " . $carpetas_borradas . "<br>";
                } 
            }
        }
    }
    echo "Usuarios Totales borrados: " . $usuarios_borrados . "<br>Carpetas Totales borradas: " . $carpetas_borradas;
        
}

