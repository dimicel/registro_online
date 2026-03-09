<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$id_nie=$_POST["id_nie"];
$parte=$_POST["parte"];
$subido_por=$_POST["subido_por"];
if ($parte=='P'){
    $parte='A';
    $documento='pasaporte';
}
elseif($parte=='A'){
    $documento='dni_anverso';
}
elseif($parte=='R'){
    $documento='dni_reverso';
}
if(is_uploaded_file($_FILES['dni']['tmp_name'])){
    if (!is_dir("../../../docs/".$id_nie))mkdir("../../../docs/".$id_nie,0777);
    if(!is_dir("../../../docs/".$id_nie."/dni"))mkdir("../../../docs/".$id_nie."/dni",0777);
    $ruta="../../../docs/".$id_nie."/"."dni/". $id_nie."-".$parte.".jpeg";
    if(!move_uploaded_file($_FILES['dni']['tmp_name'], $ruta)) exit("almacenar");
    else{
        if ($mysqli->errno>0) {
            exit("servidor");
        }
        $mysqli->query("insert into fechas_subidas_docs (id_nie, documento, subido_por) values ('$id_nie', '$documento', '$subido_por')");
        if($mysqli->errno>0) exit("error_db");
        else exit("ok");
    }
}
else exit("archivo");





