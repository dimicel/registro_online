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

$tabla=$_POST["formulario"];
$tabla_db=$tabla;
$registro=$_POST["registro"];

if(strpos($tabla_db,"premat_")>=0){
    if (strpos($tabla_db,"eso")) $tabla_db="premat_eso";
    else $tabla_db="premat_bach";
}


$consulta="select * from $tabla_db where registro='$registro'";

$res=$mysqli->query($consulta);

if ($res->num_rows==0){
    $data["error"]="sin_registro";
    exit(json_encode($data));
}

$data["error"]="ok";
$data["registro"]=array();
while ($reg=$res->fetch_assoc()){
    if ($tabla=="revision_examen"){
        $data["registro"]["id_nif"]= $reg["id_nif"];
        $data["registro"]["nombre"]=$reg["nombre"];
        $data["registro"]["fecha_registro"]=$reg["fecha_registro"];
        $data["registro"]["curso"]=$reg["curso"];
        $data["registro"]["nombre"]=$reg["nombre"];
        $data["registro"]["tipo_doc"]=$reg["tipo_doc"];
        $data["registro"]["numero_doc"]=$reg["numero_doc"];
        $data["registro"]["en_calidad_de"]=$reg["en_calidad_de"];
        $data["registro"]["del_alumno"]=$reg["del_alumno"];
        $data["registro"]["cursa"]=$reg["cursa"];
        $data["registro"]["departamento"]=$reg["departamento"];
        $data["registro"]["profesor"]=$reg["profesor"];
        $data["registro"]["asignatura"]=$reg["asignatura"];
        $data["registro"]["fecha"]=$reg["fecha"];
        $data["registro"]["incidencias"]=$reg["incidencias"];

    }
    elseif ($tabla=="revision_calificacion"){
        $data["registro"]["id_nif"]= $reg["id_nif"];
        $data["registro"]["nombre"]=$reg["nombre"];
        $data["registro"]["fecha_registro"]=$reg["fecha_registro"];
        $data["registro"]["curso"]=$reg["curso"];
        $data["registro"]["tipo_doc"]=$reg["tipo_doc"];
        $data["registro"]["numero_doc"]=$reg["numero_doc"];
        $data["registro"]["domicilio"]=$reg["domicilio"];
        $data["registro"]["telefono"]=$reg["telefono"];
        $data["registro"]["poblacion"]=$reg["poblacion"];
        $data["registro"]["cp"]=$reg["cp"];
        $data["registro"]["provincia"]=$reg["provincia"];
        $data["registro"]["ciclo_grado"]=$reg["ciclo_grado"];
        $data["registro"]["ciclo_nombre"]=$reg["ciclo_nombre"];
        $data["registro"]["modulo"]=$reg["modulo"];
        $data["registro"]["nota"]=$reg["nota"];
        $data["registro"]["motivos"]=$reg["motivos"];
        $data["registro"]["incidencias"]=$reg["incidencias"];
    } 
    elseif(substr($tabla,0,7)=="premat_" || (substr($tabla,0,4)=="mat_" && $tabla!="mat_ciclos" && $tabla!="mat_fpb")){
        if (substr($tabla,0,4)=="mat_"){
            if(strrpos($tabla,"eso")>=0  || strrpos($tabla,"bach")>=0 )$data["registro"]["consolida_premat"]= $reg["consolida_premat"];
            $data["registro"]["al_nuevo"]= $reg["al_nuevo"];
            $data["registro"]["repite"]= $reg["repite"];
            $data["registro"]["interno"]= $reg["interno"];
            $data["registro"]["nif_nie"]=$reg["id_nif"];
            $data["registro"]["direccion"]=$reg["direccion"];
            $data["registro"]["cp"]=$reg["cp"];
            $data["registro"]["localidad"]=$reg["localidad"];
            $data["registro"]["provincia"]=$reg["provincia"];
            if ($tabla!="mat_eso" && $tabla!="mat_bach"){
                $data["registro"]["nif_nie_tutor1"]=$reg["nif_nie_tutor1"];
                $data["registro"]["nif_nie_tutor2"]=$reg["nif_nie_tutor2"];
            }
            $data["registro"]["autoriza_fotos"]= $reg["autoriza_fotos"];
            $data["registro"]["tutor_autorizaciones"]= $reg["tutor_autorizaciones"];
            if ($tabla=="mat_eso" || $tabla=="mat_bach"){
                $data["registro"]["al_nuevo_otracomunidad"]= $reg["al_nuevo_otracomunidad"];
            }
            if(strrpos($tabla,"eso")>=0) $data["registro"]["transporte"]= $reg["transporte"];
        }
        
        $data["registro"]["id_nie"]= $reg["id_nie"];
        $data["registro"]["nombre"]=$reg["apellidos"].", ".$reg["nombre"];
        $data["registro"]["fecha_registro"]=$reg["fecha_registro"];
        $data["registro"]["telef_alumno"]=$reg["telef_alumno"];
        $data["registro"]["email_alumno"]=$reg["email"];
        $data["registro"]["fecha_nac"]=date("d/m/Y",strtotime($reg['fecha_nac']));
        $data["registro"]["telefono"]=$reg["telefono"];
        $data["registro"]["tutor1"]=$reg["tutor1"];
        $data["registro"]["email_tutor1"]=$reg["email_tutor1"];
        $data["registro"]["tlf_tutor1"]=$reg["tlf_tutor1"];
        $data["registro"]["tutor2"]=$reg["tutor2"];
        $data["registro"]["email_tutor2"]=$reg["email_tutor2"];
        $data["registro"]["tlf_tutor2"]=$reg["tlf_tutor2"];
        $data["registro"]["incidencias"]=$reg["incidencias"]; 
        if($tabla=="premat_2eso"){
            $data["registro"]["prog_ling"]=$reg["prog_ling"];
            $data["registro"]["1_lengua_extr"]=$reg["materia1"];
            $data["registro"]["rel_valores_et"]=$reg["materia2"];
            $data["registro"]["optativa1"]=$reg["materia3"];
            $data["registro"]["optativa2"]=$reg["materia4"];
            $data["registro"]["optativa3"]=$reg["materia5"];
            $data["registro"]["optativa4"]=$reg["materia6"];
        }
        elseif($tabla=="premat_3eso"){
            $data["registro"]["prog_ling"]=$reg["prog_ling"];
            $data["registro"]["1_lengua_extr"]=$reg["materia1"];
            $data["registro"]["rel_valores_et"]=$reg["materia2"];
            $data["registro"]["optativa1"]=$reg["materia3"];
            $data["registro"]["optativa2"]=$reg["materia4"];
            $data["registro"]["optativa3"]=$reg["materia5"];
            $data["registro"]["optativa4"]=$reg["materia6"];
        }
        elseif($tabla=="premat_4eso"){
            $data["registro"]["prog_ling"]=$reg["prog_ling"];
            $data["registro"]["1_lengua_extr"]=$reg["materia1"];
            $data["registro"]["rel_valores_et"]=$reg["materia2"];
            $data["registro"]["matematicas"]=$reg["materia3"];
            $data["registro"]["opc_bloque1"]=$reg["materia4"];
            $data["registro"]["opc_bloque21"]=$reg["materia5"];
            $data["registro"]["opc_bloque22"]=$reg["materia6"];
            $data["registro"]["opc_bloque23"]=$reg["materia7"];
            $data["registro"]["opc_bloque24"]=$reg["materia8"];
            $data["registro"]["opc_bloque31"]=$reg["materia9"];
            $data["registro"]["opc_bloque32"]=$reg["materia10"];
            $data["registro"]["opc_bloque33"]=$reg["materia11"];
            $data["registro"]["opc_bloque34"]=$reg["materia12"];
            $data["registro"]["opc_bloque35"]=$reg["materia13"];
            $data["registro"]["opc_bloque36"]=$reg["materia14"];
            $data["registro"]["optativa1"]=$reg["materia15"];
            $data["registro"]["optativa2"]=$reg["materia16"];
            $data["registro"]["optativa3"]=$reg["materia17"];
            $data["registro"]["optativa4"]=$reg["materia18"];
            $data["registro"]["optativa5"]=$reg["materia19"];
        }
        elseif($tabla=="premat_3esodiv"){
            $data["registro"]["rel_valores_et"]=$reg["materia1"];
            $data["registro"]["optativa1"]=$reg["materia2"];
            $data["registro"]["optativa2"]=$reg["materia3"];
            $data["registro"]["optativa3"]=$reg["materia4"];
        }
        elseif($tabla=="premat_4esodiv"){
            $data["registro"]["rel_valores_et"]=$reg["materia1"];
            $data["registro"]["opcion1"]=$reg["materia2"];
            $data["registro"]["opcion2"]=$reg["materia3"];
            $data["registro"]["opcion3"]=$reg["materia4"];
            $data["registro"]["opcion4"]=$reg["materia5"];
            $data["registro"]["opcion5"]=$reg["materia6"];
            $data["registro"]["opcion6"]=$reg["materia7"];
            $data["registro"]["optativa1"]=$reg["materia8"];
            $data["registro"]["optativa2"]=$reg["materia9"];
            $data["registro"]["optativa3"]=$reg["materia10"];
            $data["registro"]["optativa4"]=$reg["materia11"];
            $data["registro"]["optativa5"]=$reg["materia12"];
        }
        elseif($tabla=="premat_1bach_h" || $tabla=="premat_1bach_c"){
            $data["registro"]["modalidad"]=$reg["modalidad"];
            $data["registro"]["primer_idioma"]=$reg["materia1"];
            $data["registro"]["rel_valores_et"]=$reg["materia2"];
            $data["registro"]["obligatoria1"]=$reg["materia3"];
            $data["registro"]["obligatoria2"]=$reg["materia4"];
            $data["registro"]["obligatoria3"]=$reg["materia5"];
            $data["registro"]["optativa1"]=$reg["materia6"];
            $data["registro"]["optativa2"]=$reg["materia7"];
            $data["registro"]["optativa3"]=$reg["materia8"];
            $data["registro"]["optativa4"]=$reg["materia9"];
            $data["registro"]["optativa5"]=$reg["materia10"];
            $data["registro"]["optativa6"]=$reg["materia11"];
            $data["registro"]["optativa7"]=$reg["materia12"];
            $data["registro"]["optativa8"]=$reg["materia13"];
            $data["registro"]["optativa9"]=$reg["materia14"];
            $data["registro"]["optativa10"]=$reg["materia15"];
            $data["registro"]["optativa11"]=$reg["materia16"];
            $data["registro"]["optativa12"]=$reg["materia17"];
            $data["registro"]["optativa13"]=$reg["materia18"];
            $data["registro"]["optativa14"]=$reg["materia19"];
            $data["registro"]["optativa15"]=$reg["materia20"];
        }
        elseif($tabla=="premat_2bach_h"){
            $data["registro"]["primer_idioma"]=$reg["materia1"];
            $data["registro"]["modalidad1"]=$reg["materia2"];
            $data["registro"]["modalidad2"]=$reg["materia3"];
            $data["registro"]["modalidad3"]=$reg["materia4"];
            $data["registro"]["optativa1"]=$reg["materia5"];
            $data["registro"]["optativa2"]=$reg["materia6"];
            $data["registro"]["optativa3"]=$reg["materia7"];
            $data["registro"]["optativa4"]=$reg["materia8"];
            $data["registro"]["optativa5"]=$reg["materia9"];
            $data["registro"]["optativa6"]=$reg["materia10"];
            $data["registro"]["optativa7"]=$reg["materia11"];
            $data["registro"]["optativa8"]=$reg["materia12"];
            $data["registro"]["optativa9"]=$reg["materia13"];
            $data["registro"]["optativa10"]=$reg["materia14"];
            $data["registro"]["optativa11"]=$reg["materia15"];
            $data["registro"]["optativa12"]=$reg["materia16"];
            $data["registro"]["optativa13"]=$reg["materia17"];
            $data["registro"]["optativa14"]=$reg["materia18"];
            $data["registro"]["optativa15"]=$reg["materia19"];
            $data["registro"]["optativa16"]=$reg["materia20"];
        }
        elseif($tabla=="premat_2bach_c"){
            $data["registro"]["primer_idioma"]=$reg["materia1"];
            $data["registro"]["modalidad1"]=$reg["materia2"];
            $data["registro"]["modalidad2"]=$reg["materia3"];
            $data["registro"]["modalidad3"]=$reg["materia4"];
            $data["registro"]["optativa1"]=$reg["materia5"];
            $data["registro"]["optativa2"]=$reg["materia6"];
            $data["registro"]["optativa3"]=$reg["materia7"];
            $data["registro"]["optativa4"]=$reg["materia8"];
            $data["registro"]["optativa5"]=$reg["materia9"];
            $data["registro"]["optativa6"]=$reg["materia10"];
            $data["registro"]["optativa7"]=$reg["materia11"];
            $data["registro"]["optativa8"]=$reg["materia12"];
            $data["registro"]["optativa9"]=$reg["materia13"];
            $data["registro"]["optativa10"]=$reg["materia14"];
            $data["registro"]["optativa11"]=$reg["materia15"];
            $data["registro"]["optativa12"]=$reg["materia16"];
            $data["registro"]["optativa13"]=$reg["materia17"];
            $data["registro"]["optativa14"]=$reg["materia18"];
            $data["registro"]["optativa15"]=$reg["materia19"];
        }
    }
    else if($tabla=="mat_ciclos"){
        $data["registro"]["id_nie"]= $reg["id_nie"];
        $data["registro"]["grado"]= $reg["grado"];
        $data["registro"]["ciclo"]= $reg["ciclo"];
        $data["registro"]["curso_ciclo"]= $reg["curso_ciclo"];
        $data["registro"]["turno"]= $reg["turno"];
        $data["registro"]["nombre"]=$reg["apellidos"].", ".$reg["nombre"];
        $data["registro"]["fecha_registro"]=$reg["fecha_registro"];
        $data["registro"]["nif_nie"]=$reg["id_nif"];
        $data["registro"]["telefono"]=$reg["telefono"];
        $data["registro"]["email"]=$reg["email"];
        $data["registro"]["fecha_nac"]=date("d/m/Y",strtotime($reg['fecha_nac']));
        $data["registro"]["direccion"]=$reg["direccion"];
        $data["registro"]["cp"]=$reg["cp"];
        $data["registro"]["localidad"]=$reg["localidad"];
        $data["registro"]["provincia"]=$reg["provincia"];
        $data["registro"]["autoriza_fotos"]= $reg["autoriza_fotos"];
        $data["registro"]["mayor_edad"]= $reg["mayor_edad"];
        $data["registro"]["tutor_autorizaciones"]= $reg["tutor_autorizaciones"];
        $data["registro"]["incidencias"]=$reg["incidencias"]; 
        $data["registro"]["al_nuevo_otracomunidad"]= $reg["al_nuevo_otracomunidad"];
    }
    else if($tabla=="mat_fpb"){
        $data["registro"]["id_nie"]= $reg["id_nie"];
        $data["registro"]["ciclo"]= $reg["ciclo"];
        $data["registro"]["curso_ciclo"]= $reg["curso_ciclo"];
        $data["registro"]["nombre"]=$reg["apellidos"].", ".$reg["nombre"];
        $data["registro"]["fecha_registro"]=$reg["fecha_registro"];
        $data["registro"]["nif_nie"]=$reg["id_nif"];
        $data["registro"]["telefono"]=$reg["telefono"];
        $data["registro"]["email"]=$reg["email"];
        $data["registro"]["fecha_nac"]=date("d/m/Y",strtotime($reg['fecha_nac']));
        $data["registro"]["direccion"]=$reg["direccion"];
        $data["registro"]["cp"]=$reg["cp"];
        $data["registro"]["localidad"]=$reg["localidad"];
        $data["registro"]["provincia"]=$reg["provincia"];
        $data["registro"]["autoriza_fotos"]= $reg["autoriza_fotos"];
        $data["registro"]["tutor_autorizaciones"]= $reg["tutor_autorizaciones"];
        $data["registro"]["incidencias"]=$reg["incidencias"]; 
        $data["registro"]["al_nuevo_otracomunidad"]= $reg["al_nuevo_otracomunidad"];
    }
}
exit(json_encode($data));
