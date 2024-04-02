<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
header('Content-Type: text/html; charset=UTF-8');
include("conexion.php");

if ($mysqli->errno>0) exit("server");

if(is_uploaded_file($_FILES['csv']['tmp_name'])){
    array_map('unlink', glob("excel/*.csv"));
    $archivo=basename($_FILES['csv']['name']);
    $ruta="excel/" . $archivo;
    if(!move_uploaded_file($_FILES['csv']['tmp_name'], $ruta)) exit("almacenar");
}
else exit("archivo");


if (file_exists($ruta)) {
    chmod($ruta, 0777);
    $csv=fopen($ruta,'r+');
}
else exit ("noexiste");

if ($csv != false) {
    $fila=0;
    $columnaNIE=0;
    $columnaNombre=-1;
    $columnaApellidos=-1;
    $columnaNIF=-1;
    $fallo_alta=0;
    $err_alta=false;
    $nuevoArray=Array();
    while(($datos = fgetcsv($csv, 5000,",")) !== FALSE){  
        $nuevoArray[]=Array();
        $err_alta=false;
        $err_duplicado=false;
        if ($fila==0){
            if(!in_array("C_NUMESCOLAR",$datos)) exit("formato_archivo");
            for ($c=0; $c < count($datos); $c++){
                $nuevoArray[$fila][]=utf8_decode($datos[$c]);
                if ($datos[$c]=="C_NUMESCOLAR"){
                    $columnaNIE=$c;
                    $nuevoArray[$fila][]=utf8_decode('CONTRASEÑA');
                }
                else if($datos[$c]=="C_NUMIDE")$columnaNIF=$c;
                else if($datos[$c]=="APELLIDOS")$columnaApellidos=$c;
                else if($datos[$c]=="NOMBRE")$columnaNombre=$c;
            }
            $nuevoArray[$fila][]=utf8_decode("ERROR");
            $fila++;
            continue;
        }
        if ($columnaApellidos>-1){
            $nombre=$datos[$columnaNombre];
            $apellidos=$datos[$columnaApellidos];
        }
        else if($columnaNombre>-1 && $columnaApellidos==-1){
            $nomArray=explode(",",$datos[$columnaNombre]);
            $nombre=trim($nomArray[1]);
            $apellidos=trim($nomArray[0]);
        }
        for ($c=0; $c < count($datos); $c++) {
            $nuevoArray[$fila][]=utf8_decode($datos[$c]);
            if ($c==$columnaNIE){
                $pass=password();
                $p=password_hash($pass,PASSWORD_BCRYPT);
                $id_nie=trim($datos[$c]);
                $consulta=$mysqli->query("select * from usuarios where id_nie='$id_nie' and no_ha_entrado=0");
                if ($consulta->num_rows>0){
                    $nuevoArray[$fila][]="Duplicado";
                    $consulta->free();
                    $fallo_alta++;
                    $err_duplicado=true;
                } 
                else if ($consulta->num_rows==0){
                    $nuevoArray[$fila][]=$pass;
                    $consulta->free();
                    $consulta=$mysqli->query("select * from usuarios where id_nie='$datos[$c]' and no_ha_entrado=1");
                    if($consulta->num_rows>0){
                        $_con="update usuarios set password='$p'";
                        if ($columnaNombre>-1){
                            $_con.=", apellidos='$apellidos', nombre='$nombre'";
                        }
                        if ($columnaNIF>-1){
                            $_con.=", id_nif='$datos[$columnaNIF]'";
                        }
                        $mysqli->query($_con . " where id_nie='$datos[$c]'");
                    }
                    else{
                        $_cam="id_nie,password,no_ha_entrado";
                        $_val="'$datos[$c]','$p',1";
                        if($columnaNombre>-1){
                            $_cam.=",apellidos,nombre";
                            $_val.=",'$apellidos','$nombre'";
                        }
                        if($columnaNIF>-1){
                            $_cam.=",id_nif";
                            $_val.=",'$datos[$columnaNIF]'";
                        }
                        $mysqli->query("insert into usuarios (".$_cam.") values (".$_val.")");

                        if(!is_dir("../docs/".$datos[$columnaNIE])) mkdir("../docs/".$datos[$columnaNIE],0777);
                        if (!is_dir("../docs/".$datos[$columnaNIE]."/seguro")) mkdir("../docs/".$datos[$columnaNIE]."/seguro",0777);
                        if (!is_dir("../docs/".$datos[$columnaNIE]."/dni")) mkdir("../docs/".$datos[$columnaNIE]."/dni",0777);
                        if (!is_dir("../docs/".$datos[$columnaNIE]."/certificado_notas")) mkdir("../docs/".$datos[$columnaNIE]."/certificado_notas",0777);
                    
                    }
                    if ($mysqli->errno>0){
                        $fallo_alta++;
                        $err_alta=true;
                    }
                    
                    $consulta->free();
                }
            } 

        }
        
        if ($err_alta)$nuevoArray[$fila][]=utf8_decode("Error al dar de alta");
        else if($err_duplicado) $nuevoArray[$fila][]=utf8_decode("Registro duplicado y habilitado");
        
        $fila++;
    }
    fclose($csv);
    $csv = fopen($ruta, 'w');
    foreach ($nuevoArray as $fields) { 
        fputcsv($csv, $fields); 
    } 
    fclose($csv);
}
else exit("abrir");
exit(strval($fallo_alta));



function password(){
    $mayus="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $minus="abcdefghijklmnopqrstuvwxyz";
    $nums="0123456789";
    $array=array("","","","","","","","");
    $password="";
    $array[0]=substr($mayus,mt_rand(0,strlen("mayus")-1),1);
    $array[1]=substr($minus,mt_rand(0,strlen("minus")-1),1);
    $array[2]=substr($nums,mt_rand(0,strlen("nums")-1),1);
    $array[3]=substr($mayus,mt_rand(0,strlen("mayus")-1),1);
    $array[4]=substr($minus,mt_rand(0,strlen("minus")-1),1);
    $array[5]=substr($nums,mt_rand(0,strlen("nums")-1),1);
    $array[6]=substr($mayus,mt_rand(0,strlen("mayus")-1),1);
    $array[7]=substr($minus,mt_rand(0,strlen("signos")-1),1);
    shuffle($array);
    $password=$array[0].$array[1].$array[2].$array[3].$array[4].$array[5].$array[6].$array[7];
    return $password;
}



