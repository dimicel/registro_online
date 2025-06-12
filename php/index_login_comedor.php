<?php
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");
session_start();
session_regenerate_id();
header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$_SESSION['ip'] = getRealIPAddr();
$_SESSION['navegador'] = $_SERVER['HTTP_USER_AGENT'];
$_SESSION['ID'] = session_id();
$_SESSION['ultimaactividad'] = $_SERVER['REQUEST_TIME'];


$dat=array("error"=>'');
if (!isset($_POST["id_nie"])) exit("Acceso denegado");	
if ($mysqli->errno>0) {
	$dat["error"]="server";
    exit("json_encode($dat)");
}
else {
	$usuario=$_POST['id_nie']; 
	$contrasena=$_POST['pass'];

	$consulta=$mysqli->query("select * from usuarios where id_nie='$usuario'");
	if ($consulta->num_rows>0){
		$hoy = date('Y-m-d');
		$dia_semana = date('w'); // 0=domingo, 1=lunes, ..., 6=sábado

		// Calcular días hasta el próximo lunes
		$dias_hasta_lunes = (8 - $dia_semana) % 7;
		if ($dias_hasta_lunes == 0) $dias_hasta_lunes = 7; // Si hoy es lunes, ir al siguiente lunes

		$fechas = array();
		for ($i = 0; $i < 5; $i++) {
			$fecha = strtotime("+".($dias_hasta_lunes + $i)." days");
			$fechas[] = array(
				'dia' => date('d', $fecha),
				'mes' => date('m', $fecha),
				'anio' => date('Y', $fecha),
				'fecha' => date('Y-m-d', $fecha)
			);
		}

		$pass=$consulta->fetch_array(MYSQLI_ASSOC);
		$consulta->free();
		if (password_verify($contrasena,$pass['password'])){
			$_SESSION['acceso_logueado']="correcto";
			$_SESSION['id_nie']=$pass['id_nie'];
			$dat["error"]="ok";
			$dat["dia"]=(int)date('w');
			$dat["fechas"]=$fechas;
			exit(json_encode($dat));
		}
		else{
			$dat["error"]="password";
			exit(json_encode($dat));
		}
	}
	else{
		$consulta->free();
		$dat["error"]="nousu";
		exit(json_encode($dat));
	}
}




function getRealIPAddr()
   {
       //check ip from share internet
       if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
       {
           $ip = $_SERVER['HTTP_CLIENT_IP'];
       }
       //to check ip is pass from proxy
       elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))  
       {
           $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
       }
       else
       {
           $ip = $_SERVER['REMOTE_ADDR'];
       }

       return $ip;
   }


