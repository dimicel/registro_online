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

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from revision_examen where id_nie='$id_nie' order by curso, fecha_registro,registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    $contador=0;
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Revisión de Examen"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Revisión de Examen"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Revisión de Examen"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Revisión de Examen"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Revisión de Examen"][$contador]["dir"]="revision_examen";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from revision_calificacion where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    $contador=0;
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Revisión de Calificación"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Revisión de Calificación"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Revisión de Calificación"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Revisión de Calificación"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Revisión de Calificación"][$contador]["dir"]="revision_calificacion";
        $contador++;
    }
}
$consulta->free();
$contador=0;
$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from premat_1eso where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Prematrículas"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Prematrículas"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Prematrículas"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Prematrículas"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Prematrículas"][$contador]["dir"]="prematriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from premat_2eso where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Prematrículas"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Prematrículas"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Prematrículas"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Prematrículas"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Prematrículas"][$contador]["dir"]="prematriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from premat_3eso where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Prematrículas"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Prematrículas"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Prematrículas"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Prematrículas"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Prematrículas"][$contador]["dir"]="prematriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from premat_4eso where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Prematrículas"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Prematrículas"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Prematrículas"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Prematrículas"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Prematrículas"][$contador]["dir"]="prematriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from premat_2esopmar where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Prematrículas"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Prematrículas"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Prematrículas"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Prematrículas"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Prematrículas"][$contador]["dir"]="prematriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from premat_3esopmar where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Prematrículas"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Prematrículas"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Prematrículas"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Prematrículas"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Prematrículas"][$contador]["dir"]="prematriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from premat_1bach_hcs where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Prematrículas"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Prematrículas"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Prematrículas"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Prematrículas"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Prematrículas"][$contador]["dir"]="prematriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from premat_1bach_c where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Prematrículas"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Prematrículas"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Prematrículas"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Prematrículas"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Prematrículas"][$contador]["dir"]="prematriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from premat_1bach_lomloe where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Prematrículas"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Prematrículas"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Prematrículas"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Prematrículas"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Prematrículas"][$contador]["dir"]="prematriculas";
        $contador++;
    }
}
$consulta->free();


$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from premat_2bach_hcs where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Prematrículas"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Prematrículas"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Prematrículas"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Prematrículas"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Prematrículas"][$contador]["dir"]="prematriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from premat_bach where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Prematrículas"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Prematrículas"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Prematrículas"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Prematrículas"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Prematrículas"][$contador]["dir"]="prematriculas";
        $contador++;
    }
}
$consulta->free();

$contador=0;
$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from mat_1eso where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from mat_2eso where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from mat_3eso where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from mat_4eso where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from mat_2esopmar where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from mat_3esopmar where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from mat_1bach_c where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from mat_1bach_hcs where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from mat_2bach_c where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from mat_2bach_hcs where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias,grupo from mat_eso where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $proc=$reg["grupo"];
        
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias,grupo from mat_bach where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $proc=$reg["grupo"];
        
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Matrículas ESO y BACHILLERATO"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();

$contador=0;
$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias,grado,ciclo,curso_ciclo,turno from mat_ciclos where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $proc=$reg["curso_ciclo"]."-";
        if($reg["grado"]=="MEDIO")$proc.="GM ".$reg["ciclo"]."(".$reg["turno"].")";
        else $proc.="GS ".$reg["ciclo"]." (".$reg["turno"].")";
        
        /*$data["proceso"]["Matrícula ".$proc][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Matrícula ".$proc][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Matrícula ".$proc][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Matrícula ".$proc][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Matrícula ".$proc][$contador]["dir"]="matriculas";*/

        $data["proceso"]["Matrículas CICLOS FORMATIVOS"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Matrículas CICLOS FORMATIVOS"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Matrículas CICLOS FORMATIVOS"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Matrículas CICLOS FORMATIVOS"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Matrículas CICLOS FORMATIVOS"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();


$contador=0;
$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias,ciclo,curso_ciclo from mat_fpb where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $proc=$reg["curso_ciclo"]."- FPB ".$reg["ciclo"];
        /*
        $data["proceso"]["Matrícula ".$proc][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Matrícula ".$proc][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Matrícula ".$proc][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Matrícula ".$proc][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Matrícula ".$proc][$contador]["dir"]="matriculas";
        */
        $data["proceso"]["Matrículas FORMACIÓN PROFESIONAL BÁSICA"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Matrículas FORMACIÓN PROFESIONAL BÁSICA"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Matrículas FORMACIÓN PROFESIONAL BÁSICA"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Matrículas FORMACIÓN PROFESIONAL BÁSICA"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Matrículas FORMACIÓN PROFESIONAL BÁSICA"][$contador]["dir"]="matriculas";
        $contador++;
    }
}
$consulta->free();


$contador=0;
$consulta=$mysqli->query("select fecha_registro,registro,curso,incidencias from transporte where id_nie='$id_nie' order by curso,fecha_registro, registro");
if ($consulta->num_rows>0){
    $data["error"]="ok";
    while ($reg=$consulta->fetch_assoc()){
        $data["proceso"]["Tansporte Escolar"][$contador]["fecha_registro"]=$reg["fecha_registro"];
        $data["proceso"]["Tansporte Escolar"][$contador]["registro"]=$reg["registro"];
        $data["proceso"]["Tansporte Escolar"][$contador]["curso"]=$reg["curso"];
        $data["proceso"]["Tansporte Escolar"][$contador]["incidencias"]=$reg["incidencias"];
        $data["proceso"]["Tansporte Escolar"][$contador]["dir"]="transporte_escolar";
        $contador++;
    }
}
$consulta->free();

exit(json_encode($data));



