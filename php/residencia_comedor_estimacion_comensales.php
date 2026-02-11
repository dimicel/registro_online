<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
require_once 'conexion.php';

if ($mysqli->errno>0) {
    exit("server");
}

$curso=$_POST["curso"]; 

function getSemanaFechasArray() {
    $hoy = new DateTime();
    $diaSemana = (int)$hoy->format('N');

    if ($diaSemana >= 5) { // viernes=5, sábado=6, domingo=7
        $diasParaLunes = 8 - $diaSemana;
        $lunes = clone $hoy;
        $lunes->modify("+$diasParaLunes days");
    } else {
        $lunes = clone $hoy;
        $lunes->modify('-' . ($diaSemana - 1) . ' days');
    }

    $fechas = [];
    for ($i = 0; $i < 5; $i++) {
        $fecha = clone $lunes;
        $fecha->modify("+$i days");
        $fechas[] = $fecha->format('Y-m-d');
    }

    return $fechas;
}

$fechas = getSemanaFechasArray();

$placeholders = implode(',', array_fill(0, count($fechas), '?'));

// Ahora el conteo de residentes activos con curso y baja
$sql_residentes = "SELECT COUNT(*) AS total_residentes FROM residentes WHERE baja = 0 AND curso = ?";

$stmt_residentes = $mysqli->prepare($sql_residentes);
if (!$stmt_residentes) {
    die("Error en prepare residentes: " . $mysqli->error);
}
$stmt_residentes->bind_param('s', $curso);
$stmt_residentes->execute();
$result_residentes = $stmt_residentes->get_result();
$row_residentes = $result_residentes->fetch_assoc();
$total_residentes = (int)$row_residentes['total_residentes'];
$stmt_residentes->close();

// Consulta ausencias para las fechas dadas, solo residentes activos y de ese curso
$sql_ausencias = "
SELECT
    r.curso,
    rc.fecha_no_comedor,
    COUNT(DISTINCT rc.id_nie) AS ausentes
FROM residentes_comedor rc
INNER JOIN residentes r ON rc.id_nie = r.id_nie
WHERE r.baja = 0
  AND r.curso = ?
  AND rc.fecha_no_comedor IN ($placeholders)
GROUP BY rc.fecha_no_comedor
";

$stmt = $mysqli->prepare($sql_ausencias);
if (!$stmt) {
    die("Error en prepare ausencias: " . $mysqli->error);
}

// Bind params para $curso + fechas
// primer param: curso (s) + luego fechas (todos s)
$tipos = 's' . str_repeat('s', count($fechas));
$params = array_merge([$curso], $fechas);

// bind_param no acepta array directo, hay que usar call_user_func_array
// para ello hacemos referencia por referencia
$tmp = [];
foreach ($params as $key => $value) {
    $tmp[$key] = &$params[$key];
}
call_user_func_array([$stmt, 'bind_param'], array_merge([$tipos], $tmp));

$stmt->execute();
$result = $stmt->get_result();

$ausencias = [];
while ($row = $result->fetch_assoc()) {
    $ausencias[$row['fecha_no_comedor']] = (int)$row['ausentes'];
}
$stmt->close();

$dias_map = [
    'Monday' => 'Lun',
    'Tuesday' => 'Mar',
    'Wednesday' => 'Mier',
    'Thursday' => 'Jue',
    'Friday' => 'Vie'
];

$salida = [];
foreach ($fechas as $fecha) {
    $dia_semana_eng = date('l', strtotime($fecha));
    $dia_semana_esp = $dias_map[$dia_semana_eng] ?? $dia_semana_eng;

    $num_comensales = $total_residentes - ($ausencias[$fecha] ?? 0);
    $salida[] = "$dia_semana_esp: $num_comensales";
}

echo implode('; ', $salida);
$mysqli->close();