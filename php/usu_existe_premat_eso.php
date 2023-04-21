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
    "pm2eso"=>"premat_2eso",
    "pm3eso"=>"premat_3eso",
    "pm4eso"=>"premat_4eso"
    
);

$cursos2=array(
    "pm2eso"=>"2º ESO",
    "pm3eso"=>"3º ESO",
    "pm4eso"=>"4º ESO"
);

$campos="prog_ling,sexo,apellidos,nombre,id_nif,telef_alumno,email,registro,tutor1,";
$campos.="email_tutor1,nif_nie_tutor2,tlf_tutor1,tutor2,email_tutor2,nif_nie_tutor2,tlf_tutor2,";
$campos.="direccion,cp,localidad,provincia";
$consulta="(select $campos from premat_eso where id_nie='$id_nie' and curso='$curso') union all";


$res=$mysqli->query($consulta);

if ($res->num_rows>0) {
    $reg=$res->fetch_array(MYSQLI_ASSOC);
    $respuesta["error"]="ok";
    $respuesta["prog_ling"]=$reg["prog_ling"];
    $respuesta["sexo"]=$reg["sexo"];
    $respuesta["apellidos"]=$reg["apellidos"];
    $respuesta["nombre"]=$reg["nombre"];
    $respuesta["nif_nie"]=$reg["id_nif"];
    $respuesta["telef_alumno"]=$reg["telef_alumno"];
    $respuesta["email_alumno"]=$reg["email"];
    $respuesta["registro"]=$reg["registro"];
    $respuesta["tabla"]=$cursos[substr($reg["registro"],10,6)];
    $respuesta["curso_prematricula"]=$cursos2[substr($reg["registro"],10,6)];
    $respuesta["tutor1"]=$reg["tutor1"];
    $respuesta["email_tutor1"]=$reg["email_tutor1"];
    $respuesta["nif_nie_tutor1"]=$reg["nif_nie_tutor1"];
    $respuesta["tlf_tutor1"]=$reg["tlf_tutor1"];
    $respuesta["tutor2"]=$reg["tutor2"];
    $respuesta["email_tutor2"]=$reg["email_tutor2"];
    $respuesta["nif_nie_tutor2"]=$reg["nif_nie_tutor2"];
    $respuesta["tlf_tutor2"]=$reg["tlf_tutor2"];
    $respuesta["direccion"]=$reg["direccion"];
    $respuesta["cp"]=$reg["cp"];
    $respuesta["localidad"]=$reg["localidad"];
    $respuesta["provincia"]=$reg["provincia"];
    $res->free();
    exit(json_encode($respuesta));
}

else {
    $res->free();
    $respuesta["error"]="noexiste";
    exit(json_encode($respuesta));
}

