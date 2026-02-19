<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado'] !== "correcto") {
    exit("Acceso denegado");
}

include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->connect_errno) {
    exit("server_error");
}

$mysqli->set_charset("utf8");

// --- RECOGIDA DE DATOS ---
$id_nie = $_POST["dat_idnie"] ?? '';
$email_recuperacion = $_POST["mod_email"] ?? '';
$nif = $_POST["mod_nif"] ?? '';
if (isset($_POST["dat_usuario"])) $usuario=$_POST["dat_usuario"];
else $usuario="alumno";
// Formateo de fechas (asumiendo formato DD/MM/YYYY en el POST)
$nif_fecha_caducidad = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['mod_fecha_caducidad'])));
$fecha_nac = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['dat_fecha_nac'])));

$pais = $_POST["mod_pais"] ?? '';
$es_pasaporte = isset($_POST["mod_es_pasaporte"]) ? 1 : 0;
$nombre = $_POST["mod_nombre"] ?? '';
$apellidos = $_POST["mod_apellidos"] ?? '';
$sexo = $_POST["dat_sexo"] ?? '';
$telefono = $_POST["dat_telefono"] ?? '';
$email = $_POST["dat_email"] ?? '';
$direccion = $_POST["dat_direccion"] ?? '';
$cp = $_POST["dat_cp"] ?? '';
$localidad = $_POST["dat_localidad"] ?? '';
$provincia = $_POST["dat_provincia"] ?? '';
$tutor1 = $_POST["dat_tutor1"] ?? '';
$telef_tut1 = $_POST["dat_telef_tut1"] ?? '';
$email_tut1 = $_POST["dat_email_tut1"] ?? '';
$tutor2 = $_POST["dat_tutor2"] ?? '';
$telef_tut2 = $_POST["dat_telef_tut2"] ?? '';
$email_tut2 = $_POST["dat_email_tut2"] ?? '';
$nss = trim($_POST["dat_nss"] ?? '');
$fecha_cambio_nuss = date('Y-m-d');

// --- INICIO DE LA TRANSACCIÓN ---
$mysqli->begin_transaction();

try {
    // 1. Verificar si existe en usuarios_dat (Corregido el "form" por "from")
    $stmtCheck = $mysqli->prepare("SELECT id_nie FROM usuarios_dat WHERE id_nie = ?");
    $stmtCheck->bind_param("s", $id_nie);
    $stmtCheck->execute();
    $res = $stmtCheck->get_result();

    if ($res->num_rows == 0) {
        $stmtIns = $mysqli->prepare("INSERT INTO usuarios_dat (id_nie) VALUES (?)");
        $stmtIns->bind_param("s", $id_nie);
        $stmtIns->execute();
    }

    // 2. Consulta 1: Tabla 'usuarios'
    $sql1 = "UPDATE usuarios SET nombre=?, apellidos=?, id_nif=?, fecha_caducidad_id_nif=?, pais=?, es_pasaporte=?, email=? WHERE id_nie=?";
    $stmt1 = $mysqli->prepare($sql1);
    $stmt1->bind_param("sssssiss", $nombre, $apellidos, $nif, $nif_fecha_caducidad, $pais, $es_pasaporte, $email_recuperacion, $id_nie);
    
    if (!$stmt1->execute()) throw new Exception("Error en Consulta 1: " . $stmt1->error);

    // 3. Consulta 2: Tabla 'usuarios_dat'
    // Construcción dinámica para el NSS
    if (strlen($nss) > 0) {
        $sql2 = "UPDATE usuarios_dat SET sexo=?, fecha_nac=?, telef_alumno=?, email=?, direccion=?, cp=?, localidad=?, provincia=?, tutor1=?, email_tutor1=?, tlf_tutor1=?, tutor2=?, email_tutor2=?, tlf_tutor2=?, nss=?, fecha_cambio_nss=? WHERE id_nie=?";
        $stmt2 = $mysqli->prepare($sql2);
        $stmt2->bind_param("sssssssssssssssss", $sexo, $fecha_nac, $telefono, $email, $direccion, $cp, $localidad, $provincia, $tutor1, $email_tut1, $telef_tut1, $tutor2, $email_tut2, $telef_tut2, $nss, $fecha_cambio_nuss, $id_nie);
    } else {
        $sql2 = "UPDATE usuarios_dat SET sexo=?, fecha_nac=?, telef_alumno=?, email=?, direccion=?, cp=?, localidad=?, provincia=?, tutor1=?, email_tutor1=?, tlf_tutor1=?, tutor2=?, email_tutor2=?, tlf_tutor2=? WHERE id_nie=?";
        $stmt2 = $mysqli->prepare($sql2);
        $stmt2->bind_param("sssssssssssssss", $sexo, $fecha_nac, $telefono, $email, $direccion, $cp, $localidad, $provincia, $tutor1, $email_tut1, $telef_tut1, $tutor2, $email_tut2, $telef_tut2, $id_nie);
    }

    if (!$stmt2->execute()) throw new Exception("Error en Consulta 2: " . $stmt2->error);

    // Si todo ha ido bien, confirmamos los cambios
    $mysqli->commit();
    echo "ok";

} catch (Exception $e) {
    // Si algo falla, deshacemos todo lo anterior
    $mysqli->rollback();
    exit("Error en la actualización: " . $e->getMessage());
}

