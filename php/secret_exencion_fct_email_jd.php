<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("mail.php");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) exit("server");

$departamentos=$_POST["departamentos"];
$emails=$_POST["emails"];

// Convertir el array de departamentos en un formato adecuado para la consulta SQL (lista de valores separados por comas)
$departamentos_implode = "'" . implode("','", $departamentos) . "'";

// Preparar la consulta SQL para contar los registros por departamento y con procesado = 0
$sql = "SELECT departamento, COUNT(*) as num_registros
        FROM exencion_fct
        WHERE departamento IN ($departamentos_implode)
        AND procesado = 0
        GROUP BY departamento";

// Ejecutar la consulta
$result = $conn->query($sql);

// Comprobar si hay resultados
if ($result->num_rows > 0) {
    // Recorrer los resultados y mostrarlos
    while ($row = $result->fetch_assoc()) {
        echo "Departamento: " . $row['departamento'] . " - Registros no procesados: " . $row['num_registros'] . "<br>";
    }
} else {
    echo "No se encontraron registros para los departamentos seleccionados.";
}
/*
$asunto="Aviso de solicitudes de Exención de Formación en Empresas pendientes.";
$mensaje=$_POST["mensaje"];
$mail->addAddress($email, '');
$mail->Subject = 'REGISTRO ONLINE - '.$asunto;
$cuerpo = 'RESIDENCIA del IES Universidad Laboral<br>'.$mensaje;
$mail->Body =$cuerpo;
$mail->send();
*/

 
