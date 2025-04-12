campos = new Array();
encabezamiento = new Array();
var anno_ini_curso = 0;
var mes;
var orden_campo;
var orden_direccion;
var curso_actual;
var num_registros;
var num_reg_pagina = 25;
var numero_paginas;
var pagina = 1;
var orden_direccion_usu = "🡅";
var departamento="";



$(function() {
    if (document.location.hostname!="registro.ulaboral.org")document.getElementById("servidor_pruebas").style.display="inherit";
    else document.getElementById("servidor_pruebas").style.display="none";
   
    document.getElementById("cargando").style.display = 'inherit';
    prom1=Promise.resolve($.post("php/sesion.php", { tipo_usu: "jefe departamento" },()=>{},"json"));
    prom2=prom1.then((resp)=> {
        if (resp["error"] != "ok") document.write(resp["error"]);
        else {
            anno_ini_curso = resp["anno_ini_curso"];
            departamento= resp["departamento"];
            document.getElementById("rotulo_tipo_usu").innerHTML="DEPARTAMENTO: "+departamento.toUpperCase(); 
            mes = resp["mes"];
            _curso = anno_ini_curso + "-" + (anno_ini_curso + 1);
            curso_actual=_curso;
            generaSelectCurso();
            document.getElementById("curso").value = _curso;

            $('#navegacion_usus_top,#navegacion_usus_bottom').bootpag({
                total: 1,
                page: pagina,
                maxVisible: 10,
                leaps: true,
                firstLastUse: true,
                first: '←',
                last: '→',
                wrapClass: 'pagination',
                activeClass: 'active',
                disabledClass: 'disabled',
                nextClass: 'next',
                prevClass: 'prev',
                lastClass: 'last',
                firstClass: 'first'
            }).on("page", function(event, num) {
                pagina = num;
                listaUsus();
            });
            $('#navegacion_usus_top li').addClass('page-item');
            $('#navegacion_usus_top a').addClass('page-link');
            $('#navegacion_usus_bottom li').addClass('page-item');
            $('#navegacion_usus_bottom a').addClass('page-link');

            listaUsus();
            document.getElementById("cargando").style.display = 'none';
        } 
    });

    
    
});

function generaSelectCurso(){
    if (mes<6) a_final=anno_ini_curso;
    else a_final=anno_ini_curso+1;

    const miSelect = document.getElementById("curso");
    for (var i=2020;i<=a_final;i++){
        const elemento = document.createElement("option");
        elemento.value = i+"-"+(parseInt(i)+1);
        elemento.textContent = elemento.value;
        miSelect.appendChild(elemento);
    }
}


function cierrasesion() {
    $.post("php/logout.php", {}, function(resp) {
        open("index.php?q=" + Date.now().toString(), "_self");
    });
}

