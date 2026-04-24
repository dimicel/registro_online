<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("mail.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) exit("server");

$lista_destinatarios = [];

if (isset($_POST["emails"]) && is_array($_POST["emails"])) {
    // Si viene como array, lo usamos directamente
    $lista_destinatarios = $_POST["emails"];
} elseif (isset($_POST["email"])) {
    // Si viene como string, lo metemos dentro de un array para unificar
    $lista_destinatarios = [$_POST["email"]];
} else {
    exit("no_email");
}

$asunto=$_POST["asunto"];
$mensaje=$_POST["mensaje"];
// 1. AÑADE UN DESTINATARIO FIJO EN EL CAMPO "PARA" (Requerido por SMTP)
// Usamos la misma cuenta de envío para que el correo sea válido
$mail->addAddress('noresponder@ies.ulaboral.org', 'Destinatarios Registro Online');

// 2. AÑADE LOS DESTINATARIOS EN COPIA OCULTA (BCC)
foreach ($lista_destinatarios as $email) {
    // Es buena práctica validar que el email no esté vacío antes de añadirlo
    if (!empty(trim($email))) {
        $mail->addBCC(trim($email)); 
    }
}

$mail->Subject = 'Registro Online - ' . $asunto;
$cuerpo = 'Registro online del IES Universidad Laboral<br>' . $mensaje;
$mail->Body = $cuerpo;

// 3. ENVÍO
try {
    $mail->send();
    echo "ok";
} catch (Exception $e) {
    echo "Error al enviar: " . $mail->ErrorInfo;
}


 
