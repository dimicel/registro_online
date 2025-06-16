<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
$error="";
$Datos="";

include("conexion.php");
if ($mysqli->errno>0) $error="Error en servidor.";

$curso=$_POST["comedor_curso"];
$mes=$_POST["mes_informe"];
$anno_1=$substr($curso, 0, 4);
$anno_2=$substr($curso, -4);
$array_meses=array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
$array_dias_mes=array(31,29,31,30,31,30,31,31,30,31,30,31);
$mes_anno="";
$fecha_inicio="";
$fecha_fin="";
if ((int)$mes>=7 && (int)$mes<=12) {
    $mes_anno=$array_meses[(int)$mes-1]."/".$anno_1;
    $fecha_inicio = $anno_1."-".str_pad($mes, 2, "0", STR_PAD_LEFT)."-01";
    $fecha_fin = $anno_1."-".str_pad($mes, 2, "0", STR_PAD_LEFT)."-".$array_dias_mes[(int)$mes-1];
}
elseif((int)$mes>=1 && (int)$mes<=6) {
    $mes_anno=$array_meses[(int)$mes-1]."/".$anno_2;
    $fecha_inicio = $anno_2."-".str_pad($mes, 2, "0", STR_PAD_LEFT)."-01";
    $fecha_fin = $anno_2."-".str_pad($mes, 2, "0", STR_PAD_LEFT)."-".$array_dias_mes[(int)$mes-1];
}

$Name = 'informe_no_asistencia_comedor_'.$mes_anno.'.csv';
$FileName = "./$Name";
$Datos="INFORME FALTAS DE ASISTENCIA AL COMEDOR NO COMUNICADAS ".$mes_anno.PHP_EOL;
$Datos.='NIE;APELLIDOS;NOMBRE;CURSO_ACTUAL;FECHA'.PHP_EOL;

header('Expires: 0');
header('Cache-control: private');
header('Content-Type: application/x-octet-stream;charset=utf-8'); // Archivo de Excel
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Content-Description: File Transfer');
header('Last-Modified: '.date('D, d M Y H:i:s'));
header('Content-Disposition: attachment; filename="'.$Name.'"');
header("Content-Transfer-Encoding: binary");


// Consulta para obtener los registros de no asistencia no comunicada
$sql = "
    SELECT r.id_nie, r.apellidos, r.nombre, rc.fecha_comedor
    FROM residentes r
    INNER JOIN residentes_comedor rc ON r.id_nie = rc.id_nie
    WHERE rc.fecha_comedor BETWEEN ? AND ?
      AND rc.desayuno = 0
      AND rc.comida = 0
      AND rc.cena = 0
      AND NOT EXISTS (
          SELECT 1
          FROM residentes_comedor rc2
          WHERE rc2.id_nie = rc.id_nie
            AND rc2.fechas_no_comedor = rc.fecha_comedor
      )
    ORDER BY r.apellidos, r.nombre, rc.fecha_comedor
";

$stmt = $mysqli->prepare($sql);
if ($stmt === false) {
    exit("Error en la preparación de la consulta.");
}

$stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $linea = [
        $row['id_nie'],
        $row['apellidos'],
        $row['nombre'],
        $curso,
        $row['fecha_comedor']
    ];
    $Datos .= implode(';', $linea) . PHP_EOL;
}

$stmt->close();
$mysqli->close();

echo $Datos;
exit;