$mysqli->close();
/*session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) {
    exit("server");
}
$mysqli->set_charset("utf8");
if (isset($_POST["dat_usuario"])) $usuario=$_POST["dat_usuario"];
else $usuario="alumno";
$id_nie=$_POST["dat_idnie"];
$email_recuperacion=$_POST["mod_email"];
$nif=$_POST["mod_nif"];
$nif_fecha_caducidad=substr($_POST['mod_fecha_caducidad'],6,4).'/'.substr($_POST['mod_fecha_caducidad'],3,2).'/'.substr($_POST['mod_fecha_caducidad'],0,2);
$nif_fecha_caducidad=date('Y-m-d',strtotime($nif_fecha_caducidad));
$pais=$_POST["mod_pais"];
$es_pasaporte=isset($_POST["mod_es_pasaporte"])?1:0;
$nombre=$_POST["mod_nombre"];
$apellidos=$_POST["mod_apellidos"]; 
$sexo=$_POST["dat_sexo"];
$fecha_nac=substr($_POST['dat_fecha_nac'],6,4).'/'.substr($_POST['dat_fecha_nac'],3,2).'/'.substr($_POST['dat_fecha_nac'],0,2);
$fecha_nac=date('Y-m-d',strtotime($fecha_nac));
$telefono=$_POST["dat_telefono"];
$email=$_POST["dat_email"];
$direccion=$_POST["dat_direccion"];
$cp=$_POST["dat_cp"];
$localidad=$_POST["dat_localidad"];
$provincia=$_POST["dat_provincia"];
$tutor1=$_POST["dat_tutor1"]; 
$telef_tut1=$_POST["dat_telef_tut1"];
$email_tut1=$_POST["dat_email_tut1"];
$tutor2=$_POST["dat_tutor2"];
$telef_tut2=$_POST["dat_telef_tut2"];
$email_tut2=$_POST["dat_email_tut2"];
$nss=trim($_POST["dat_nss"]);
$fecha_cambio_nuss=date('Y-m-d');

$checkusu=$mysqli->query("select * form usuarios_dat where id_nie='$id_nie'");
if($checkusu->num_rows==0){
    $mysqli->query("insert into usuarios_dat (id_nie) values ('$id_nie')");
}

$consulta1="update usuarios set nombre='$nombre',apellidos='$apellidos',id_nif='$nif',fecha_caducidad_id_nif='$nif_fecha_caducidad',pais='$pais',es_pasaporte='$es_pasaporte',email='$email_recuperacion' where id_nie='$id_nie'";

$consulta2="update usuarios_dat set sexo='$sexo',
            fecha_nac='$fecha_nac',
            telef_alumno='$telefono',
            email='$email',
            direccion='$direccion',
            cp='$cp',
            localidad='$localidad',
            provincia='$provincia',
            tutor1='$tutor1',
            email_tutor1='$email_tut1',
            tlf_tutor1='$telef_tut1',
            tutor2='$tutor2',
            email_tutor2='$email_tut2',
            tlf_tutor2='$telef_tut2'";
if(strlen($nss)>0){
    $consulta2.= ",nss='$nss',fecha_cambio_nss='$fecha_cambio_nuss' ";
}
$consulta2.= " where id_nie='$id_nie'";

if(!$mysqli->query($consulta1)) exit("Fallo consulta1:".$mysqli->error); 
if(!$mysqli->query($consulta2)) exit("Fallo consulta2:".$mysqli->error);
exit("ok");

$mysqli->close();
*/