<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
include("mail.php");
header("Content-Type: text/html;charset=utf-8");
session_start();
session_regenerate_id();
header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
	
if ($mysqli->errno>0) {
    exit("server");
}

$nie=$_POST["nie"];
$email=$_POST["email"];
$password=$_POST["password"];
$nombre=$_POST["nombre"];
$apellidos=$_POST["apellidos"];
$nif=$_POST["nif"];
$pass=password_hash($_POST["password"],PASSWORD_BCRYPT);
$mysqli->set_charset("utf8");

$consulta=$mysqli->query("select * from usuarios where id_nie='$nie' and no_ha_entrado=1");
if ($consulta->num_rows>0) exit("usuario");
$consulta->free();
$consulta=$mysqli->query("select * from usuarios where id_nie='$nie' and no_ha_entrado=0");
if ($consulta->num_rows>0) exit("registrado");
$consulta->free();

$mysqli->query("insert into usuarios (id_nie,password,no_ha_entrado,nombre,apellidos,id_nif,email) values ('$nie','$pass',1,'$nombre','$apellidos','$nif','$email')");
if ($mysqli->errno>0){
	exit("fallo_alta");		
}

if(!is_dir("../docs/".$nie)) mkdir("../docs/".$nie,0777);
if (!is_dir("../docs/".$nie."/seguro")) mkdir("../docs/".$nie."/seguro",0777);
if (!is_dir("../docs/".$nie."/dni")) mkdir("../docs/".$nie."/dni",0777);
if (!is_dir("../docs/".$nie."/certificado_notas")) mkdir("../docs/".$nie."/certificado_notas",0777);

if ($email!=''){
    $mensaje="      TRAMITACIÓN ONLINE DE DOCUMENTACIÓN DEL IES UNIVERSIDAD LABORAL DE TOLEDO <br><br><br>";
    $mensaje.="No responda a este correo. Este es un mensaje automático.<br>";
    $mensaje.="Cuando entre por primera vez con estas credenciales, deberá revisar o completar un formulario con los datos iniciales.<br>";
    $mensaje.="En dicho formulario deberá cambiar la contraseña que se le entrega por otra que usted desee.<br><br>";
    $mensaje.="Sus credenciales para el primer acceso son:<br>";
    $mensaje.="          NIE: ".$nie . "<br>";
    $mensaje.="          Contraseña: ". $password . "<br>" . "<br>" . "<br>";
    $mensaje.="<bold>TRATAMIENTO DE LOS DATOS</bold>";
    $mensaje.="<ul>";
    $mensaje.="<li>";
    $mensaje.="<p><bold style='color:black;text-decoration: underline;'>Responsable: </bold>Instituto Educacion Secundaria Universidad Laboral – Toledo.</p>";
    $mensaje.="</li>";
    $mensaje.="<li>";
    $mensaje.="<p><bold style='color:black;text-decoration: underline;'>Finalidad: </bold>Gestión administrativa de la comunidad educativa del IES Universidad Laboral de Toledo.</p>";
    $mensaje.="</li>";
    $mensaje.="<li>";
    $mensaje.="<p><bold style='color:black;text-decoration: underline;'>Legitimación: </bold>6.1.e) Misión en interés público o ejercicio de poderes públicos del Reglamento General de Protección de Datos. Ley Orgánica 2/2006, de 3 de mayo, de Educación</p>";
    $mensaje.="</li>";
    $mensaje.="<li>";
    $mensaje.="<p><bold style='color:black;text-decoration: underline;'>Origen de los datos: </bold>El Propio Interesado o su Representante Legal; Administraciones Públicas.</p>";
    $mensaje.="</li>";
    $mensaje.="<li>";
    $mensaje.="<p><bold style='color:black;text-decoration: underline;'>Categoría de los datos: </bold>Datos de carácter identificativo: NIF/DNI, nombre y apellidos, dirección, teléfono, correo electrónico, imagen. Características personales: nacionalidad, fecha nacimiento, sexo. Datos académicos.</p>";
    $mensaje.="</li>";
    $mensaje.="<li>";
    $mensaje.="<p><bold style='color:black;text-decoration: underline;'>Destinatario: </bold>No existe cesión de datos.</p>";
    $mensaje.="</li>";
    $mensaje.="<li>";
    $mensaje.="<p><bold style='color:black;text-decoration: underline;'>Derechos: </bold>Puede ejercer los derechos de acceso, rectificación o supresión de sus datos, así como otros derechos, tal y como se explica en la información adicional.</p>";
    $mensaje.="</li>";
    $mensaje.="<li>";
    $mensaje.="<p><bold style='color:black;text-decoration: underline;'>Información adicional: </bold>Disponible en la dirección electrónica: <a href='https://rat.castillalamancha.es/info/2018' target='_blank'>https://rat.castillalamancha.es/info/2018</a></p>";
    $mensaje.="</li>";
    $mensaje.="<li>";
    $mensaje.="<p>";
    $mensaje.="<bold style='color:black;text-decoration: underline;'>Consentimiento: </bold>Consiento que mis datos sean tratados conforme a las características del tratamiento previamente descrito.<br> Puede retirar este consentimiento solicitándolo en el siguiente correo electrónico: <a href='mailto:protecciondatos@jccm.es'";
    $mensaje.="target='_blank'>protecciondatos@jccm.es</a> o <a href='mailto:protecciondedatos.educacion@jccm.es' target='_blank'>protecciondedatos.educacion@jccm.es</a>";
    $mensaje.="</p>";
    $mensaje.="</li>";
    $mensaje.="</ul>";

    
    $mail->addAddress($email, '');
    $mail->Subject = 'Nuevo Acceso - Sistema Tramitación Online del IES Universidad Laboral';
    
    $mail->Body =$mensaje;

    try{
        $check_envio=$mail->send();
        exit ("email");
    } catch (Exception  $e){
        exit ("envio ".$e->ErrorInfo);
    }
    
}
else exit("ok");