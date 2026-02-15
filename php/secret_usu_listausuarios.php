<?php
// Mantenemos el debug por si acaso
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado'] !== "correcto") exit("Acceso denegado");

include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

$data = array();
if ($mysqli->connect_errno) exit(json_encode(["error" => "server"]));

$pagina = isset($_POST["pagina"]) ? (int)$_POST["pagina"] : 1;
$num_reg_pagina = isset($_POST["num_reg_pagina"]) ? (int)$_POST["num_reg_pagina"] : 10;
$orden_direccion = (isset($_POST["orden_direccion_usu"]) && $_POST["orden_direccion_usu"] == 'DESC') ? 'DESC' : 'ASC';
$buscar = isset($_POST["buscar"]) ? trim($_POST["buscar"]) : "";
$solo_han_entrado = isset($_POST["solo_han_entrado"]) ? $_POST["solo_han_entrado"] : "";
$curso = isset($_POST["curso"]) ? $_POST["curso"] : "Todos";

$condiciones = [];
$params_where = [];
$tipos_where = "";

if ($solo_han_entrado == "Si") $condiciones[] = "u.no_ha_entrado = 0";
elseif ($solo_han_entrado == "No") $condiciones[] = "u.no_ha_entrado = 1";

// 2. Filtro: Curso con corrección de COLLATE
if ($curso != "Todos") {
    if ($curso == "2021-2022") {
        $tablas = ["mat_1bach_c", "mat_1bach_hcs", "mat_1eso", "mat_2eso", "mat_3eso", "mat_4eso", "mat_2bach_c", "mat_2bach_hcs", "mat_2esopmar", "mat_3esodiv", "mat_3esopmar"];
    } else {
        $tablas = ["mat_eso", "mat_bach", "mat_fpb", "mat_ciclos"];
    }

    $subs = [];
    foreach ($tablas as $t) {
        // Forzamos COLLATE para evitar el error de mezcla de caracteres
        $subs[] = "u.id_nie COLLATE utf8_unicode_ci IN (SELECT id_nie COLLATE utf8_unicode_ci FROM $t WHERE curso COLLATE utf8_unicode_ci = ?)";
        $tipos_where .= "s";
        $params_where[] = $curso;
    }
    $condiciones[] = "(" . implode(" OR ", $subs) . ")";
}

// 3. Buscador
if ($buscar != "") {
    $condiciones[] = "(u.apellidos LIKE ? OR u.nombre LIKE ? OR u.id_nie LIKE ?)";
    $termino = "%$buscar%";
    $tipos_where .= "sss";
    array_push($params_where, $termino, $termino, $termino);
}

$where_sql = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

// --- CONSULTA 1: TOTAL ---
$sql_count = "SELECT COUNT(*) AS total FROM usuarios u $where_sql";
$stmt_count = $mysqli->prepare($sql_count);
if (!empty($params_where)) $stmt_count->bind_param($tipos_where, ...$params_where);
$stmt_count->execute();
$data["num_registros"] = $stmt_count->get_result()->fetch_assoc()['total'];
$stmt_count->close();

if ($data["num_registros"] == 0) exit(json_encode(["error" => "sin_registros"]));

// --- CONSULTA 2: DATOS ---
$offset = ($pagina - 1) * $num_reg_pagina;

if ($curso != "Todos") {
    $select_residente = ", IF(r.id_nie IS NULL, 'No', 'Si') as residente";
    // También añadimos COLLATE al JOIN por si acaso
    $join_residente = " LEFT JOIN residentes r ON u.id_nie COLLATE utf8_unicode_ci = r.id_nie COLLATE utf8_unicode_ci AND r.curso COLLATE utf8_unicode_ci = ? ";
    $tipos_final = "s" . $tipos_where . "ii";
    $params_final = array_merge([$curso], $params_where, [$num_reg_pagina, $offset]);
} else {
    $select_residente = ", '-' as residente";
    $join_residente = "";
    $tipos_final = $tipos_where . "ii";
    $params_final = array_merge($params_where, [$num_reg_pagina, $offset]);
}

$consulta = "SELECT u.* $select_residente FROM usuarios u $join_residente $where_sql ORDER BY u.apellidos $orden_direccion LIMIT ? OFFSET ?";

