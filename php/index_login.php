<?php
include("conexion.php");
include("funciones.php");
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
	$usuario_mayus=strtoupper($usuario);
	$contrasena=$_POST['password'];
	$mysqli->set_charset("utf8");

	$consulta=$mysqli->query("select * from usuarios_admin where id_nie='$usuario'");
	if ($consulta->num_rows>0){
		$admin=$consulta->fetch_array(MYSQLI_ASSOC);
		if ($admin['nivel']!='jefe_dpto'){
			if (password_verify($contrasena,$admin['password'])){
				$_SESSION['acceso_logueado']="correcto";
				$_SESSION['tipo_usu']=$admin["nivel"];
				$_SESSION['id_nie']=$admin["id_nie"];
				$_SESSION['id_nif']="";
				$_SESSION['nombre']=$admin["nombre"];
				$_SESSION['apellidos']="";
				$_SESSION['email']="";
				$dat["pagina"]= $admin["pagina"]."?q=".time();
				$dat["error"]="ok";
				$consulta->free();
				exit(json_encode($dat));
			}
			else{
				$dat["error"]="password";
				exit(json_encode($dat));
			}
		}
	}
	$consulta->free();

	$consulta=$mysqli->query("select * from departamentos where id_nie='$usuario_mayus'");
	if ($consulta->num_rows>0){
		$dat["error"]="password";
		while($dpto=$consulta->fetch_array(MYSQLI_ASSOC)){
			if(password_verify($contrasena,$dpto['password'])){
				$_SESSION['acceso_logueado']="correcto";
				$_SESSION['tipo_usu']="jefe departamento";
				$_SESSION['departamento']=$dpto['departamento'];
				$_SESSION['nombre_ap_jd']=$dpto['nombre_ap_jd'];
				$_SESSION['email_jd']=$dpto['email_jd'];
				$dat["error"]="ok";
				$dat["pagina"]= "departamento.php?q=".time();
			}
		}
		exit(json_encode($dat));
	}
	$consulta->free();
	
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
			$_SESSION['id_nif']=$pass['id_nif'];
			$_SESSION['nombre']=$pass['nombre'];
			$_SESSION['apellidos']=$pass['apellidos'];
			$_SESSION['email']=$pass['email'];
			if ($pass['no_ha_entrado']){
				$dat["error"]="primera_vez";
				exit(json_encode($dat));
			} 
			$_SESSION['id_nie']=$pass['id_nie'];
			$_SESSION['anno_ini_curso']=calculaCurso_ini();
			$_SESSION['tipo_usu']="usuario";
			$dat["pagina"]= "usuario.php?q=".time();
			$dat["error"]="ok";
			exit(json_encode($dat));
		}
		else{
			$dat["error"]="passwor";
			exit(json_encode($dat));
		}
	}
	else{
		$consulta->free();
		$dat["error"]="nousu";
		exit(json_encode($dat));
	}
}





