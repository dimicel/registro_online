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
		$pass=$consulta->fetch_array(MYSQLI_ASSOC);
		$consulta->free();
		if (password_verify($contrasena,$pass['password'])){
			$_SESSION['acceso_logueado']="correcto";
			$_SESSION['id_nie']=$pass['id_nie'];
			$dat["error"]="ok";
			$dat["dia"]=date('w');
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


