<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado'] !== "correcto") exit("Acceso denegado");

$tipo = $_POST["tipo"];

if ($tipo == 'logo_centro') {
    $ruta = "../recursos/escudo.jpg";
    $ruta_mini = "../recursos/mini_escudo.jpg";
} elseif ($tipo == 'logo_junta') {
    $ruta = "../recursos/logo_ccm.jpg";
} elseif ($tipo == 'firma_sello') {
    $ruta = "../recursos/sello_firma.jpg";
}

if (is_uploaded_file($_FILES['archivo']['tmp_name'])) {
    if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta)) exit("almacenar");

    // Si es el logo del centro, crear una versión comprimida más pequeña
    if ($tipo == 'logo_centro') {
        // Cargar imagen original
        $img = @imagecreatefromjpeg($ruta);
        if ($img === false) exit("error_imagen");

        // Redimensionar a tamaño más pequeño (por ejemplo 100x100, ajustable)
        $ancho_original = imagesx($img);
        $alto_original = imagesy($img);

        $ancho_nuevo = 300;
        $alto_nuevo = intval(($alto_original / $ancho_original) * $ancho_nuevo);

        $mini_img = imagecreatetruecolor($ancho_nuevo, $alto_nuevo);
        imagecopyresampled($mini_img, $img, 0, 0, 0, 0, $ancho_nuevo, $alto_nuevo, $ancho_original, $alto_original);

        // Guardar imagen comprimida con calidad baja para reducir tamaño
        imagejpeg($mini_img, $ruta_mini, 30);  // calidad del 0 (peor) al 100 (mejor)

        // Liberar memoria
        imagedestroy($img);
        imagedestroy($mini_img);
    }

    exit("ok");
} else {
    exit("archivo");
}


