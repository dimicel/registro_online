<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$id_nie=$_POST["id_nie"];
$subido_por=$_POST["subido_por"];
if(is_uploaded_file($_FILES['foto']['tmp_name'])){
    $ruta="../../../docs/fotos/" . $id_nie.".jpeg";
    if(!move_uploaded_file($_FILES['foto']['tmp_name'], $ruta)) exit("almacenar");
    else{
        if ($mysqli->errno>0) {
            exit("servidor");
        }
        $mysqli->query("insert into fechas_subidas_docs (id_nie, documento, subido_por) values ('$id_nie', 'foto', '$subido_por')");
        if($mysqli->errno>0) exit("error_db");
        else exit("ok");
    }
}
else exit("archivo");

