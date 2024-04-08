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

$dat=array("error"=>'',"pagina"=>'');
if (!isset($_POST["usuario"])) exit("Acceso denegado");	
if ($mysqli->errno>0) {
	$dat["error"]="server";
    exit(json_encode($dat));
}
else {
	$usuario=$_POST['usuario']; 
	$contrasena=$_POST['password'];

	$mysqli->set_charset("utf8");

	$consulta=$mysqli->query("select * from usuarios where id_nie='$usuario'");
	if ($consulta->num_rows>0){
		$pass=$consulta->fetch_array(MYSQLI_ASSOC);
		$consulta->free();
		if (password_verify($contrasena,$pass['password'])){
			$_SESSION['acceso_logueado']="correcto";
			if($pass['habilitado']==0){
				$dat["error"]="inhabilitado";
				exit(json_encode($dat));
			}
			if ($pass['no_ha_entrado']){
				$dat["error"]="primera_vez";
				$dat["datos"]["id_nif"]=$pass['id_nif'];
				$dat["datos"]["nombre"]=$pass['nombre'];
				$dat["datos"]["apellidos"]=$pass['apellidos'];
				$dat["datos"]["email"]=$pass['email'];
				exit(json_encode($dat));
			} 
			$_SESSION['id_nie']=$pass['id_nie'];
			$_SESSION['id_nif']=$pass['id_nif'];
			$_SESSION['nombre']=$pass['nombre'];
			$_SESSION['apellidos']=$pass['apellidos'];
			$_SESSION['email']=$pass['email'];
			$_SESSION['anno_ini_curso']=calculaCurso_ini();
			if ($pass['id_nie']=="S4500175G"){
				$_SESSION['tipo_usu']="secretaria";
				$dat["error"]="ok";
				$dat["pagina"]= "secretaria.php";
				exit(json_encode($dat));
			} 
			else if ($pass['id_nie']=="S4500175GJEF"){
				$_SESSION['tipo_usu']="jefatura estudios";
				$dat["error"]="ok";
				$dat["pagina"]= "secretaria.php";
				exit(json_encode($dat));
			} 
			else if ($pass['id_nie']=="S4500175GRES"){
				$_SESSION['tipo_usu']="residencia";
				$dat["error"]="ok";
				$dat["pagina"]= "residencia.php";
				exit(json_encode($dat));
			}
			else{
				$_SESSION['tipo_usu']="usuario";
				$dat["error"]="ok";
				$dat["pagina"]= "usuario.php";
				exit(json_encode($dat));
			} 
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


function calculaCurso_ini(){
    $mes=(int)date("n");
    $anno=(int)date("Y");
    if ($mes>=7 && $mes<=12) 
        return $anno;
    else
        return $anno-1;
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