$stmt_data = $mysqli->prepare($consulta);
$stmt_data->bind_param($tipos_final, ...$params_final);
$stmt_data->execute(); // Aquí es donde antes petaba
$res = $stmt_data->get_result();

$data["error"] = "ok";
$data["registros"] = array();
while ($reg = $res->fetch_assoc()) {
    $data["registros"][] = [
        "id_nie"        => $reg["id_nie"],
        "nombre"        => ucwords(strtolower($reg["apellidos"])) . ", " . ucwords(strtolower($reg["nombre"])),
        "email"         => $reg["email"],
        "habilitado"    => $reg["habilitado"],
        "no_ha_entrado" => ($reg["no_ha_entrado"] == 1) ? "NO" : "SI",
        "residente"     => $reg["residente"]
    ];
}

$stmt_data->close();
$mysqli->close();
echo json_encode($data);
/*session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

$data=array();
if ($mysqli->errno>0) {
    $data["error"]="server";
    exit(json_encode($data));
}

$pagina=$_POST["pagina"];
$num_reg_pagina=$_POST["num_reg_pagina"];//Número de registros por página
$orden_direccion=$_POST["orden_direccion_usu"];
$buscar=$_POST["buscar"];
$solo_han_entrado=$_POST["solo_han_entrado"];
$curso=$_POST["curso"];

$filtro_han_entrado="";
if ($solo_han_entrado=="Si") $filtro_han_entrado="WHERE no_ha_entrado=0 ";
elseif ($solo_han_entrado=="No") $filtro_han_entrado="WHERE no_ha_entrado=1 ";

$offset=($pagina-1)*$num_reg_pagina;
if (trim($buscar)==""){
    $consulta="SELECT * FROM usuarios $filtro_han_entrado ORDER BY apellidos $orden_direccion LIMIT $num_reg_pagina OFFSET $offset";
    $sql = "SELECT COUNT(*) AS total FROM usuarios $filtro_han_entrado";
}
else {
    if ($filtro_han_entrado!=""){
        $consulta="SELECT * FROM usuarios $filtro_han_entrado AND (apellidos LIKE '%$buscar%' OR nombre  LIKE '%$buscar%' OR id_nie  LIKE '%$buscar%')  ORDER BY apellidos $orden_direccion LIMIT $num_reg_pagina OFFSET $offset";
        $sql = "SELECT COUNT(*) AS total FROM usuarios $filtro_han_entrado AND (apellidos LIKE '%$buscar%' OR nombre  LIKE '%$buscar%' OR id_nie  LIKE '%$buscar%')";
    }
    else {
        $consulta="SELECT * FROM usuarios WHERE (apellidos LIKE '%$buscar%' OR nombre  LIKE '%$buscar%' OR id_nie  LIKE '%$buscar%') ORDER BY apellidos $orden_direccion LIMIT $num_reg_pagina OFFSET $offset";
        $sql = "SELECT COUNT(*) AS total FROM usuarios WHERE (apellidos LIKE '%$buscar%' OR nombre  LIKE '%$buscar%' OR id_nie  LIKE '%$buscar%')";
    }
}

$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    // Obtener el resultado
    $resultado=$result->fetch_assoc();
    $data["num_registros"] = $resultado['total'];
} else {
    $data["num_registros"] = 0;
}

$res=$mysqli->query($consulta);

if ($res->num_rows==0){
    $data["error"]="sin_registros";
    exit(json_encode($data));
}
$data["error"]="ok";
$contador=0;
$data["registros"]=array();

while ($reg=$res->fetch_assoc()){
    $data["registros"][$contador]["id_nie"]= $reg["id_nie"];
    $data["registros"][$contador]["nombre"]=ucwords(strtolower($reg["apellidos"])).", ".ucwords(strtolower($reg["nombre"]));
    $data["registros"][$contador]["email"]= $reg["email"];
    $data["registros"][$contador]["habilitado"]= $reg["habilitado"];
    if ($reg["no_ha_entrado"]==1) $data["registros"][$contador]["no_ha_entrado"]= "NO";
    else $data["registros"][$contador]["no_ha_entrado"]= "SI";
    $contador++;
}
$res->free();
exit(json_encode($data));
*/
