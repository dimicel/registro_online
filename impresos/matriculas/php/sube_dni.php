<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$id_nie=$_POST["id_nie"];
$parte=$_POST["parte"];
/*if(is_uploaded_file($_FILES['dni']['tmp_name'])){
    if (!is_dir("../../../docs/".$id_nie))mkdir("../../../docs/".$id_nie,0777);
    if(!is_dir("../../../docs/".$id_nie."/dni"))mkdir("../../../docs/".$id_nie."/dni",0777);
    $ruta="../../../docs/".$id_nie."/"."dni/". $id_nie."-".$parte.".jpeg";
    if(!move_uploaded_file($_FILES['dni']['tmp_name'], $ruta)) exit("almacenar");
    exit("ok");
}
else exit("archivo");*/


if ($_FILES["dni"]["error"] == UPLOAD_ERR_OK) {
    if (!is_dir("../../../docs/".$id_nie))mkdir("../../../docs/".$id_nie,0777);
    if(!is_dir("../../../docs/".$id_nie."/dni"))mkdir("../../../docs/".$id_nie."/dni",0777);
    $target_file = "../../../docs/".$id_nie."/"."dni/". $id_nie."-".$parte.".jpeg";//$target_dir . $new_file_name;
    // Verificar si el archivo es una imagen
    $check = getimagesize($_FILES["dni"]["tmp_name"]);
    $img = imagecreatefromjpeg($_FILES["dni"]["tmp_name"]);
    $width = imagesx($img);
    $height = imagesy($img);
    //rota imagen en vertical
    /*if ($width < $height) {
        $img = imagerotate($img, 90, 0);
        $width = imagesx($img);
        $height = imagesy($img);
    }*/
    //if($check !== false) {
        // Verificar tamaño del archivo
        if ($_FILES["dni"]["size"] > 150*1024) {
            // Redimensionar la imagen manteniendo la relación de aspecto
            $aspect_ratio = $width / $height;
            $new_width = min($width, 500); // Límite de ancho
            $new_height = round($new_width / $aspect_ratio);
            $new_img = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($new_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagedestroy($img);
            // Guardar el archivo en la ruta especificada
            imagejpeg($new_img, $target_file);
            imagedestroy($new_img);
            exit("ok");
        } else {
            // Guardar el archivo sin modificar
            move_uploaded_file($_FILES["dni"]["tmp_name"], $target_file);
            exit("ok");
        }
    //} else {
    //    exit("noimagen");
    //}
} else {
    exit("archivo");
}




