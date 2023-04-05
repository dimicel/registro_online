<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
include("conexion.php");
header("Content-Type: text/html;charset=utf-8");

$data=array();
if ($mysqli->errno>0) {
    $data["error"]="server";
    exit(json_encode($data));
}

$tabla=$_POST["tabla"];
$curso=$_POST["curso"];
$buscar=$_POST["buscar"];
$orden_campo=$_POST["orden_campo"];
$orden_direccion=$_POST["orden_direccion"];
$solo_incidencias=$_POST["solo_incidencias"];
if (isset($_POST["curso_num"])) $curso_num=$_POST["curso_num"];
else $curso_num="";
if(isset($_POST["nuevo_otra_comunidad"])) $nuevo_otra_com=$_POST["nuevo_otra_comunidad"];
else $nuevo_otra_com="";

if ($tabla=="mat_ciclos"){
    $ciclo=$_POST["ciclo"];
    $curso_ciclo=$_POST["curso_ciclo"];
    $turno=$_POST["turno"];
}
elseif($tabla=="mat_fpb"){
    $ciclo=$_POST["ciclo"];
    $curso_ciclo=$_POST["curso_ciclo"];
}

if ($tabla=="premat_2eso" || $tabla=="premat_3eso" || $tabla=="premat_4eso"  || $tabla=="premat_3esopmar" || $tabla=="premat_1bach_lomloe" || $tabla=="premat_1bach_c" || $tabla=="premat_1bach_hcs" || $tabla=="premat_2bach_c" || $tabla=="premat_2bach_hcs"){
    $proceso="prematricula";
    $campos="id_nie,nombre,apellidos,registro,incidencias";
}
elseif($tabla=="mat_1eso" || $tabla=="mat_2eso" || $tabla=="mat_3eso" || $tabla=="mat_4eso" || $tabla=="mat_2esopmar" || $tabla=="mat_3esopmar" || $tabla=="mat_eso"){
    $proceso="matriculaeso";
    $campos="id_nie,nombre,apellidos,registro,consolida_premat,transporte,incidencias,listado";
}
elseif($tabla=="mat_1bach_c" || $tabla=="mat_1bach_hcs" || $tabla=="mat_2bach_c" || $tabla=="mat_2bach_hcs" || $tabla=="mat_bach"){
    $proceso="matriculabach";
    $campos="id_nie,nombre,apellidos,registro,consolida_premat,incidencias,listado";
}
elseif($tabla=="mat_ciclos"){
    $proceso="matriculaciclos";
    $campos="id_nie,nombre,apellidos,registro,incidencias,listado,grado,ciclo,curso_ciclo,turno,fecha_nac,mayor_28";
}
elseif($tabla=="mat_fpb"){
    $proceso="matriculafpb";
    $campos="id_nie,nombre,apellidos,registro,incidencias,listado,ciclo,curso_ciclo";
}
else {
    $proceso=$tabla;
    if ($proceso=="revision_examen"){
        $campos="id_nif,nombre,del_alumno,registro,listado,incidencias";
    }
    elseif ($proceso=="revision_calificacion"){
        $campos="id_nif,nombre,registro,listado,incidencias";
    }
}

$coletilla="";
if ($solo_incidencias==1) $coletilla="incidencias!='' and ";
if ($nuevo_otra_com=="Si") $coletilla.="al_nuevo_otracomunidad='Si' and ";
if($curso_num!="") $coletilla.="grupo='$curso_num' and ";


if ($proceso=="matriculaciclos"){
    $consulta="select ".$campos." from $tabla where $coletilla curso='$curso' and ciclo='$ciclo' and curso_ciclo='$curso_ciclo' and turno='$turno' " ;  
}
elseif($proceso=="matriculafpb"){
    $consulta="select ".$campos." from $tabla where $coletilla curso='$curso' and ciclo='$ciclo' and curso_ciclo='$curso_ciclo'" ;  
} else{
    $consulta="select ".$campos." from $tabla where $coletilla curso='$curso' " ;
}

if ($buscar!=""){
    if ($proceso=="prematricula" || $proceso=="matriculaeso" || $proceso=="matriculabach" || $proceso=="matriculaciclos" || $proceso=="matriculafpb"){
        $consulta=$consulta . " and id_nie like '%$buscar%' or registro like '%$buscar%' or nombre like '%$buscar%' or apellidos like '%$buscar%'";
    }
    else{
        $consulta=$consulta . " and id_nif like '%$buscar%' or registro like '%$buscar%' or nombre like '%$buscar%'";
    }
} 
$consulta=$consulta." order by $orden_campo $orden_direccion";


$res=$mysqli->query($consulta);

if ($res->num_rows==0){
    $data["error"]="sin_registros";
    exit(json_encode($data));
}

$data["error"]="ok";
$contador=0;
$data["registros"]=array();

