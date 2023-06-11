<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$id_nie=$_POST["id_nie"];
$anno_curso=$_POST["anno_curso"];

if(is_uploaded_file($_FILES['foto']['tmp_name'])){
    $ruta="../../../docs/fotos/" . $id_nie.".jpeg";
    //if (file_exists("../../../fotos/" . $id_nie.".jpeg")) unlink("../../../fotos/" . $id_nie.".jpeg");
    //if (file_exists("../../../fotos/" . $id_nie.".jpg")) unlink("../../../fotos/" . $id_nie.".jpg");
    if(!move_uploaded_file($_FILES['foto']['tmp_name'], $ruta)) exit("almacenar");
    exit("ok");
}
else exit("archivo");

/*
if ($_FILES["foto"]["error"] == UPLOAD_ERR_OK) {
    $target_dir = "../../../docs/fotos/";
    $new_file_name = $id_nie.".jpeg";
    $target_file = $target_dir . $new_file_name;
    // Verificar si el archivo es una imagen
    $check = getimagesize($_FILES["foto"]["tmp_name"]);
    $img = imagecreatefromjpeg($_FILES["foto"]["tmp_name"]);
    $width = imagesx($img);
    $height = imagesy($img);
    //rota imagen en vertical
    
        // Verificar tamaño del archivo
        if ($_FILES["foto"]["size"] > 65536) {
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
            move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file);
            exit("ok");
        }
} else {
    exit("archivo");
}
*/
