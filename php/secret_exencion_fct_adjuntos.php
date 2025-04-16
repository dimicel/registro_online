<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

if ($mysqli->errno>0) {
    $data["error"]="server";
    exit(json_encode($data));
}
$registro=$_POST["registro"];
if(isset($_POST["listarResolucion"])){
    $listaRes=false;
}
else {
    $listaRes=true;
}


$sql = "SELECT *  FROM exencion_fct_docs WHERE registro = '$registro' ORDER BY ruta";
$result = $mysqli->query($sql);
$contador=0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data["datos"][$contador]["descripcion"]=$row["descripcion"];
        $data["datos"][$contador]["ruta"]=$row["ruta"];
        $data["datos"][$contador]["subidopor"]=$row["subidopor"];
        $contador++;
    }
    $mysqli->close();
    if ($listaRes==true){
        if (is_file("../".dirname($data["datos"][$contador-1]["ruta"])."/informe_jd/informe_jd.pdf")){
            $data["datos"][$contador]["descripcion"]="Informe del Jefe Dpto.";
            $data["datos"][$contador]["ruta"]=dirname($data["datos"][$contador-1]["ruta"])."/informe_jd/informe_jd.pdf";
            $data["datos"][$contador]["subidopor"]="generado_por_aplicacion";
        }
        if (is_file("../".dirname($data["datos"][$contador-1]["ruta"])."/resolucion/resolucion.pdf")){
            $data["datos"][$contador]["descripcion"]="Resolución";
            $data["datos"][$contador]["ruta"]=dirname($data["datos"][$contador-1]["ruta"])."/resolucion/resolucion.pdf";
            $data["datos"][$contador]["subidopor"]="generado_por_aplicacion";
        }
    }
    $data["error"]="ok";
    exit(json_encode($data));
}
else {
    $data["error"]="sin_adjuntos";
    $mysqli->close();
    exit(json_encode($data));
}

