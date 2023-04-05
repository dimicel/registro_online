<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$id_nie=$_POST["id_nie"];
$anno_curso=$_POST["anno_curso"];
if(is_uploaded_file($_FILES['seguro']['tmp_name'])){
    if (!is_dir("../../../docs/".$id_nie))mkdir("../../../docs/".$id_nie,0777);
    if(!is_dir("../../../docs/".$id_nie."/seguro"))mkdir("../../../docs/".$id_nie."/seguro",0777);
    if(!is_dir("../../../docs/".$id_nie."/seguro"."/".$anno_curso))mkdir("../../../docs/".$id_nie."/seguro"."/".$anno_curso,0777);
    $ruta="../../../docs/".$id_nie."/"."seguro/".$anno_curso."/". $id_nie.".jpeg";
    if(!move_uploaded_file($_FILES['seguro']['tmp_name'], $ruta)) exit("almacenar");
    exit("ok");
}
else exit("archivo");


