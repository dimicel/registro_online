
<?php

session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$id_nie = isset($_POST["id_nie"]) ? $_POST["id_nie"] : '';
$anno_curso = isset($_POST["anno_curso"]) ? $_POST["anno_curso"] : '';

if ($id_nie=='' || $anno_curso=='') exit("archivo");

if ($_FILES["foto"]["error"] == UPLOAD_ERR_OK) {
	$target_dir = "../../../docs/fotos/";
    $new_file_name = $id_nie.".jpeg";
    $target_file = $target_dir . $new_file_name;
	

	$hazPorGD=false;

	// Comprobar si la imagen tiene información EXIF
	if (exif_imagetype($_FILES["foto"]["tmp_name"]) == IMAGETYPE_JPEG && function_exists('exif_read_data')) {
	  $exifData = exif_read_data($_FILES["foto"]["tmp_name"]);
	  $orientation = isset($exif['Orientation']) ? $exif['Orientation'] : null;
	  // Comprobar si la información EXIF es correcta
	  if ($exifData !== false) {
		$hazPorGD=false;
	  } else {
		$hazPorGD=true;
	  }
	} else {
	  // La imagen no tiene información EXIF
	  $hazPorGD=true;
	}
	// Carga la imagen
	$image = imagecreatefromjpeg($_FILES["foto"]["tmp_name"]);

	If($hazPorGD){

		// Obtén el ancho y alto de la imagen
		$width = imagesx($image);
		$height = imagesy($image);

		// Determina si la imagen está en modo retrato o paisaje
		if ($height > $width) {
			// La imagen ya está en modo retrato, no se requiere rotación
		} else {
			// La imagen está en modo paisaje, gira 90 grados en sentido antihorario
			$image = imagerotate($image, -90, 0);
		}

		// Guarda la imagen rotada (si se rotó)
		imagejpeg($image, "ruta/de/la/imagen_rotada.jpg");

		// Libera los recursos de la imagen
		imagedestroy($image);
	}
	else{
		

		// Rota la imagen en función de la orientación
		switch ($orientation) {
			case 2:
				$image = imagerotate($image, 180, 0);
				break;
			case 3:
				$image = imagerotate($image, 180, 0);
				break;
			case 4:
				$image = imagerotate($image, 180, 0);
				imageflip($image, IMG_FLIP_VERTICAL);
				break;
			case 5:
				$image = imagerotate($image, -90, 0);
				imageflip($image, IMG_FLIP_VERTICAL);
				break;
			case 6:
				$image = imagerotate($image, -90, 0);
				break;
			case 7:
				$image = imagerotate($image, 90, 0);
				imageflip($image, IMG_FLIP_VERTICAL);
				break;
			case 8:
				$image = imagerotate($image, 90, 0);
				break;
			default:
				break;
		}
	}

	//Reescalado de la imagen
	$max_size_kb = 64;
	$file_size_kb = filesize($_FILES["foto"]["size"] ) / 1024; // Convertir bytes a kilobytes
	if ($file_size_kb > $max_size_kb) {
		$ratio = sqrt($file_size_kb / $max_size_kb);
		$new_width = round($width / $ratio);
		$new_height = round($height / $ratio);
		$new_image = imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		imagedestroy($image);
		imagejpeg($new_image, $target_file, 90);
		imagedestroy($new_image);
	}
	exit ("ok");

} else {
    exit("archivo");
}
