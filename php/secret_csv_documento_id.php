<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado'] !== "correcto") {
    exit("Acceso denegado");
}

include("conexion.php");

// 1. Validar que recibimos el curso
$curso = isset($_POST["curso_csv_doc_id"]) ? $_POST["curso_csv_doc_id"] : null;

if (!$curso || $mysqli->connect_error) {
    exit("Error: Parámetros insuficientes o fallo de conexión.");
}

// 2. Consulta (He añadido real_escape_string por seguridad básica)
$curso_safe = $mysqli->real_escape_string($curso);
$query = "
    SELECT 
        u.apellidos, 
        u.nombre, 
        u.id_nie, 
        u.fecha_caducidad_id_nif,
        u.pais,
        u.id_nif,
        u.es_pasaporte
    FROM usuarios u
    WHERE (
          EXISTS (
              SELECT 1 
              FROM mat_ciclos mc 
              WHERE mc.id_nie COLLATE utf8mb3_general_ci = u.id_nie COLLATE utf8mb3_general_ci
                AND mc.curso = '$curso'
          )
          OR
          EXISTS (
              SELECT 1 
              FROM mat_fpb mf 
              WHERE mf.id_nie COLLATE utf8mb3_general_ci = u.id_nie COLLATE utf8mb3_general_ci
                AND mf.curso = '$curso'
          )
          OR
          EXISTS (
              SELECT 1 
              FROM mat_eso me 
              WHERE me.id_nie COLLATE utf8mb3_general_ci = u.id_nie COLLATE utf8mb3_general_ci
                AND me.curso = '$curso'
          )
          OR
          EXISTS (
              SELECT 1 
              FROM mat_bach mb 
              WHERE mb.id_nie COLLATE utf8mb3_general_ci = u.id_nie COLLATE utf8mb3_general_ci
                AND mb.curso = '$curso'
          )
      )
    ORDER BY u.apellidos ASC, u.nombre ASC";

$res = $mysqli->query($query);

if (!$res || $res->num_rows == 0) {
    exit("No hay registros que listar.");
}

// 3. Preparación de Cabeceras (Nada debe imprimirse antes de esto)
$Name = 'listado_num_doc_' . $curso . '.csv';
header('Content-Type: text/csv; charset=latin1'); // Cambiado a text/csv para mejor compatibilidad
header('Content-Disposition: attachment; filename="' . $Name . '"');
header('Cache-Control: max-age=0');

// 4. Construcción del contenido
$Datos = 'NIE;ALUMNO;N_DOCUMENTO;ES_PASAPORTE;FECHA_CADUCIDAD;PAIS' . PHP_EOL;

while ($r = $res->fetch_assoc()) {
    // Saltamos usuarios de prueba
    if (str_starts_with(strtoupper($r["id_nie"]), 'P')) continue;

    // Función auxiliar para limpiar texto y convertir a ISO-8859-1 (Excel prefiere esto en CSV)
    $alumno = ucwords(strtolower($r["apellidos"])) . ", " . ucwords(strtolower($r["nombre"]));
    
    // Usamos mb_convert_encoding en lugar de utf8_decode por compatibilidad con PHP 8.x
    $linea = [
        "'" . $r["id_nie"] . "'",
        $alumno,
        "\t" . $r["id_nif"],
        ($r["es_pasaporte"] ? "Si" : "No"),
        $r["fecha_caducidad_id_nif"],
        $r["pais"]
    ];

    $linea_csv = implode(";", $linea);
    $Datos .= mb_convert_encoding($linea_csv, "ISO-8859-1", "UTF-8") . PHP_EOL;
}

echo $Datos;
exit();
