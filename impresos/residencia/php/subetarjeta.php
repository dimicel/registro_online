<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");

$id_nie=$_POST["id_nie"];

if(is_uploaded_file($_FILES['tarjeta_sanitaria']['tmp_name'])){
    if(!is_dir("../../../docs/".$id_nie."/tarjeta_sanitaria"."/"))mkdir("../../../docs/".$id_nie."/tarjeta_sanitaria"."/",0777);
    $ruta="../../../docs/".$id_nie."/"."tarjeta_sanitaria/ts_". $id_nie.".jpeg";
    if(!move_uploaded_file($_FILES['tarjeta_sanitaria']['tmp_name'], $ruta)) exit("almacenar");
    chmod($ruta, 0777);
    exit("ok");
}
else exit("archivo");

