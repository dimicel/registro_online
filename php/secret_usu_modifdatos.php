<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");
    
if ($mysqli->errno>0) {
    exit("server");
}

$cont_ok=0;
$cont_error=0;

$id_nie=$_POST["dat_idnie"];
$nombre=$_POST["mod_nombre"];
$apellidos=$_POST["mod_apellidos"];
$email=$_POST["mod_email"];
$id_nif=$_POST["mod_nif"];

$tablas = $mysqli->query("SHOW TABLES FROM ulaboral_imp_sec_online");
if ($tablas){
    while ($fila = $tablas->fetch_row()) {
        $c=$mysqli->query("show columns from $fila[0] like 'apellidos'");
        //if ($fila[0]!="revision_examen" && $fila[0]!="revision_calificacion" && $fila[0]!="prematricula" && $fila[0]!="matricula"){
        if($c->num_rows>0){ 
            $consulta="UPDATE $fila[0] SET nombre='$nombre', apellidos='$apellidos', email='$email', id_nif='$id_nif' where id_nie='$id_nie'";
            $mysqli->query($consulta);
            if ($mysqli->errno>0) {
                $cont_error++;
            }
        }
    }
}
if ($cont_error>0) exit("fallo");
else exit("ok");



