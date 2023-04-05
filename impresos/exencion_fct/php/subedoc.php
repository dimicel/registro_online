<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$usuario=$_POST['usuario'];

//comprobamos que sea una petición ajax
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{	
	if(is_uploaded_file($_FILES['fotosel']['tmp_name'])){
		$tmpname=$_FILES['fotosel']['tmp_name'];
		if(move_uploaded_file($tmpname, "../img/".$usuario.".jpg"))echo "ok";
		else echo "almacenar";
	}
	else echo "imagen";
}
?>