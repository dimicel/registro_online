<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado'] !== "correcto") {
    exit("Acceso denegado");
}

include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno > 0) {
    exit("servidor");
}

// Función para cerrar todo y salir
function cerrar_y_salir($mysqli, $stmt, $mensaje) {
    $stmt->close();
    $mysqli->close();
    exit($mensaje);
}

$departamento = $_POST['config_dpto'];
$email = $_POST['config_email_jd'];
$password = $_POST['config_password_jd'];


if (strlen($password) > 0) {
    //Verifica antes que la contraseña no esté asignadav a otro jefe de departamento
    $consulta=$mysqli->query("select * from departamentos");
    $pass_asignada=false;
    if ($consulta->num_rows>0){
        while($dpto=$consulta->fetch_array(MYSQLI_ASSOC)){
            if(password_verify($contrasena,$dpto['password']) && $dpto['departamento']!=$departamento){
                $pass_asignada=true;
                break;
            }
        }
    }
    ///////////////////////////////////////
    if ($pass_asignada) {
        $sql = "UPDATE departamentos SET email_jd = ? WHERE departamento = ?";
        $stmt->bind_param('ss', $email, $departamento);
    } 
    else {
        $sql = "UPDATE departamentos SET email_jd = ?, password = ? WHERE departamento = ?";
        $pass = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bind_param('sss', $email, $pass, $departamento);
    }
    $stmt = $mysqli->prepare($sql);
    
} else {
    $sql = "UPDATE departamentos SET email_jd = ? WHERE departamento = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ss', $email, $departamento);
}

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        if($pass_asignada) cerrar_y_salir($mysqli, $stmt, "password_duplicada");
        else cerrar_y_salir($mysqli, $stmt, "ok");
    } else {
        cerrar_y_salir($mysqli, $stmt, "database");
    }
} else {
    cerrar_y_salir($mysqli, $stmt, "Error al actualizar el registro: " . $stmt->error);
}




