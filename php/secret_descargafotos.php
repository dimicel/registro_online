<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");

if ($_POST["usuario"]!="secretaria") exit("Acceso denegado");
$nom_arch="fotos_alumnos".time().".zip";
$zip= new ZipArchive();
if ($zip->open($nom_arch, ZIPARCHIVE::CREATE) === true) {
    $directorio=opendir("../docs/fotos/");
    while(($foto=readdir($directorio))!==false){
        if ($foto!=="." && $foto!==".." && is_file("../docs/fotos/".$foto)){
            //if (substr($foto,-3)=="jpg" || substr($foto,-4)=="jpeg"){
                //$zip->addFile("../docs/fotos/". $foto, $foto);
                $zip->addFile("../docs/fotos/". $foto);
                //$content = file_get_contents("../docs/fotos/".$foto);
                //$zip->addFromString(pathinfo ( "../docs/fotos/".$foto, PATHINFO_BASENAME), $content);
            //}
        }
    }
    $zip->close();
    //rename("fotos_alumnos.zip", "../fotos/fotos_alumnos.zip");
    //header('Expires: 0');
    //header('Cache-control: private');
    //header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    //header('Content-Description: File Transfer');
    //header('Last-Modified: '.date('D, d M Y H:i:s'));
    //header('Content-Type: application/zip'); 
    header('Content-Type: application/octet-stream');
    header("Content-Transfer-Encoding: Binary");
    header('Content-disposition: attachment; filename='.$nom_arch);
    header('Content-Length: ' . filesize($nom_arch));
    //ob_end_flush();
    readfile($nom_arch);
    unlink($nom_arch);
}


