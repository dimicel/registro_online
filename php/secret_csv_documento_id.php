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
$Datos = 'NIE;ALUMNO;N_DOCUMENTO;ES_PASAPORTE;FECHA_CADUCIDAD;CADUCADO;DIAS_HASTA_CADUCIDAD;PAIS' . PHP_EOL;


$fechaHoy = new DateTime(); 
$fechaHoy->setTime(0, 0, 0); // Normalizamos a las 00:00 para comparar solo días

while ($r = $res->fetch_assoc()) {
    // Saltamos usuarios de prueba
    if (strpos(strtoupper($r["id_nie"]), 'P') === 0) continue;

    // Función auxiliar para limpiar texto y convertir a ISO-8859-1 (Excel prefiere esto en CSV)
    $alumno = ucwords(strtolower($r["apellidos"])) . ", " . ucwords(strtolower($r["nombre"]));

    // 1. Convertimos las fechas a objetos DateTime para comparar con precisión
    $fechaCaducidad = new DateTime($r["fecha_caducidad_id_nif"]);

    // 2. Determinamos si ya ha caducado (anterior o igual a hoy)
    $estaCaducado = ($fechaCaducidad <= $fechaHoy) ? "Si" : "No";

    // 3. Calculamos los días restantes
    $diferencia = $fechaHoy->diff($fechaCaducidad);
    $diasRaw = (int)$diferencia->format("%r%a"); // %r mantiene el signo negativo si ya pasó

    // Si los días son menores o iguales a 0, forzamos que sea 0
    $diasFaltan = ($diasRaw > 0) ? $diasRaw : 0;

    $linea = [
        "\t" . $r["id_nie"],          // Usamos tabulador para evitar formato científico en Excel
        $alumno,
        "\t" . $r["id_nif"],
        ($r["es_pasaporte"] ? "Si" : "No"),
        $r["fecha_caducidad_id_nif"],
        $estaCaducado,                // Nuevo Item 1: ¿Caducado?
        $diasFaltan,                  // Nuevo Item 2: Días restantes (0 si ya pasó)
        $r["pais"]
    ];


    $linea_csv = implode(";", $linea);
    $Datos .= mb_convert_encoding($linea_csv, "ISO-8859-1", "UTF-8") . PHP_EOL;
}

echo $Datos;
exit();
