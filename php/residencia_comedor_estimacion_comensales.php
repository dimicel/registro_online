<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
require_once 'conexion.php';

if ($mysqli->errno>0) {
    exit("server");
}


function getSemanaFechas() {
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

    $viernes = clone $lunes;
    $viernes->modify('+4 days');

    return ['lunes' => $lunes->format('Y-m-d'), 'viernes' => $viernes->format('Y-m-d')];
}

$fechas = getSemanaFechas();
$lunes = $fechas['lunes'];
$viernes = $fechas['viernes'];

$sql = "
WITH fechas_semana AS (
  SELECT ? AS fecha
  UNION ALL SELECT DATE_ADD(fecha, INTERVAL 1 DAY) FROM fechas_semana WHERE fecha < ?
),
residentes_activos AS (
  SELECT COUNT(*) AS total_residentes FROM residentes WHERE baja = 0
),
ausencias_dia AS (
  SELECT
    rc.fecha_no_comedor,
    COUNT(DISTINCT rc.id_nie) AS ausentes
  FROM residentes_comedor rc
  INNER JOIN residentes r ON rc.id_nie = r.id_nie
  WHERE r.baja = 0
    AND rc.fecha_no_comedor BETWEEN ? AND ?
  GROUP BY rc.fecha_no_comedor
)
SELECT 
  fs.fecha,
  DAYNAME(fs.fecha) AS dia_semana,
  ra.total_residentes - COALESCE(ad.ausentes, 0) AS num_comensales
FROM fechas_semana fs
CROSS JOIN residentes_activos ra
LEFT JOIN ausencias_dia ad ON fs.fecha = ad.fecha_no_comedor
ORDER BY fs.fecha
";

if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("ssss", $lunes, $viernes, $lunes, $viernes);
    $stmt->execute();
    $result = $stmt->get_result();

    $dias_map = [
        'Monday' => 'Lun',
        'Tuesday' => 'Mar',
        'Wednesday' => 'Mier',
        'Thursday' => 'Jue',
        'Friday' => 'Vie'
    ];

    $salida = [];
    while ($row = $result->fetch_assoc()) {
        $dia_eng = $row['dia_semana'];
        $dia_esp = $dias_map[$dia_eng] ?? $dia_eng;
        $num = (int)$row['num_comensales'];
        $salida[] = "$dia_esp: $num";
    }
    $stmt->close();

    echo implode('; ', $salida);
} else {
    echo "Error en la consulta: " . $mysqli->error;
}

$mysqli->close();