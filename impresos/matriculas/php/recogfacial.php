<?php
// Ruta de la imagen que deseas procesar
$image_path = "ruta/a/la/imagen.jpg";

// Cargamos la imagen en OpenCV
$image = cv\imread($image_path);

// Cargamos la biblioteca de reconocimiento facial de OpenCV
$face_detection = cv\CascadeClassifier::load("ruta/a/haarcascade_frontalface_default.xml");

// Detectamos las caras en la imagen
$faces = new cv\Mat();
$face_detection->detectMultiScale($image, $faces);

// Determinamos si se encontró una cara en la imagen
$face_found = ($faces->rows > 0);

// Si encontramos una cara
if ($face_found && count($faces->getRects()) == 1) {
  // Recortamos la imagen alrededor de la cara y la centramos en un cuadro de 640x480
  foreach ($faces->getRects() as $face) {
    $center_x = $face->x + ($face->width / 2);
    $center_y = $face->y + ($face->height / 2);

    $cropped_image = cv\Rect::fromLTRB(
      max($center_x - 320, 0),
      max($center_y - 240, 0),
      min($center_x + 320, $image->cols),
      min($center_y + 240, $image->rows)
    );

    $cropped = $image->crop($cropped_image)->resize(640, 480);
    
    // Guardamos la imagen recortada
    cv\imwrite("ruta/al/nuevo/recorte.jpg", $cropped);
  }
} else {
  // Si no encontramos una cara o encontramos más de una cara
  echo "La imagen no es correcta.";
}
