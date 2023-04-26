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
$registro=$_POST["registro"];

if(strpos($tabla,"premat_")){
    if (strpos($tabla,"eso")) $tabla="premat_eso";
    else $tabla="premat_bach";
}

$consulta="select * from $tabla where registro='$registro'";

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
            $data["registro"]["consolida_premat"]= $reg["consolida_premat"];
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
        }
        if(strrpos($tabla,"eso")>=0) $data["registro"]["transporte"]= $reg["transporte"];
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
        if ($tabla=="premat_1eso" || $tabla=="mat_1eso"){
            $data["registro"]["prog_ling"]=$reg["prog_ling"];
            $data["registro"]["rel_valores_et"]=$reg["rel_valores_et"];
            $data["registro"]["1_lengua_extr"]=$reg["1_lengua_extr"];
            $data["registro"]["optativa1"]=$reg["optativa1"];
            $data["registro"]["optativa2"]=$reg["optativa2"];
            $data["registro"]["optativa3"]=$reg["optativa3"];
            $data["registro"]["optativa4"]=$reg["optativa4"];
        }
        elseif($tabla=="premat_2eso" || $tabla=="mat_2eso"){
            $data["registro"]["prog_ling"]=$reg["prog_ling"];
            $data["registro"]["rel_valores_et"]=$reg["rel_valores_et"];
            $data["registro"]["1_lengua_extr"]=$reg["1_lengua_extr"];
            $data["registro"]["optativa1"]=$reg["optativa1"];
            $data["registro"]["optativa2"]=$reg["optativa2"];
            $data["registro"]["optativa3"]=$reg["optativa3"];
            $data["registro"]["optativa4"]=$reg["optativa4"];
            $data["registro"]["optativa5"]=$reg["optativa5"];
        }
        elseif($tabla=="premat_3eso" || $tabla=="mat_3eso"){
            $data["registro"]["prog_ling"]=$reg["prog_ling"];
            $data["registro"]["matematicas"]=$reg["matematicas"];
            $data["registro"]["rel_valores_et"]=$reg["rel_valores_et"];
            $data["registro"]["1_lengua_extr"]=$reg["1_lengua_extr"];
            $data["registro"]["optativa1"]=$reg["optativa1"];
            $data["registro"]["optativa2"]=$reg["optativa2"];
            $data["registro"]["optativa3"]=$reg["optativa3"];
            $data["registro"]["optativa4"]=$reg["optativa4"];
        }
        elseif($tabla=="premat_4eso" || $tabla=="mat_4eso"){
            $data["registro"]["prog_ling"]=$reg["prog_ling"];
            $data["registro"]["modalidad"]=$reg["modalidad"];
            $data["registro"]["rel_valores_et"]=$reg["rel_valores_et"];
            $data["registro"]["1_lengua_extr"]=$reg["1_lengua_extr"];
            $data["registro"]["espec_oblig"]=$reg["espec_oblig"];
            $data["registro"]["troncales_opcion1"]=$reg["troncales_opcion1"];
            $data["registro"]["troncales_opcion2"]=$reg["troncales_opcion2"];
            $data["registro"]["optativa1"]=$reg["espec_opcion1"];
            $data["registro"]["optativa2"]=$reg["espec_opcion2"];
            $data["registro"]["optativa3"]=$reg["espec_opcion3"];
            $data["registro"]["optativa4"]=$reg["espec_opcion4"];
            $data["registro"]["optativa5"]=$reg["espec_opcion5"];
            $data["registro"]["optativa6"]=$reg["espec_opcion6"];
            $data["registro"]["optativa7"]=$reg["espec_opcion7"];
            $data["registro"]["optativa8"]=$reg["espec_opcion8"];
            $data["registro"]["optativa9"]=$reg["espec_opcion9"];
        }
        elseif($tabla=="premat_3esodiv" || $tabla=="mat_2esopmar"){
            $data["registro"]["rel_valores_et"]=$reg["rel_valores_et"];
            $data["registro"]["optativa1"]=$reg["optativa1"];
            $data["registro"]["optativa2"]=$reg["optativa2"];
            $data["registro"]["optativa3"]=$reg["optativa3"];
            $data["registro"]["optativa4"]=$reg["optativa4"];
        }
        elseif($tabla=="premat_4esodiv" || $tabla=="mat_3esopmar"){
            $data["registro"]["rel_valores_et"]=$reg["rel_valores_et"];
            $data["registro"]["optativa1"]=$reg["optativa1"];
            $data["registro"]["optativa2"]=$reg["optativa2"];
            $data["registro"]["optativa3"]=$reg["optativa3"];
            $data["registro"]["optativa4"]=$reg["optativa4"];
        }
        elseif($tabla=="premat_1bach_h" || $tabla=="mat_1bach_hcs"){
            $data["registro"]["primer_idioma"]=$reg["primer_idioma"];
            $data["registro"]["itinerario"]=$reg["itinerario"];
            $data["registro"]["tronc_gen1"]=$reg["tronc_gen1"];
            $data["registro"]["tronc_gen2"]=$reg["tronc_gen2"];
            $data["registro"]["tronc_opcion"]=$reg["tronc_opcion"];
            $data["registro"]["espec_itin1"]=$reg["espec_itin1"];
            $data["registro"]["espec_itin2"]=$reg["espec_itin2"];
            $data["registro"]["espec_itin3"]=$reg["espec_itin3"];
            $data["registro"]["espec_itin4"]=$reg["espec_itin4"];
            $data["registro"]["espec_itin5"]=$reg["espec_itin5"];
            $data["registro"]["espec_itin6"]=$reg["espec_itin6"];
            $data["registro"]["espec_itin7"]=$reg["espec_itin7"];
            $data["registro"]["espec_itin8"]=$reg["espec_itin8"];

            $data["registro"]["espec_com1"]=$reg["espec_com1"];
            $data["registro"]["espec_com2"]=$reg["espec_com2"];
            $data["registro"]["espec_com3"]=$reg["espec_com3"];
            $data["registro"]["espec_com4"]=$reg["espec_com4"];
            $data["registro"]["espec_com5"]=$reg["espec_com5"];
            $data["registro"]["espec_com6"]=$reg["espec_com6"];
            $data["registro"]["espec_com7"]=$reg["espec_com7"];
            
        }
        elseif($tabla=="premat_1bach_c" || $tabla=="mat_1bach_c"){
            $data["registro"]["primer_idioma"]=$reg["primer_idioma"];
            $data["registro"]["itinerario"]=$reg["itinerario"];
            $data["registro"]["tronc_gen1"]=$reg["tronc_gen1"];
            $data["registro"]["tronc_gen2"]=$reg["tronc_gen2"];
            $data["registro"]["espec_itin1"]=$reg["espec_itin1"];
            $data["registro"]["espec_itin2"]=$reg["espec_itin2"];
            $data["registro"]["espec_itin3"]=$reg["espec_itin3"];
            $data["registro"]["espec_itin4"]=$reg["espec_itin4"];
            $data["registro"]["espec_itin5"]=$reg["espec_itin5"];
            $data["registro"]["espec_itin6"]=$reg["espec_itin6"];
            $data["registro"]["espec_itin7"]=$reg["espec_itin7"];
            $data["registro"]["espec_itin8"]=$reg["espec_itin8"];
            $data["registro"]["espec_itin9"]=$reg["espec_itin9"];
            $data["registro"]["espec_itin10"]=$reg["espec_itin10"];
            $data["registro"]["espec_itin11"]=$reg["espec_itin11"];

            $data["registro"]["espec_com1"]=$reg["espec_com1"];
            $data["registro"]["espec_com2"]=$reg["espec_com2"];
            $data["registro"]["espec_com3"]=$reg["espec_com3"];
            $data["registro"]["espec_com4"]=$reg["espec_com4"];
            $data["registro"]["espec_com5"]=$reg["espec_com5"];
            $data["registro"]["espec_com6"]=$reg["espec_com6"];
            $data["registro"]["espec_com7"]=$reg["espec_com7"];
        }
        elseif($tabla=="premat_2bach_h" || $tabla=="mat_2bach_hcs"){
            $data["registro"]["primer_idioma"]=$reg["primer_idioma"];
            $data["registro"]["itinerario"]=$reg["itinerario"];
            $data["registro"]["tronc_opc1"]=$reg["tronc_opc1"];
            $data["registro"]["tronc_opc2"]=$reg["tronc_opc2"];
            $data["registro"]["tronc_gen"]=$reg["tronc_gen"];
            $data["registro"]["espec_itin_com1"]=$reg["espec_itin_com1"];
            $data["registro"]["espec_itin_com2"]=$reg["espec_itin_com2"];
            $data["registro"]["espec_itin_com3"]=$reg["espec_itin_com3"];
            $data["registro"]["espec_itin_com4"]=$reg["espec_itin_com4"];
            $data["registro"]["espec_itin_com5"]=$reg["espec_itin_com5"];
            $data["registro"]["espec_itin_com6"]=$reg["espec_itin_com6"];
            $data["registro"]["espec_itin_com7"]=$reg["espec_itin_com7"];
            $data["registro"]["espec_itin_com8"]=$reg["espec_itin_com8"];
            $data["registro"]["espec_itin_com9"]=$reg["espec_itin_com9"];
            $data["registro"]["espec_itin_com10"]=$reg["espec_itin_com10"];
            $data["registro"]["espec_itin_com11"]=$reg["espec_itin_com11"];
            $data["registro"]["espec_itin_com12"]=$reg["espec_itin_com12"];
            $data["registro"]["espec_itin_com13"]=$reg["espec_itin_com13"];
            $data["registro"]["espec_itin_com14"]=$reg["espec_itin_com14"];
            $data["registro"]["espec_itin_com15"]=$reg["espec_itin_com15"];
            $data["registro"]["espec_itin_com16"]=$reg["espec_itin_com16"];
            $data["registro"]["espec_itin_com17"]=$reg["espec_itin_com17"];
        }
        elseif($tabla=="premat_2bach_c" || $tabla=="mat_2bach_c"){
            $data["registro"]["primer_idioma"]=$reg["primer_idioma"];
            $data["registro"]["itinerario"]=$reg["itinerario"];
            $data["registro"]["tronc_opc1"]=$reg["tronc_opc1"];
            $data["registro"]["tronc_opc2"]=$reg["tronc_opc2"];
            $data["registro"]["espec_itin_com1"]=$reg["espec_itin_com1"];
            $data["registro"]["espec_itin_com2"]=$reg["espec_itin_com2"];
            $data["registro"]["espec_itin_com3"]=$reg["espec_itin_com3"];
            $data["registro"]["espec_itin_com4"]=$reg["espec_itin_com4"];
            $data["registro"]["espec_itin_com5"]=$reg["espec_itin_com5"];
            $data["registro"]["espec_itin_com6"]=$reg["espec_itin_com6"];
            $data["registro"]["espec_itin_com7"]=$reg["espec_itin_com7"];
            $data["registro"]["espec_itin_com8"]=$reg["espec_itin_com8"];
            $data["registro"]["espec_itin_com9"]=$reg["espec_itin_com9"];
            $data["registro"]["espec_itin_com10"]=$reg["espec_itin_com10"];
            $data["registro"]["espec_itin_com11"]=$reg["espec_itin_com11"];
            $data["registro"]["espec_itin_com12"]=$reg["espec_itin_com12"];
            $data["registro"]["espec_itin_com13"]=$reg["espec_itin_com13"];
            $data["registro"]["espec_itin_com14"]=$reg["espec_itin_com14"];
            $data["registro"]["espec_itin_com15"]=$reg["espec_itin_com15"];
            $data["registro"]["espec_itin_com16"]=$reg["espec_itin_com16"];
            $data["registro"]["espec_itin_com17"]=$reg["espec_itin_com17"];
            $data["registro"]["espec_itin_com18"]=$reg["espec_itin_com18"];
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
