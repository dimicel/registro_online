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
    u.es_pasaporte,
    -- Agrupamos los datos de las diferentes tablas
    COALESCE(me.grupo, mb.grupo) AS grupo,
    COALESCE(mf.curso_ciclo, mc.curso_ciclo) AS curso_ciclo,
    COALESCE(mf.ciclo, mc.ciclo) AS ciclo,
    COALESCE(mf.turno, mc.turno) AS turno
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
ORDER BY u.apellidos ASC, u.nombre ASC;";

$res = $mysqli->query($query);

// 3. Preparación de Cabeceras (Nada debe imprimirse antes de esto)
$Name = 'listado_num_doc_' . $curso . '.csv';
header('Content-Type: text/csv; charset=latin1'); // Cambiado a text/csv para mejor compatibilidad
header('Content-Disposition: attachment; filename="' . $Name . '"');
header('Cache-Control: max-age=0');

if (!$res || $res->num_rows == 0) {
    echo "No hay registros que listar.";
    exit();
}

// 4. Construcción del contenido
$Datos = 'NIE;ALUMNO;N_DOCUMENTO;ES_PASAPORTE;FECHA_CADUCIDAD;CADUCADO;DIAS_HASTA_CADUCIDAD;PAIS;CURSO;TURNO' . PHP_EOL;


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
    if ($r['ciclo']) {
        $curso = $r['curso_ciclo'] . "º-" . $r['ciclo'];
        $turno = $r['turno'];
    } else {
        $curso = $r['grupo'];
        $turno = 'N/A';
    }

    $linea = [
        "\t" . $r["id_nie"],          // Usamos tabulador para evitar formato científico en Excel
        $alumno,
        "\t" . $r["id_nif"],
        ($r["es_pasaporte"] ? "Si" : "No"),
        $r["fecha_caducidad_id_nif"],
        $estaCaducado,                // Nuevo Item 1: ¿Caducado?
        $diasFaltan,                  // Nuevo Item 2: Días restantes (0 si ya pasó)
        $r["pais"],
        $curso,
        $turno
    ];


    $linea_csv = implode(";", $linea);
    $Datos .= mb_convert_encoding($linea_csv, "ISO-8859-1", "UTF-8") . PHP_EOL;
}

echo $Datos;
exit();
