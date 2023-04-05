<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
include("mail.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) exit("server");

$tabla=$_POST["formulario"];
$registro=$_POST["registro"];
$incidencias=$_POST["incidencias"];
$aviso_incidencia_solventada=$_POST["aviso_incidencia_solventada"];
$email="";

$c1=$mysqli->query("select id_nie,registro from $tabla  where registro='$registro'");
if ($c1->num_rows==1){
    $i=$c1->fetch_array(MYSQLI_ASSOC);
    $id_nie=$i["id_nie"];
    $c2=$mysqli->query("select id_nie,email from usuarios  where id_nie='$id_nie'");
    if ($c2->num_rows==1){
        $j=$c2->fetch_array(MYSQLI_ASSOC);
        $email=$j["email"];
    }
}

$consulta="update $tabla set incidencias='$incidencias' where registro='$registro'";

if($mysqli->query($consulta)){
    if($email!=""){
        $mail->addAddress($email, '');
        $mail->Subject = 'Registro Online';

        $cuerpo = 'Registro online del IES Universidad Laboral<br>';
        if (trim($incidencias)=="" && $aviso_incidencia_solventada==1){
            $cuerpo .= 'El formulario con número de registro: '.$registro.' ya no tiene incidencias.';
            $mail->Body =$cuerpo;
            $mail->send();
        }
        elseif (trim($incidencias)!="") {
            $cuerpo .= 'Se ha registrado una incidencia en el formulario con número de registro: '.$registro.'<br>';
            $cuerpo .= 'La incidencia es:<br>';
            $cuerpo .= $incidencias.'<br><br>';
            $cuerpo .= 'Acceda al panel de control de usuario del Registro Online para visualizarla cuando quiera.';
            $mail->Body =$cuerpo;
            $mail->send();
        }
        
    }
    exit("ok");
} 
else exit("Fallo: " . $mysqli->error);

$mysqli->close();

 