function listaUsus() {
    direccion = new Array();
    direccion["🡅"] = "ASC";
    direccion["🡇"] = "DESC";

    estilo_usu = ["width:80px", "width:220px", "width:240px","width:70px;text-align:center","width:70px;text-align:center","width:100px;text-align:center"];
    encabezamiento_usu = ["NIE", "Alumno", "Nº Registro","Informe","Resolución","Estado"];

    //Construcción del encabezamiento de la tabla
    encab_usus = "<tr>";
    for (i = 0; i < encabezamiento_usu.length; i++) {
        if (encabezamiento_usu[i] == "Alumno") encab_usus += "<td style='" + estilo_usu[i] + "'onclick='ordenUsus()'>" + encabezamiento_usu[i] + " " + orden_direccion_usu + "</td>";
        else encab_usus += "<td style='" + estilo_usu[i] + "'>" + encabezamiento_usu[i] + "</td>";
    }
    document.getElementById("encabezado_usus").innerHTML = encab_usus;
    ///////////////////////////////////////////////
    datos = {
        buscar: document.getElementById("busqueda_usus").value,
        orden_direccion_usu: direccion[orden_direccion_usu],
        pagina: pagina,
        num_reg_pagina: num_reg_pagina,
        curso:document.getElementById("curso").value,
        departamento:departamento
    }
    $.post("php/departamento_listausuarios.php", datos, function(resp) {
        if (resp.error == "server") alerta("Error en el servidor. Inténtalo más tarde.", "Error de servidor");
        else if (resp.error == "sin_registros") {
            document.getElementById("div_notabla_usus").style.display = "";
            document.getElementById("div_tabla_usus").style.display = "none";
            numero_paginas=1;
            pagina=1;
            $('#navegacion_usus_top,#navegacion_usus_bottom').bootpag({
                total: numero_paginas
            });
            $('#navegacion_usus_top li').addClass('page-item');
            $('#navegacion_usus_top a').addClass('page-link');
            $('#navegacion_usus_bottom li').addClass('page-item');
            $('#navegacion_usus_bottom a').addClass('page-link');
        } else {
            document.getElementById("div_notabla_usus").style.display = "none";
            document.getElementById("div_tabla_usus").style.display = "";
            data = "";
            data_array = resp["registros"];
            for (i = 0; i < data_array.length; i++) {
                data += "<tr>";
                data += "<td style='" + estilo_usu[0] + "'>" + data_array[i]["id_nie"] + "</td>";
                data += "<td style='" + estilo_usu[1] + "'>" + data_array[i]["nombre"] + "</td>";
                data += "<td style='" + estilo_usu[2] + "'><a href='#'  onclick='verPanelProcesamiento(\""+data_array[i]["registro"]+"\",\""+data_array[i]["dirRegistro"]+"\")'>"+data_array[i]["registro"]+"</a></td>";
                //data += "<td style='" + estilo_usu[2] + "'><a href='docs/"+data_array[i]["id_nie"]+"/exencion_form_emp/"+document.getElementById("curso").value+"/"+data_array[i]["dirRegistro"]+"/"+data_array[i]["registro"]+".pdf' target='_blank'>"+data_array[i]["registro"]+"</a></td>";
                if (data_array[i]["informe_jd"]!=""){
                    data += "<td style='" + estilo_usu[3] + ";text-align:center'><a href='"+data_array[i]["informe_jd"]+"' target='_blank' >Ver</a></td>";
                }
                else{
                    data += "<td style='" + estilo_usu[3] + ";text-align:center'>-</td>";
                }
                if (data_array[i]["resolucion"]!=""){
                    data += "<td style='" + estilo_usu[4] + ";text-align:center'><a href='"+data_array[i]["resolucion"]+"' target='_blank'>Ver</a></td>";
                }
                else{
                    data += "<td style='" + estilo_usu[4] + ";text-align:center'>-</td>";
                }
                if (data_array[i]["visto"]==1){
                    data += "<td style='" + estilo_usu[5] + ";text-align:center'>Procesado</td>";
                }
                else{
                    //data += "<td style='" + estilo_usu[5] + ";text-align:center'><input type='button' class='btn btn-success btn-sm'  value='Procesar' onclick='generarInforme(\""+data_array[i]["registro"]+"\",\""+data_array[i]["dirRegistro"]+"\")'></td>";
                    data += "<td style='" + estilo_usu[5] + ";text-align:center'>Pendiente</td>";
                }
                data += "</tr>";
            }
            document.getElementById("registros_usus").innerHTML = data;
            num_registros = resp.num_registros;
            numero_paginas = Math.ceil(num_registros / num_reg_pagina);
            if (pagina > numero_paginas) pagina = numero_paginas;
            
            $('#navegacion_usus_top,#navegacion_usus_bottom').bootpag({
                total: numero_paginas
            });
            $('#navegacion_usus_top li').addClass('page-item');
            $('#navegacion_usus_top a').addClass('page-link');
            $('#navegacion_usus_bottom li').addClass('page-item');
            $('#navegacion_usus_bottom a').addClass('page-link');
        }
    }, "json");
}



function ordenUsus() {
    if (orden_direccion_usu == "🡅") orden_direccion_usu = "🡇";
    else orden_direccion_usu = "🡅";
    listaUsus();
}


function cierrasesion() {
    $.post("php/logout.php", {}, function(resp) {
        open("index.php?q=" + Date.now().toString(), "_self");
    });
}

