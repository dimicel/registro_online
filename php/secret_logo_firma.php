<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$tipo=$_POST["tipo"];
if ($tipo=='logo_centro'){
    $ruta="../recursos/escudo.jpg";
}
elseif($tipo=='logo_junta'){
    $ruta="../recursos/logo_ccm.jpg";
}
elseif($tipo=='firma_sello'){
    $ruta="../recursos/sello_firma.jpg";
}

if(is_uploaded_file($_FILES['archivo']['tmp_name'])){
    
    if(!move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta)) exit("almacenar");
    exit("ok");
}
else exit("archivo");

