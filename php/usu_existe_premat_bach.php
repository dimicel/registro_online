<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
$mysqli->set_charset("utf8");
$respuesta=array();

if ($mysqli->errno>0){
    $respuesta["error"]="server";
    exit(json_encode($respuesta));
} 

$id_nie=$_POST["id_nie"];
$curso=$_POST["curso"];
$cursos=array(
    "pm1bac"=>"premat_bach",
    "pm1bah"=>"premat_bach",
    "pm1bag"=>"premat_bach",
    "pm2bac"=>"premat_bach",
    "pm2bah"=>"premat_bach"
);

$cursos2=array(
    "pm1bac"=>"1º Bachillerato Ciencias y Tecnología",
    "pm1bah"=>"1º Bachillerato Humanidades y Ciencias Sociales",
    "pm1bag"=>"1º Bachillerato General",
    "pm2bac"=>"2º Bachillerato Ciencias y Tecnología",
    "pm2bah"=>"2º Bachillerato Humanidades y Ciencias Sociales"
);

$campos="sexo,apellidos,nombre,telef_alumno,email,registro,tutor1,";
$campos.="email_tutor1,tlf_tutor1,tutor2,email_tutor2,tlf_tutor2";
$consulta="select * from premat_bach where id_nie='$id_nie' and curso='$curso'";

$res=$mysqli->query($consulta);

if ($res->num_rows>0) {
    $reg=$res->fetch_array(MYSQLI_ASSOC);
    $respuesta["error"]="ok";
    $respuesta["sexo"]=$reg["sexo"];
    $respuesta["apellidos"]=$reg["apellidos"];
    $respuesta["nombre"]=$reg["nombre"];
    $respuesta["telef_alumno"]=$reg["telef_alumno"];
    $respuesta["email_alumno"]=$reg["email"];
    $respuesta["registro"]=$reg["registro"];
    $respuesta["tabla"]=$cursos[substr($reg["registro"],10,6)];
    $respuesta["curso_prematricula"]=$cursos2[substr($reg["registro"],10,6)];
    $respuesta["tutor1"]=$reg["tutor1"];
    $respuesta["email_tutor1"]=$reg["email_tutor1"];
    $respuesta["tlf_tutor1"]=$reg["tlf_tutor1"];
    $respuesta["tutor2"]=$reg["tutor2"];
    $respuesta["email_tutor2"]=$reg["email_tutor2"];
    $respuesta["tlf_tutor2"]=$reg["tlf_tutor2"];
    $res->free();
    exit(json_encode($respuesta));
}

else {
    $res->free();
    $respuesta["error"]="noexiste";
    exit(json_encode($respuesta));
}