function verPanelProcesamiento(reg,dirReg){
    ancho = 700;
    botones = "<input style='margin-left:5px' type='button' class='textoboton btn btn-success' value='Generar Informe' onclick='generaInforme(\""+reg+"\")'/>";
    botones += "<input style='margin-left:5px' type='button' class='textoboton btn btn-success' value='Cerrar' onclick='javascript:$(\"#verRegistro_div\").dialog(\"close\");$(\"#verRegistro_div\").dialog(\"destroy\");'/>";
    contenido="";
    $.post("php/secret_recuperaregistro.php", { formulario: "exencion_fct", registro: reg }, function(resp) {
        if (resp.error == "server") alerta("Error en el servidor. Inténtalo más tarde.", "Error de servidor");
        else if (resp.error == "no_tabla" || resp.error == "sin_registro") alerta("El registro no se encuentra en el servidor.", "No encontrado");
        else if (resp.error == "ok") {
            contenido += "<span class='verReg_label'>NIE: </span><span class='verReg_campo'>" + resp.registro.id_nie +"</span><span class='verReg_label' style='margin-left:5px'>NIF: </span><span class='verReg_campo'>" + resp.registro.id_nif +"</span><span class='verReg_label' style='margin-left:5px'>Nº Registro: </span><span class='verReg_campo'>" + reg +"</span><br>";
            contenido += "<span class='verReg_label'>Alumno: </span><span class='verReg_campo'>" + resp.registro.apellidos +", "+resp.registro.nombre+ "</span><br>";
            contenido += "<span class='verReg_label'>Cursa: </span><span class='verReg_campo'>"+resp.registro.curso_ciclo+" de Grado " + resp.registro.grado + " "+resp.registro.ciclo+"</span><br>";
            contenido += "<span class='verReg_label'>DOCUMENTOS ADJUNTOS: </span><br>";
            contenido +="<div id='ver_reg_ajuntosExencFCT'></div>"
            contenido +="<div class='container' style='margin-top:20px'><div class='row'>";
            contenido +="<div class='col-4 d-flex justify-content-right'>";
            contenido +="<label>Valoración del informe:</label>";
            contenido +="</div>";
            contenido +="<div class='col-5' >";
            contenido +="<select id='valoracion_informe' class='form-control' onchange='seleccionValoracion(this.value)'/>";
            contenido +="<option value=''>Seleccione una...</option>";
            contenido +="<option value='exento'>EXENTO</option>";
            contenido +="<option value='parcialmente exento'>PARCIALMENTE EXENTO</option>";
            contenido +="<option value='no exento'>NO EXENTO</option>";
            contenido +="</select>";
            contenido += "</div></div>";
            contenido += "<div class='row mt-3' id='div_motivo' style='display:none'><div class='col'>"
            contenido += "<span id='rotulo_motivo' class='verReg_label'>MOTIVO NO EXENCIÓN O EXENCIÓN PARCIAL (1000/1000): </span>";
            contenido += "<textarea id='motivo' style='width:100%' onchange='javascript:actualizar=true;' class='verReg_campo form-control' oninput='limiteCaracteres(this)'></textarea>";
            contenido += "</div></div><hr>";
            contenido += "<div class='row'><div class='col' style='text-align:right'>"
            contenido += botones;
            contenido += "</div></div></div>";
            document.getElementById("verRegistro_div").innerHTML = contenido;
            verRegAdjuntosExencFCT(reg);

            $("#verRegistro_div").dialog({
                autoOpen: true,
                dialogClass: "no-close",
                modal: true,
                draggable: false,
                hide: { effect: "fade", duration: 0 },
                resizable: false,
                show: { effect: "fade", duration: 0 },
                title: "VISTA DEL REGISTRO",
                width: ancho,
                position: { my: "center top", at: "center top", of: window }
            });
        }
    }, "json");
}

function limiteCaracteres(obj){
    var rot=document.getElementById("rotulo_motivo");
    if (obj.value.length<=1000) rot.innerHTML="MOTIVO NO EXENCIÓN O EXENCIÓN PARCIAL ("+String(1000-obj.value.length)+"/1000): ";
    else obj.value=obj.value.slice(0,-1);
}

function seleccionValoracion(v){
    if (v=="" || v=="exento") document.getElementById("div_motivo").style.display="none";
    else document.getElementById("div_motivo").style.display="";
}

function verRegAdjuntosExencFCT(reg){
    _div="";
    $.post("php/secret_exencion_fct_adjuntos.php",{registro:reg},(resp2)=>{
        if(resp2.error=="server") _div += "<span class='verReg_label'>Hay un problema en sel servidor y no se han podido recuperar los documentos adjuntos.</span>";
        else if(resp2.error=="sin_adjuntos") _div += "<span class='verReg_label'>El alumno no adjuntó documentos a la solicitud.</span>";
        else {
            _div+="<ul id='ul_docs_convalid'>";
            for(i=0;i<resp2.datos.length;i++){
                _div += "<li><a style='color:GREEN;font-size:0.75em' target='_blank' href='"+resp2.datos[i].ruta+"'>"+resp2.datos[i].descripcion+"</a>";
                if (resp2.datos[i].subidopor=="secretaria"){
                    _div+="&nbsp&nbsp(<a style='color:RED;font-size:0.75em' href='#' onclick='borraAdjuntos(\"exencion_fct_docs\",\""+resp2.datos[i].ruta+"\",\""+resp2.datos[i].descripcion+"\",\""+reg+"\",1)'>X</a>)";
                }
                _div+="</li>";
            }
            _div+="</ul>";
        }
        document.getElementById("ver_reg_ajuntosExencFCT").innerHTML=_div;
    },"json");
}


function generaInforme(_registro){
    alert(_registro)

}
