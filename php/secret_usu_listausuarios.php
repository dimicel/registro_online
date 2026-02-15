<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado'] !== "correcto") exit("Acceso denegado");

include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

$data = array();

// Verificar conexión
if ($mysqli->connect_errno) {
    $data["error"] = "server";
    exit(json_encode($data));
}

// Recogida de variables
$pagina = isset($_POST["pagina"]) ? (int)$_POST["pagina"] : 1;
$num_reg_pagina = isset($_POST["num_reg_pagina"]) ? (int)$_POST["num_reg_pagina"] : 10;
$orden_direccion = ($_POST["orden_direccion_usu"] == 'DESC') ? 'DESC' : 'ASC';
$buscar = trim($_POST["buscar"]);
$solo_han_entrado = $_POST["solo_han_entrado"];
$curso = $_POST["curso"];

$condiciones = [];
$params = [];
$tipos = "";

// 1. Filtro: Solo han entrado
if ($solo_han_entrado == "Si") {
    $condiciones[] = "u.no_ha_entrado = 0";
} elseif ($solo_han_entrado == "No") {
    $condiciones[] = "u.no_ha_entrado = 1";
}

// 2. Filtro: Curso (Subconsultas en tablas de matrícula)
if ($curso != "Todos") {
    $condiciones[] = "(
        u.id_nie IN (SELECT id_nie FROM mat_1bach_c WHERE curso = ?) OR
        u.id_nie IN (SELECT id_nie FROM mat_1bach_hcs WHERE curso = ?) OR
        u.id_nie IN (SELECT id_nie FROM mat_1eso WHERE curso = ?) OR 
        u.id_nie IN (SELECT id_nie FROM mat_2eso WHERE curso = ?) OR 
        u.id_nie IN (SELECT id_nie FROM mat_3eso WHERE curso = ?) OR 
        u.id_nie IN (SELECT id_nie FROM mat_4eso WHERE curso = ?) OR 
        u.id_nie IN (SELECT id_nie FROM mat_2bach_c WHERE curso = ?) OR 
        u.id_nie IN (SELECT id_nie FROM mat_2bach_hcs WHERE curso = ?) OR 
        u.id_nie IN (SELECT id_nie FROM mat_2esopmar WHERE curso = ?) OR 
        u.id_nie IN (SELECT id_nie FROM mat_3esodiv WHERE curso = ?) OR 
        u.id_nie IN (SELECT id_nie FROM mat_3esopmar WHERE curso = ?) OR 
        u.id_nie IN (SELECT id_nie FROM mat_eso WHERE curso = ?) OR 
        u.id_nie IN (SELECT id_nie FROM mat_bach WHERE curso = ?) OR 
        u.id_nie IN (SELECT id_nie FROM mat_fpb WHERE curso = ?) OR 
        u.id_nie IN (SELECT id_nie FROM mat_ciclos WHERE curso = ?)
    )";
    $tipos .= "sssssssssssssss";
    array_push($params, $curso, $curso, $curso, $curso, $curso, $curso, $curso, $curso, $curso, $curso, $curso, $curso, $curso, $curso, $curso);
}

// 3. Filtro: Buscador
if ($buscar != "") {
    $condiciones[] = "(u.apellidos LIKE ? OR u.nombre LIKE ? OR u.id_nie LIKE ?)";
    $termino = "%$buscar%";
    $tipos .= "sss";
    array_push($params, $termino, $termino, $termino);
}

// Construir el WHERE
$where_sql = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

// --- CONSULTA 1: OBTENER TOTAL PARA PAGINACIÓN ---
$sql_count = "SELECT COUNT(*) AS total FROM usuarios u $where_sql";
$stmt_count = $mysqli->prepare($sql_count);
if (!empty($params)) {
    $stmt_count->bind_param($tipos, ...$params);
}
$stmt_count->execute();
$res_count = $stmt_count->get_result();
$total_filas = $res_count->fetch_assoc()['total'];
$data["num_registros"] = $total_filas;
$stmt_count->close();

if ($total_filas == 0) {
    $data["error"] = "sin_registros";
    exit(json_encode($data));
}

// --- CONSULTA 2: OBTENER DATOS CON RESIDENTES Y PAGINACIÓN ---
$offset = ($pagina - 1) * $num_reg_pagina;

// Configuración dinámica según el curso
if ($curso != "Todos") {
    $select_residente = ", IF(r.id_nie IS NULL, 'No', 'Si') as residente";
    $join_residente = " LEFT JOIN residentes r ON u.id_nie = r.id_nie AND r.curso = ? ";
    // El orden de los tipos para bind_param debe coincidir con el orden en la SQL:
    // 1. El curso del JOIN, 2. Los filtros del WHERE, 3. LIMIT, 4. OFFSET
    $tipos_data = "s" . $tipos . "ii";
    $params_data = array_merge([$curso], $params, [$num_reg_pagina, $offset]);
} else {
    $select_residente = ", '-' as residente";
    $join_residente = "";
    $tipos_data = $tipos . "ii";
    $params_data = array_merge($params, [$num_reg_pagina, $offset]);
}

$consulta = "SELECT u.* $select_residente 
             FROM usuarios u 
             $join_residente 
             $where_sql 
             ORDER BY u.apellidos $orden_direccion 
             LIMIT ? OFFSET ?";

$stmt_data = $mysqli->prepare($consulta);
$stmt_data->bind_param($tipos_data, ...$params_data);
$stmt_data->execute();
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

$res->free();
$stmt_data->close();
$mysqli->close();

exit(json_encode($data));
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
