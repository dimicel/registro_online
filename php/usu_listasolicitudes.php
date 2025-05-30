<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
header("Content-Type: text/html;charset=utf-8");

$data=array();
$id_nie=$_POST["id_nie"];
$data["error"]="noregistros";

include("conexion.php");
if ($mysqli->errno>0) {
    $data["error"]="server";
    exit(json_encode($data));
}

$consulta=$mysqli->query("select * from revision_examen where id_nie='$id_nie' order by curso, fecha_registro,registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    $contador=0;
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Revisión de Examen"][$contador]=$reg;
        $data["proceso"]["Revisión de Examen"][$contador]["dir"]="revision_examen";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select * from revision_calificacion where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    $contador=0;
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Revisión de Calificación"][$contador]=$reg;
        $data["proceso"]["Revisión de Calificación"][$contador]["dir"]="revision_calificacion";
        $contador++;
    }
}
$consulta->free();


$consulta=$mysqli->query("select * from convalidaciones where id_nie='$id_nie' order by curso, fecha_registro,organismo_destino,registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    $contador=0;
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Convalidaciones"][$contador]=$reg;
        $data["proceso"]["Convalidaciones"][$contador]["dir"]="convalidaciones";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select * from exencion_fct where id_nie='$id_nie' order by curso, fecha_registro,registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    $contador=0;
    while ($reg=$consulta->fetch_assoc()){
        
        $registro = $reg["registro"];
        // 1. Extraer la parte desde la fecha hasta el final
        preg_match('/_(\d{8}_.+)$/', $registro, $matches);
        $parteDesdeFecha = $matches[1] ?? '';

        // 2. Extraer la fecha y convertirla a día, mes y año
        preg_match('/_(\d{2})(\d{2})(\d{4})_/', $registro, $fechaMatches);
        $dia = (int)($fechaMatches[1] ?? 0);
        $mes = (int)($fechaMatches[2] ?? 0);
        $anio = (int)($fechaMatches[3] ?? 0);

        // 3. Calcular el curso escolar
        $curso = '';
        if (($mes >= 7 && $mes <= 12)) {
            $curso = $anio . '-' . ($anio + 1);
        } elseif ($mes >= 1 && $mes <= 6) {
            $curso = ($anio - 1) . '-' . $anio;
        }
        
        
        if (is_file("../docs/".$id_nie."/exencion_form_emp"."/".$curso."/".$parteDesdeFecha."/docs/resolucion/resolucion.pdf"))$reg["procesado"]=1;
        else $reg["procesado"]=0;
        $data["proceso"]["Exención Formación en Empresas"][$contador]=$reg;
        $data["proceso"]["Exención Formación en Empresas"][$contador]["dir"]="exencion_form_emp";
        
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select * from premat_eso where id_nie='$id_nie' order by curso,fecha_registro, registro");
$contador=0;
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Prematrículas"][$contador]=$reg;
        $data["proceso"]["Prematrículas"][$contador]["dir"]="prematriculas";
        $contador++;
    }
}
$consulta->free();



$consulta=$mysqli->query("select*  from premat_bach where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Prematrículas"][$contador]=$reg;
        $data["proceso"]["Prematrículas"][$contador]["dir"]="prematriculas";
        $contador++;
    }
}
$consulta->free();

$contador=0;
$consulta=$mysqli->query("select * from mat_1eso where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]=$reg;
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select * from mat_2eso where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]=$reg;
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select * from mat_3eso where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]=$reg;
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select * from mat_4eso where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]=$reg;
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select * from mat_2esopmar where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]=$reg;
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select * from mat_3esopmar where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]=$reg;
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select * from mat_1bach_c where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]=$reg;
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select * from mat_1bach_hcs where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]=$reg;
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select * from mat_2bach_c where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]=$reg;
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select * from mat_2bach_hcs where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]=$reg;
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select * from mat_eso where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]=$reg;
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();
exit();
$consulta=$mysqli->query("select * where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]=$reg;
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();
//exit();
$contador=0;
$consulta=$mysqli->query("select * from mat_ciclos where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas CICLOS FORMATIVOS"][$contador]=$reg;
        $data["proceso"]["Matrículas CICLOS FORMATIVOS"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();


$contador=0;
$consulta=$mysqli->query("select *,ciclo,curso_ciclo from mat_fpb where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas FORMACIÓN PROFESIONAL BÁSICA"][$contador]=$reg;
        $data["proceso"]["Matrículas FORMACIÓN PROFESIONAL BÁSICA"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$contador=0;
$consulta=$mysqli->query("select * from residentes where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Residencia"][$contador]=$reg;
        $data["proceso"]["Residencia"][$contador]["dir"]="residencia";
        $contador++;
    }
}
$consulta->free();


$contador=0;
$consulta=$mysqli->query("select * from transporte where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Tansporte Escolar"][$contador]=$reg;
        $data["proceso"]["Tansporte Escolar"][$contador]["dir"]="transporte_escolar";
        $contador++;
    }
}
$consulta->free();

exit(json_encode($data));



