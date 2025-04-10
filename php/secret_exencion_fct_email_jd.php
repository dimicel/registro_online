<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPmailer/src/Exception.php';
require 'PHPmailer/src/PHPMailer.php';
require 'PHPmailer/src/SMTP.php';

include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) exit("server");

$departamentos=$_POST["departamentos"];
$emails=$_POST["emails"];
$envios_email= array();

// Convertir el array de departamentos en un formato adecuado para la consulta SQL (lista de valores separados por comas)
$departamentos_implode = "'" . implode("','", $departamentos) . "'";
// Preparar la consulta SQL para contar los registros por departamento y con procesado = 0
$sql = "SELECT departamento, procesado, COUNT(*) as num_registros
        FROM exencion_fct
        WHERE departamento IN ($departamentos_implode)
        AND procesado = 0
        GROUP BY departamento";

// Ejecutar la consulta
$result = $mysqli->query($sql);

// Comprobar si hay resultados
if ($result->num_rows > 0) {
    // Recorrer los resultados y mostrarlos
    $contador=0;
    while ($row = $result->fetch_assoc()) {
        if ($row['num_registros']>0){
            $envios_email[$contador]["departamento"]=$row['departamento'];
            $envios_email[$contador]["email"]=$emails[array_search($row['departamento'],$departamentos)];
            $envios_email[$contador]["pendientes"]=$row['num_registros'];
            $contador++;
        }  
    }
} else {
    echo "No se encontraron registros para los departamentos seleccionados.";
}
$error="";
$mysqli->close();

for ($i=0; $i<count($envios_email);$i++){
    
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();
    $mail->Host = 'ssl://mail.ulaboral.org';
    $mail->SMTPAuth = TRUE;
    $mail->SMTPSecure = 'tls';
    $mail->Username = 'noresponder@ies.ulaboral.org';
    $mail->Password = 'Uni-L@boral-23';

    // Set the SMTP port. 587 para TLS 
    $mail->Port = 465;
    $mail->setLanguage('es', 'PHPmailer/language/');
    $mail->setFrom('noresponder@ies.ulaboral.org', 'Registro Online - IES Universidad Laboral');
    $mail->isHTML(true);

    $asunto="Aviso de solicitudes de Exención PFE pendientes.";
    $mensaje="Hay pendientes de resolver ".$envios_email[$i]["pendientes"] . " solicitudes de Exención de Formación en la Empresa.";
    $mensaje.="<br>No responda a este correo. El contenido del mismo se ha generado automáticamente.";
    $mail->addAddress($envios_email[$i]["email"], '');
    $mail->Subject = 'REGISTRO ONLINE - '.$asunto;
    $cuerpo = 'Departamento de '.$envios_email[$i]["departamento"].'<br>'.$mensaje;
    $mail->Body =$cuerpo;
    if (!$mail->send()){
        $error.=$envios_email[$i]["departamento"]."<br>";
    }
    exit("it ".$i);
}

if ($error=="") exit("ok");
else exit($error);



 
