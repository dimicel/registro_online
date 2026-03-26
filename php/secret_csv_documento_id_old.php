<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado'] !== "correcto") {
    exit("Acceso denegado");
}

include("conexion.php");

$curso = isset($_POST["curso_csv_doc_id"]) ? $_POST["curso_csv_doc_id"] : null;
$formato=$_POST["formato"];

if (!$curso || $mysqli->connect_error) {
    exit("Error: Parámetros insuficientes o fallo de conexión.");
}

$curso_safe = $mysqli->real_escape_string($curso);
$query="
SELECT 
    u.apellidos, 
    u.nombre, 
    u.id_nie, 
    u.fecha_caducidad_id_nif,
    u.pais,
    u.id_nif,
    u.es_pasaporte,
    COALESCE(me.grupo, mb.grupo) AS grupo,
    COALESCE(mf.curso_ciclo, mc.curso_ciclo) AS curso_ciclo,
    COALESCE(mf.ciclo, mc.ciclo) AS ciclo,
    mc.turno AS turno,
    DATE_FORMAT(MAX(CASE WHEN doc.documento = 'dni_anverso' THEN doc.fecha END), '%d/%m/%Y - %H:%i:%s') AS ultima_fecha_anverso_dni,
    DATE_FORMAT(MAX(CASE WHEN doc.documento = 'dni_reverso' THEN doc.fecha END), '%d/%m/%Y - %H:%i:%s') AS ultima_fecha_reverso_dni,
    DATE_FORMAT(MAX(CASE WHEN doc.documento = 'pasaporte' THEN doc.fecha END), '%d/%m/%Y - %H:%i:%s') AS ultima_fecha_pasaporte,
    DATE_FORMAT(MAX(CASE WHEN doc.documento = 'seguro_escolar' THEN doc.fecha END), '%d/%m/%Y - %H:%i:%s') AS ultima_fecha_seguro_escolar
FROM usuarios u
INNER JOIN (
    SELECT id_nie FROM mat_ciclos WHERE curso = '$curso'
    UNION
    SELECT id_nie FROM mat_fpb WHERE curso = '$curso'
    UNION
    SELECT id_nie FROM mat_eso WHERE curso = '$curso'
    UNION
    SELECT id_nie FROM mat_bach WHERE curso = '$curso'
) AS m ON m.id_nie = u.id_nie
LEFT JOIN mat_ciclos mc ON mc.id_nie = u.id_nie AND mc.curso = '$curso'
LEFT JOIN mat_fpb mf    ON mf.id_nie = u.id_nie AND mf.curso = '$curso'
LEFT JOIN mat_eso me    ON me.id_nie = u.id_nie AND me.curso = '$curso'
LEFT JOIN mat_bach mb   ON mb.id_nie = u.id_nie AND mb.curso = '$curso'
LEFT JOIN fechas_subidas_docs doc ON doc.id_nie = u.id_nie
GROUP BY 
    u.id_nie, u.apellidos, u.nombre, u.fecha_caducidad_id_nif, 
    u.pais, u.id_nif, u.es_pasaporte, grupo, curso_ciclo, ciclo, turno
ORDER BY u.apellidos ASC, u.nombre ASC";

$res = $mysqli->query($query);

// Preparación de Cabeceras (Nada debe imprimirse antes de esto)
$Name = 'listado_num_doc_' . $curso . '.csv';
header('Content-Type: text/csv; charset=latin1'); // Cambiado a text/csv para mejor compatibilidad
header('Content-Disposition: attachment; filename="' . $Name . '"');
header('Cache-Control: max-age=0');

if (!$res || $res->num_rows == 0) {
    echo "No hay registros que listar.";
    exit();
}


$fechaHoy = new DateTime(); 
$fechaHoy->setTime(0, 0, 0); // Normalizamos a las 00:00 para comparar solo días

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
fputcsv($output, ["NIE", "ALUMNO", "N_DOCUMENTO", "ES_PASAPORTE", "FECHA_CADUCIDAD", "CADUCADO", "DIAS_HASTA_CADUCIDAD", "PAIS", "CURSO", "TURNO", "SUBIDA_ANVERSO_DNI", "SUBIDA_REVERSO_DNI", "SUBIDA_PASAPORTE", "SUBIDA_SEGURO_ESCOLAR"], ";");
while ($r = $res->fetch_assoc()) {
    // Saltamos usuarios de prueba
    if (strpos(strtoupper($r["id_nie"]), 'P') === 0) continue;

    // Función auxiliar para limpiar texto y convertir a ISO-8859-1 (Excel prefiere esto en CSV)
    $alumno = ucwords(strtolower($r["apellidos"])) . ", " . ucwords(strtolower($r["nombre"]));

    $fechaRaw = $r["fecha_caducidad_id_nif"];
    $esInvalida = (empty($fechaRaw) || $fechaRaw == "0000-00-00" || $fechaRaw == "1970-01-01");
    if ($esInvalida) {
    $fechaCad_ES = '';
    $estaCaducado = 'X';
    $diasFaltan = 0; // Opcional, por si lo usas luego
    } else {
        // 1. Convertimos las fechas a objetos DateTime para comparar con precisión
        $fechaCaducidad = new DateTime($r["fecha_caducidad_id_nif"]);
        $fechaCad_ES = $fechaCaducidad->format('d/m/Y');

        // 2. Determinamos si ya ha caducado (anterior o igual a hoy)
        $estaCaducado = ($fechaCaducidad <= $fechaHoy) ? "Si" : "No";

        // 3. Calculamos los días restantes
        $diferencia = $fechaHoy->diff($fechaCaducidad);
        $diasRaw = (int)$diferencia->format("%r%a"); // %r mantiene el signo negativo si ya pasó

        // Si los días son menores o iguales a 0, forzamos que sea 0
        $diasFaltan = ($diasRaw > 0) ? $diasRaw : 0;
    }
    if ($r['ciclo']) {
        $curso = $r['curso_ciclo'] . " - " . $r['ciclo'];
        $turno= $r['turno'] ?? 'N/A';
    } else {
        $curso = $r['grupo'];
        $turno = 'N/A';
    }

    fputcsv($output, [
        "\t" . $r["id_nie"],          // Usamos tabulador para evitar formato científico en Excel
        $alumno,
        "\t" . $r["id_nif"],
        ($r["es_pasaporte"] ? "Si" : "No"),
        $fechaCad_ES,
        $estaCaducado,                // Nuevo Item 1: ¿Caducado?
        $diasFaltan,                  // Nuevo Item 2: Días restantes (0 si ya pasó)
        $r["pais"],
        $curso,
        $turno,
        $r["ultima_fecha_anverso_dni"]?:'',
        $r["ultima_fecha_reverso_dni"]?:'',
        $r["ultima_fecha_pasaporte"]?:'',
        $r["ultima_fecha_seguro_escolar"]?:''
    ], ';');

}

fclose($output);
exit();
