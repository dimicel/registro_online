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

$registro=$_POST["registro"];

// Ejecutar la consulta
$result = $mysqli->query("SELECT * FROM exencion_fct WHERE registro='$registro'");

// Comprobar si hay resultados
if ($result->num_rows == 1 ) {
    $fila = $result->fetch_assoc();
    $id_nie=$fila['id_nie'];
    $curso=$fila['curso'];
    // Recorrer los resultados y mostrarlos
    $mysqli->query("UPDATE exencion_fct SET procesado=0 WHERE registro='$registro'");
    if ($mysqli->errno>0) exit("server"); 
    else{
        if(is_file("../docs/".$id_nie."/exencion_form_emp/".$curso."/".substr($registro, 17)."/docs/informe_jd/informe_jd.pdf")){
            if(!unlink("../docs/".$id_nie."/exencion_form_emp/".$curso."/".substr($registro, 17)."/docs/informe_jd/informe_jd.pdf")){
                exit("no_borrado");
            }
        }
        else{
            exit("no_existe");
        }
        if(is_file("../docs/".$id_nie."/exencion_form_emp/".$curso."/".substr($registro, 17)."/docs/resolucion/resolucion.pdf")){
            if(!unlink("../docs/".$id_nie."/exencion_form_emp/".$curso."/".substr($registro, 17)."/docs/resolucion/resolucion.pdf")){
                exit("res_no_borrado");
            }
        }
    }
} else {
    exit("no_registro");
}
exit("ok");
