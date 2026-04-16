<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");


$id_nie=$_POST["id_nie"];
$curso=$_POST["curso"];
$tipo_doc=$_POST["tipo_doc"];
$resp=Array();

if($tipo_doc=="dni"){
    include("conexion.php");
    if ($mysqli->errno>0){
        $resp["es_pasaporte"]=-1;
    } 
    else{
        $sql="SELECT es_pasaporte FROM usuarios WHERE id_nie='$id_nie'";
        $resultado=$mysqli->query($sql);
        if ($resultado->num_rows>0){
            $fila=$resultado->fetch_assoc();
            $resp["es_pasaporte"]=$fila["es_pasaporte"];
        }
        else $resp["es_pasaporte"]=-1;
    }
}


if (is_file("../docs/fotos/".$id_nie.".jpeg")) $resp["foto"]=1;
else $resp["foto"]=0;

if (is_file("../docs/".$id_nie."/dni"."/".$id_nie."-A.jpeg")) $resp["dni_anverso"]=1;
else $resp["dni_anverso"]=0;

if (is_file("../docs/".$id_nie."/dni"."/".$id_nie."-R.jpeg")) $resp["dni_reverso"]=1;
else $resp["dni_reverso"]=0;

if (is_file("../docs/".$id_nie."/seguro"."/".$curso."/".$id_nie.".jpeg")) $resp["seguro"]=1;
else $resp["seguro"]=0;

if (is_file("../docs/".$id_nie."/nss/nss_".$id_nie.".jpeg")) $resp["num_ss"]=1;
else $resp["num_ss"]=0;

if (is_file("../docs/".$id_nie."/certificado"."/".$curso."/".$id_nie.".pdf")) $resp["certificado"]=1;
else $resp["certificado"]=0;



exit(json_encode($resp));

