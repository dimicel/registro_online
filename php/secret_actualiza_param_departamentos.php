<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

$data=array();
if ($mysqli->errno>0) {
    exit("servidor");
}

$departamento=$_POST['config_dpto'];
$email=$_POST['config_email_jd'];
$password =$_POST['config_password_jd'];
$pass=password_hash($password,PASSWORD_BCRYPT);

if (strlen($password)>0) $sql = "UPDATE departamentos SET email_jd = ? WHERE departamento=?";
else $sql = "UPDATE departamentos SET email_jd = ?, password=? WHERE departamento=?";

$stmt = $mysqli->prepare($sql);

if (strlen($password)>0) $stmt->bind_param('sss', $email,$pass, $departamento);
else $stmt->bind_param('ss', $email, $departamento);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $stmt->close();
        $mysqli->close();
        exit("ok");
    } else {
        $stmt->close();
        $mysqli->close();
        exit("database");
    }
} else {
    $stmt->close();
    $mysqli->close();
    exit("Error al actualizar el registro: " . $stmt->error);
}



