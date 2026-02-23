<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado'] !== "correcto") {
    exit("Acceso denegado");
}

include("conexion.php");

// Cabeceras de control de caché y tipo
header("Content-Type: text/html;charset=utf-8");
header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if ($mysqli->connect_errno) {
    exit("server");
}

$mysqli->set_charset("utf8");

// --- 1. PROCESAMIENTO DE FECHA ---
// Recibimos dd/mm/yyyy y lo convertimos a Y-m-d para MySQL
$fecha_raw = $_POST["nu_doc_fecha_cad"];
$dateObj = DateTime::createFromFormat('d/m/Y', $fecha_raw);

if ($dateObj) {
    $fecha_caducidad_id_nif = $dateObj->format('Y-m-d');
} else {
    // Si la fecha viene vacía o mal formato, puedes decidir si guardar NULL o error
    $fecha_caducidad_id_nif = null; 
}

// --- 2. RECOGIDA DE DATOS ---
$nie          = $_POST["nu_nie"] ?? '';
$nif          = $_POST["nu_nif"] ?? '';
$es_pasaporte = ($_POST["nu_pasaporte"] == "true" && strlen(trim($nif)) > 0) ? 1 : 0;
$pais         = $_POST["nu_nacionalidad"] ?? '';
$nombre       = $_POST["nu_nombre"] ?? '';
$apellidos    = $_POST["nu_apellidos"] ?? '';
$email        = $_POST["nu_email"] ?? '';
$password     = $_POST["nu_password"] ?? '';
$pass_hashed  = password_hash($password, PASSWORD_BCRYPT);

// --- 3. CONSULTA PREPARADA: VERIFICACIÓN ---
$stmt_check = $mysqli->prepare("SELECT id_nie FROM usuarios WHERE id_nie = ? AND no_ha_entrado = 0");
$stmt_check->bind_param("s", $nie);
$stmt_check->execute();
$res_check = $stmt_check->get_result();

if ($res_check->num_rows > 0) {
    $stmt_check->close();
    exit("registrado");
}
$stmt_check->close();

// --- 4. CONSULTA PREPARADA: UPDATE ---
$sql_update = "UPDATE usuarios SET 
                id_nif = ?, 
                password = ?, 
                fecha_caducidad_id_nif = ?, 
                es_pasaporte = ?, 
                pais = ?, 
                nombre = ?, 
                apellidos = ?, 
                email = ?, 
                no_ha_entrado = 0 
               WHERE id_nie = ?";

$stmt_upd = $mysqli->prepare($sql_update);

// "sssisssss" indica los tipos de datos: s=string, i=integer
$stmt_upd->bind_param("sssisssss", 
    $nif, 
    $pass_hashed, 
    $fecha_caducidad_id_nif, 
    $es_pasaporte, 
    $pais, 
    $nombre, 
    $apellidos, 
    $email, 
    $nie
);

if ($stmt_upd->execute()) {
    $stmt_upd->close();
    $mysqli->close();
    
    if (!is_dir("../docs/".$nie)) {
        mkdir("../docs/".$nie, 0777, true); // true para crear carpetas anidadas si hiciera falta
    }
    exit("ok");
} else {
    $stmt_upd->close();
    $mysqli->close();
    exit("fallo_alta");
}