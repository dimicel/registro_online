<?php
session_start();
header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$respuesta=array();
$mes=0;
$dia=0;

if (!isset($_SESSION['ID'])) $respuesta["error"]="Error_01 - Acceso restringido. No ha introducido las credenciales de acceso en la ventana de login.";
//elseif($_SESSION['ip'] != $_SERVER['HTTP_X_FORWARDED_FOR']) $respuesta["error"]="Error_02 - Acceso restringido. No ha introducido las credenciales de acceso en la ventana de login.";
//elseif ($_SESSION['ip'] != $_SERVER['REMOTE_ADDR']) $respuesta["error"]="Error_02 - Acceso restringido. No ha introducido las credenciales de acceso en la ventana de login.";
elseif ($_SESSION['navegador'] != $_SERVER['HTTP_USER_AGENT']) $respuesta["error"]="Error_03 - Acceso restringido. No ha introducido las credenciales de acceso en la ventana de login.";
elseif ($_SESSION['ID'] != session_id()) $respuesta["error"]="Error_04 - Acceso restringido. No ha introducido las credenciales de acceso en la ventana de login.";
elseif (!isset($_POST['tipo_usu']) || !isset($_SESSION['tipo_usu']) || $_POST['tipo_usu']!=$_SESSION['tipo_usu'] ) {
    if(isset($_POST['tipo_usu']) && isset($_SESSION['tipo_usu'])){
        if($_POST['tipo_usu']=="secretaria" && $_SESSION['tipo_usu']=="jefatura estudios"){
            $respuesta["error"]="ok";
            $respuesta["tipo_usu"]="jefatura estudios";
        }
        elseif($_POST['tipo_usu']=="residencia" && $_SESSION['tipo_usu']=="secretaria"){
            $respuesta["error"]="ok";
            $respuesta["tipo_usu"]="secretaria";
        }
        else{
            $respuesta["error"]="Error_05 - Acceso restringido. No ha introducido las credenciales de acceso en la ventana de login.";
        }
    }
    else {
        $respuesta["error"]="Error_05 - Acceso restringido. No ha introducido las credenciales de acceso en la ventana de login.";
    }
}
elseif($_POST['tipo_usu']=="jefe departamento" && $_SESSION['tipo_usu']=="jefe departamento"){
    $respuesta["error"]="ok";
    $respuesta["tipo_usu"]="jefe departamento";
    $respuesta["departamento"]=$_SESSION['departamento'];
    $respuesta["nombre_ap_jd"]=$_SESSION['nombre_ap_jd'];
    $respuesta["email_jd"]=$_SESSION['email_jd'];
    $respuesta["anno_ini_curso"]=calculaCurso_ini();
    exit (json_encode($respuesta));
}
else $respuesta["error"]="ok";

if ($respuesta["error"]=="ok"){
	$consulta=$mysqli->query("select admin_maestro from config_centro");
	if ($consulta->num_rows>0){
		$admin=$consulta->fetch_array(MYSQLI_ASSOC);
		$respuesta["admin_maestro"]=$admin['admin_maestro'];
		$consulta->free();
	}
	else{
		$dat["error"]="server";
		exit(json_encode($dat));
	}    
    $respuesta["id_nif"]=$_SESSION['id_nif'];
    $respuesta["id_nie"]=$_SESSION['id_nie'];
    $respuesta["nombre"]=$_SESSION['nombre'];
    $respuesta["apellidos"]=$_SESSION['apellidos'];
    $respuesta["email"]=$_SESSION['email'];
    $respuesta["anno_ini_curso"]=calculaCurso_ini();
    $respuesta["anno_ini_curso_docs"]=calculaCurso_ini_docs();
    $respuesta["mes"]=$mes;
    $respuesta["dia"]=$dia;
    if (!isset($_SESSION['primera_carga'])){
        $respuesta['primera_carga']=true;
        $_SESSION['primera_carga']=false;
    }
    else{
        $respuesta['primera_carga']=false;
    }

}

exit (json_encode($respuesta));


function calculaCurso_ini(){
    $GLOBALS['mes'];
    $GLOBALS['mes']=(int)date("n");
    $anno=(int)date("Y");
    $GLOBALS['dia']=(int)date("d");
    if ($GLOBALS['mes']>=7 && $GLOBALS['mes']<=12) 
        return $anno;
    else
        return $anno-1;
}

function calculaCurso_ini_docs(){
    $GLOBALS['mes'];
    $GLOBALS['mes']=(int)date("n");
    $anno=(int)date("Y");
    $GLOBALS['dia']=(int)date("d");
    if ($GLOBALS['mes']>9 && $GLOBALS['mes']<=12) 
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