if ($proceso=="revision_examen"){
    while ($reg=$res->fetch_assoc()){
        $data["registros"][$contador]["id_nif"]= $reg["id_nif"];
        $data["registros"][$contador]["nombre"]=$reg["nombre"];
        if ($reg["en_calidad_de"]!="ALUMNO")$data["registros"][$contador]["del_alumno"]=$reg["del_alumno"];
        else $data["registros"][$contador]["del_alumno"]="-";
        $data["registros"][$contador]["registro"]=$reg["registro"];
        $data["registros"][$contador]["listado"]=$reg["listado"];
        if ($reg["incidencias"]=="") $data["registros"][$contador]["incidencias"]=0;
        else $data["registros"][$contador]["incidencias"]=1;
        $contador++;
    }
}
elseif ($proceso=="revision_calificacion"){
    while ($reg=$res->fetch_assoc()){
        $data["registros"][$contador]["id_nif"]= $reg["id_nif"];
        $data["registros"][$contador]["nombre"]=$reg["nombre"];
        $data["registros"][$contador]["registro"]=$reg["registro"];
        $data["registros"][$contador]["listado"]=$reg["listado"];
        if ($reg["incidencias"]=="") $data["registros"][$contador]["incidencias"]=0;
        else $data["registros"][$contador]["incidencias"]=1;
        $contador++;
    }
} 
elseif ($proceso=="prematricula"){
    while ($reg=$res->fetch_assoc()){
        $data["registros"][$contador]["id_nie"]= $reg["id_nie"];
        $data["registros"][$contador]["nombre"]=ucwords(strtolower($reg["apellidos"])).", ".ucwords(strtolower($reg["nombre"]));
        $data["registros"][$contador]["registro"]=$reg["registro"];
        if ($reg["incidencias"]=="") $data["registros"][$contador]["incidencias"]=0;
        else $data["registros"][$contador]["incidencias"]=1;
        $contador++;
    }
}
elseif ($proceso=="matriculaeso"){
    while ($reg=$res->fetch_assoc()){
        $data["registros"][$contador]["id_nie"]= $reg["id_nie"];
        $data["registros"][$contador]["nombre"]=ucwords(strtolower($reg["apellidos"])).", ".ucwords(strtolower($reg["nombre"]));
        $data["registros"][$contador]["registro"]=$reg["registro"];
        $data["registros"][$contador]["consolida_premat"]=$reg["consolida_premat"];
        $data["registros"][$contador]["transporte"]=$reg["transporte"];
        $data["registros"][$contador]["listado"]=$reg["listado"];
        if ($reg["incidencias"]=="") $data["registros"][$contador]["incidencias"]=0;
        else $data["registros"][$contador]["incidencias"]=1;
        $contador++;
    }
}
elseif ($proceso=="matriculabach"){
    while ($reg=$res->fetch_assoc()){
        $data["registros"][$contador]["id_nie"]= $reg["id_nie"];
        $data["registros"][$contador]["nombre"]=ucwords(strtolower($reg["apellidos"])).", ".ucwords(strtolower($reg["nombre"]));
        $data["registros"][$contador]["registro"]=$reg["registro"];
        $data["registros"][$contador]["consolida_premat"]=$reg["consolida_premat"];
        $data["registros"][$contador]["listado"]=$reg["listado"];
        if ($reg["incidencias"]=="") $data["registros"][$contador]["incidencias"]=0;
        else $data["registros"][$contador]["incidencias"]=1;
        $contador++;
    }
}
elseif ($proceso=="matriculaciclos"){
    while ($reg=$res->fetch_assoc()){
        $data["registros"][$contador]["id_nie"]= $reg["id_nie"];
        $data["registros"][$contador]["nombre"]=ucwords(strtolower($reg["apellidos"])).", ".ucwords(strtolower($reg["nombre"]));
        $data["registros"][$contador]["registro"]=$reg["registro"];
        $data["registros"][$contador]["listado"]=$reg["listado"];
        $data["registros"][$contador]["mayor_28"]=$reg["mayor_28"];
        if ($reg["incidencias"]=="") $data["registros"][$contador]["incidencias"]=0;
        else $data["registros"][$contador]["incidencias"]=1;
        $contador++;
    }
}
elseif ($proceso=="matriculafpb"){
    while ($reg=$res->fetch_assoc()){
        $data["registros"][$contador]["id_nie"]= $reg["id_nie"];
        $data["registros"][$contador]["nombre"]=ucwords(strtolower($reg["apellidos"])).", ".ucwords(strtolower($reg["nombre"]));
        $data["registros"][$contador]["registro"]=$reg["registro"];
        $data["registros"][$contador]["listado"]=$reg["listado"];
        if ($reg["incidencias"]=="") $data["registros"][$contador]["incidencias"]=0;
        else $data["registros"][$contador]["incidencias"]=1;
        $contador++;
    }
}

exit(json_encode($data));

