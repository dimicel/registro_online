var num_registros;
var num_reg_pagina = 25;
var numero_paginas;
var pagina = 1;
var orden_direccion_usu = "🡅";
var validFormSubeDoc;
var registro_adjuntos_convalid="";
var registro_adjuntos_exenc_fct="";
//var alto_tabla_usus=480;

$(function() {
    $('#registros_usus').contextMenu({
        selector: 'tr',
        callback: function(key, options) {
            id = $(this).children("td:first").html();
            nom = $(this).children("td:nth-child(2)").html();
            if (key == "edit") {
                panelModUsu(id);
            } else if (key == "delete") {
                eliminaUsuario(id, nom);
            } else if (key == "upload") {
                subeDocExpediente(id, nom);
            }
            else if(key=="inhabilitar"){
                inhabilitaUsuario(id,$(this));
            }
            else if(key=="pdf_evau"){
                pdfEVAU(id,nom);
            }
            else if(key=="download"){
                descargarExpediente(id,nom);
            }
        },
        items: {
            "edit": { name: "Ver/Modificar Datos" },
            "upload": { name: "Subir un documento a Expediente" },
            "download":{name: "Descargar Expediente"},
            "inhabilitar":{name: "Inhabilitar/Habilitar usuario"},
            "pdf_evau":{name: "Generar PDF NIF/NIE para EVAU"},
            "delete": { name: "Eliminar" }
        }
    });

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
});

function inhabilitaUsuario(_ID,obj){
    if (obj.css("background-color")=="rgb(255, 0, 0)")habilitar=1;  //rojo
    else habilitar=0;
    $.post("php/secret_usu_inhabilitar.php",{id_nie:_ID,habilitar:habilitar},(resp)=>{
        if (resp=="inhabilitado"){
            listaUsus();
            alerta("Usuario INHABILITADO","CAMBIO ESTADO USUARIO");
        } 
        else if(resp=="habilitado"){
            listaUsus();
            alerta("Usuario HABILITADO","CAMBIO ESTADO USUARIO");
        } 
        else {
            alerta("No se ha podido cambiar el estado del usuario por un problema en el servidor.","ERROR SERVIDOR");
        }
    })
}

function listaUsus() {
    direccion = new Array();
    direccion["🡅"] = "ASC";
    direccion["🡇"] = "DESC";

    estilo_usu = ["width:80px", "width:270px", "width:270px", "width:90px", "width:40px", "width:80px"];
    encabezamiento_usu = ["NIE", "Alumno", "Email", "¿Ha entrado?", "Imag.", "Expediente"];

    //Construcción del encabezamiento de la tabla
    encab_usus = "<tr>";
    for (i = 0; i < encabezamiento_usu.length; i++) {
        if (encabezamiento_usu[i] == "Alumno") encab_usus += "<td style='" + estilo_usu[i] + "'onclick='ordenUsus()'>" + encabezamiento_usu[i] + " " + orden_direccion_usu + "</td>";
        else encab_usus += "<td style='" + estilo_usu[i] + "'>" + encabezamiento_usu[i] + "</td>";
    }
    ///////////////////////////////////////////////
    datos = {
        buscar: document.getElementById("busqueda_usus").value,
        orden_direccion_usu: direccion[orden_direccion_usu],
        pagina: pagina,
        num_reg_pagina: num_reg_pagina,
        solo_han_entrado: document.getElementById("sel_solo_entrado").value
    }
    $.post("php/secret_usu_listausuarios.php", datos, function(resp) {
        if (resp.error == "server") alerta("Error en el servidor. Inténtalo más tarde.", "Error de servidor");
        else if (resp.error == "sin_registros") {
            document.getElementById("div_notabla_usus").style.display = "inline-block";
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
            document.getElementById("div_tabla_usus").style.display = "inline-block";
            data = "";
            data_array = resp["registros"];
            for (i = 0; i < data_array.length; i++) {
                n_reg="";
                if (data_array[i]["id_nie"].substring(0,9) == "S4500175G") continue;
                if (data_array[i]["habilitado"]==0)data += "<tr style='background-color:red'>";
                else data += "<tr>";
                data += "<td style='" + estilo_usu[0] + "'>" + data_array[i]["id_nie"] + "</td>";
                data += "<td style='" + estilo_usu[1] + "'>" + data_array[i]["nombre"] + "</td>";
                if (String(data_array[i]["nombre"]).trim() != "" && data_array[i]["habilitado"]==1) {
                    data += "<td style='" + estilo_usu[2] + "'><a href='javascript:void(0)' onclick='panelEnvioEmail(\"" + data_array[i]["email"] + "\")'>" + data_array[i]["email"] + "</a></td>";
                } else {
                    data += "<td style='" + estilo_usu[2] + "'></td>";
                }
                data += "<td style='" + estilo_usu[3] + ";text-align:center'>" + data_array[i]["no_ha_entrado"] + "</td>";
                //Columna DOCS
                data += "<td style='" + estilo_usu[4] + ";text-align:center' onclick='javascript:verDocsMatricula(this.parentNode.children[0].innerHTML)'>Ver</td>";
                //columna EXPEDIENTE
                data += "<td style='" + estilo_usu[5] + ";text-align:center' onclick='javascript:verExpediente(this.parentNode.children[0].innerHTML,this.parentNode.children[1].innerHTML)'>Ver</td>";
                data += "</tr>";
            }
            document.getElementById("encabezado_usus").innerHTML = encab_usus;
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

function cierraListaUsuarios() {
    $("#doc_reg_tab").removeClass("d-none");
    $("#usu_reg_tab").addClass("d-none");
}


function verExpediente(id_nie, nom) {
    tablas = ["mat_1eso",
        "mat_2eso",
        "mat_2esopmar",
        "mat_3eso",
        "mat_3esopmar",
        "mat_4eso",
        "mat_1bach_c",
        "mat_1bach_hcs",
        "mat_2bach_c",
        "mat_2bach_hcs",
        "mat_fpb",
        "mat_ciclos",
        "transporte"
    ];

    tablas_asoc = {
        "mat_1eso": "MATRÍCULA 1º ESO",
        "mat_2eso": "MATRÍCULA 2º ESO",
        "mat_2esopmar": "MATRÍCULA 2º ESO PMAR",
        "mat_3eso": "MATRÍCULA 3º ESO",
        "mat_3esopmar": "MATRÍCULA 3º ESO PMAR",
        "mat_4eso": "MATRÍCULA 4º ESO",
        "mat_1bach_c": "MATRÍCULA 1º BACHILLERATO CIENCIAS",
        "mat_1bach_hcs": "MATRÍCULA 1º BACHILLERATO HH.CC.SS.",
        "mat_2bach_c": "MATRÍCULA 2º BACHILLERATO CIENCIAS",
        "mat_2bach_hcs": "MATRÍCULA 2º BACHILLERATO HH.CC.SS.",
        "mat_fpb": "MATRÍCULA FPB",
        "mat_ciclos": "MATRÍCULA CICLOS FORMATIVOS",
        "transporte": "SOLICITUD DE TRANSPORTE"
    };

    tablas_php = {
        "mat_1eso": "descargapdf_mat1eso.php",
        "mat_2eso": "descargapdf_mat2eso.php",
        "mat_2esopmar": "descargapdf_mat2esopmar.php",
        "mat_3eso": "descargapdf_mat3eso.php",
        "mat_3esopmar": "descargapdf_mat3esopmar.php",
        "mat_4eso": "descargapdf_mat4eso.php",
        "mat_1bach_c": "descargapdf_mat1bach_c.php",
        "mat_1bach_hcs": "descargapdf_mat1bach_hcs.php",
        "mat_2bach_c": "descargapdf_mat2bach.php",
        "mat_2bach_hcs": "descargapdf_mat2bach_hcs.php",
        "mat_fpb": "descargapdf_matfpb.php",
        "mat_ciclos": "descargapdf_matciclos.php",
        "transporte": "descargapdf.php"
    };

    dir_php = {
        "mat_1eso": "matriculas",
        "mat_2eso": "matriculas",
        "mat_2esopmar": "matriculas",
        "mat_3eso": "matriculas",
        "mat_3esopmar": "matriculas",
        "mat_4eso": "matriculas",
        "mat_1bach_c": "matriculas",
        "mat_1bach_hcs": "matriculas",
        "mat_2bach_c": "matriculas",
        "mat_2bach_hcs": "matriculas",
        "mat_fpb": "matriculas",
        "mat_ciclos": "matriculas",
        "transporte": "transporte"
    };

    docs_exp = { //par índice del array y titulo sección de la tabla html
        "anulacion_matricula": "ANULACIÓN DE MATRÍCULA",
        "anulacion_modulos_modular": "ANULACIÓN DE MÓDULOS (MODULAR)",
        "certificado_notas": "CERTIFICADOS DE NOTAS",
        "convalidaciones": "CONVALIDACIONES",
        "exencion_fct":"EXENCIÓN FORMACIÓN EN EMPRESAS (PFE)",
        "fct": "FCT",
        "homologacion_estudios": "HOMOLOGACIÓN DE ESTUDIOS",
        "matriculas": "MATRICULAS",
        "informes_orientacion": "ORIENTACIÓN (INFORMES)",
        "perdida_eval_continua": "PÉRDIDA DERECHO EVALUACIÓN CONTÍNUA",
        "prematriculas": "PREMATRICULAS",
        "renuncia_convocatoria": "RENUNCIA A CONVOCATORIA",
        "titulo_eso_fpb": "TITULACIÓN DE LA ESO PARA FPB",
        "transporte_escolar": "TRANSPORTE ESCOLAR",
        "otros": "OTROS"
    }
    
    panelExpedienteUsuario(id_nie,nom);
    
}

function obtieneDocsExpediente() {
    alert("n_reg: "+n_reg);
    filtro_curso = document.getElementById("curso_exp").value;
    $.post("php/secret_usu_expedienteusu.php", { id_nie: document.getElementById("nie_exp").innerHTML, filtro: filtro_curso }, function(resp) {
        if (resp.error == "server") alerta("Error en el servidor. Inténtalo más tarde.", "Error de servidor");
        else if (resp.error == "sin_registros") {
            contenido_div = "<center>NO EXISTEN DOCUMENTOS ASOCIADOS A ESTE NIE</center>";
            document.getElementById("div_tabla_expediente").innerHTML = contenido_div;
        } else {
            contenido_div = "<table style='display: block; width: 100%;'>";
            //Obtenemos los docs desde los propios directorios del expediente
            for (var td in docs_exp) {
                if (resp["docs"][td].length > 0) {
                    contenido_div += "<tr style='font-size:bolder'><td colspan=4 width='775px'>" + docs_exp[td] + "</td></tr>";
                    for (j = 0; j < resp["docs"][td].length; j++) {
                        nombre_doc=resp["docs"][td][j]["doc"];
                        if(n_reg==nombre_doc.slice(0, nombre_doc.lastIndexOf("."))) contenido_div+= "<tr style='background-color:orange !important'>";
                        else contenido_div+= "<tr>";
                        if(docs_exp[td] == "CONVALIDACIONES"){
                            contenido_div += "<td width='80px'>" + resp["docs"][td][j]["curso"] + "</td>";
                            contenido_div += "<td><a href='" + resp["docs"][td][j]["enlace"] + "' target='_blank'>"+resp["docs"][td][j]["doc"] + "</a>";
                            contenido_div += "<a style='margin-left:15px;margin-right:15px' href='#' onclick='adjuntosConvalid(\""+resp['docs'][td][j]['doc']+"\",\"convalidaciones\")'>>Ver Adjuntos<</a>";
                            if (resp["docs"][td][j]["resolucion"]=="" && resp["docs"][td][j]["resolucion_con"]=="" && resp["docs"][td][j]["resolucion_min"]==""){
                                contenido_div +="</td>";
                            }
                            else{
                                if (resp["docs"][td][j]["resolucion"]!=""){
                                    contenido_div += "<a target='_blank' style='margin-left:5px' href='"+resp['docs'][td][j]['resolucion']+"'>>Res.Centro<</a></td>";
                                }
                                if (resp["docs"][td][j]["resolucion_con"]!=""){
                                    contenido_div += "<a target='_blank' style='margin-left:5px' href='"+resp['docs'][td][j]['resolucion_con']+"'>>Res.Consej.<</a></td>";
                                }
                                if (resp["docs"][td][j]["resolucion_min"]!=""){
                                    contenido_div += "<a target='_blank' style='margin-left:5px' href='"+resp['docs'][td][j]['resolucion_min']+"'>>Res.Minist.<</a></td>";
                                }
                            }
                        }
                        else if(docs_exp[td] == "EXENCIÓN FORMACIÓN EN EMPRESAS (PFE)"){
                            contenido_div += "<td width='80px'>" + resp["docs"][td][j]["curso"] + "</td>";
                            contenido_div += "<td><a href='" + resp["docs"][td][j]["enlace"] + "' target='_blank'>"+resp["docs"][td][j]["doc"] + "</a>";
                            contenido_div += "<a style='margin-left:15px;margin-right:15px' href='#' onclick='adjuntosConvalid(\""+resp['docs'][td][j]['doc']+"\",\"exencion_fct\")'>>Ver Adjuntos<</a>";
                            if (resp["docs"][td][j]["resolucion"]=="" && resp["docs"][td][j]["informe_jd"]==""){
                                contenido_div +="</td>";
                            }
                            else{
                                if (resp["docs"][td][j]["informe_jd"]!=""){
                                    contenido_div += "<a target='_blank' style='margin-left:5px' href='"+resp['docs'][td][j]['informe_jd']+"' title='Informe del Jefe de Departamento'>>Informe JD<</a>";
                                }
                                if (resp["docs"][td][j]["resolucion"]!=""){
                                    contenido_div += "<a target='_blank' style='margin-left:5px' href='"+resp['docs'][td][j]['resolucion']+"'>>Resolución<</a>";
                                }
                                contenido_div +="</td>";
                            }
                        }
                        else{
                            contenido_div += "<td width='80px'>" + resp["docs"][td][j]["curso"] + "</td>";
                            contenido_div += "<td><a href='" + resp["docs"][td][j]["enlace"] + "' target='_blank'>"
                            contenido_div += resp["docs"][td][j]["doc"] + "</a></td>";
                        }
                        if (docs_exp[td] != "MATRICULAS" && docs_exp[td] != "PREMATRICULAS" && docs_exp[td] != "CONVALIDACIONES" && docs_exp[td] !="EXENCIÓN FORMACIÓN EN EMPRESAS (PFE)" && docs_exp[td] !="TRANSPORTE ESCOLAR") {
                            //contenido_div += "<td onclick='borraDocExp(this)' style='color:brown; text-align:center' width='20px' data-toggle='tooltip' data-placement='right' title='Borrar documento del expediente'>X</tr>";
                            contenido_div += "<td class='text-center'><button onclick='borraDocExp(this.parentNode)' class='textoboton btn btn-danger btn-sm' data-toggle='tooltip' data-placement='right' title='Borrar documento del expediente' style='color:white;font-weight:bold; font-size:1em !important'><i class='bi bi-trash'></i></button></td>";
                            contenido_div += "<td class='text-center'><button onclick='cambiaNomDocExp(this.parentNode)' class='textoboton btn btn-success btn-sm' data-toggle='tooltip' data-placement='right' title='Cambiar nombre del documento' style='color:white;font-weight:bold; font-size:1em !important'><i class='bi bi-pencil-square'></i></i></button></td>";
                        } else {
                            contenido_div += "<td colspan=3 style='color:brown; text-align:center' width='20px' data-placement='right'></td>";
                        }
                        contenido_div += "</tr>"
                    }
                }
            }
            contenido_div += "</table>";//<p style='text-align:center; color:brown'>FIN DE EXPEDIENTE</p>
            document.getElementById("div_tabla_expediente").innerHTML = contenido_div;
        }
    }, "json");
}


function borraDocExp(obj) {
    $("#div_dialogs2").load("html/secretaria.htm?q="+Date.now()+" #div_borra_doc", function(response,status, xhr){
        if ( status == "error" ) {
            var msg = "Error en la carga de procedimiento: " + xhr.status + " " + xhr.statusText;
            alerta(msg,"ERROR DE CARGA");
        }
        else{
            _del_ruta_completa = obj.parentElement.children[1].children[0].href;
            _del_ruta = ".." + _del_ruta_completa.substr(_del_ruta_completa.indexOf("/docs/"));
            _del_curso = obj.parentElement.children[0].innerHTML;
            _del_documento_pos = _del_ruta.indexOf(_del_curso);
            _del_documento = _del_ruta.substr(_del_documento_pos + 10);
            document.getElementById("doc_cod_seg").value = "";
            document.getElementById("del_ruta").value = _del_ruta;
            document.getElementById("del_documento").innerHTML = "Curso: " + _del_curso + " Nombre: " + _del_documento;
            cod_seg = Math.floor(Math.random() * 1000).toString();
            if (cod_seg.length < 4) {
                aux = "";
                for (i = cod_seg.length; i < 4; i++) {
                    aux += "0";
                }
                cod_seg = aux + cod_seg;
            }
            document.getElementById("doc_cod_seg").innerHTML = cod_seg;
            $("#div_dialogs2").dialog({
                autoOpen: true,
                dialogClass: "alert no-close",
                modal: true,
                hide: { effect: "fade", duration: 0 },
                resizable: false,
                show: { effect: "fade", duration: 0 },
                title: "BORRADO DE DOCUMENTO DEL EXPEDIENTE",
                maxHeight: 500,
                width: 550,
                close:function(event,ui){
                    $("#div_dialogs2").dialog("destroy");
                }
            });
        }
    });

}

function confirmadoBorradoDoc() {
    doc_ruta = document.getElementById("del_ruta").value;
    if (document.getElementById("doc_cod_seg").innerHTML == document.getElementById("t_doc_cod_seg").value) {
        $.post("php/secret_usu_borra_doc_exp.php", { ruta: doc_ruta }, function(resp) {
            document.getElementById("t_doc_cod_seg").value="";
            if (resp == "error") {
                alerta("No se ha podido borrar el documento.", "ERROR BORRADO");
            } else if (resp == "ok") {
                alerta("Documento borrado con éxito.", "BORRADO OK");
            }
            $('#div_dialogs2').dialog('close');
            obtieneDocsExpediente();
        });
    } else {
        document.getElementById("t_doc_cod_seg").value = "";
        alerta("Código introducido incorrecto.<br>No queda confirmado el borrado del documento.<br>Cancele o vuelva a introducir el código.", "BORRADO NO CONFIRMADO");
    }
}

function cambiaNomDocExp(obj) {
    $("#div_dialogs2").load("html/secretaria.htm?q="+Date.now()+" #div_camb_nom_doc", function(response,status, xhr){
        if ( status == "error" ) {
            var msg = "Error en la carga de procedimiento: " + xhr.status + " " + xhr.statusText;
            alerta(msg,"ERROR DE CARGA");
        }
        else{
            document.getElementById("nuevo_nom").value="";
            document.getElementById("tm_doc_cod_seg").value="";
            _rut_comp = obj.parentElement.children[1].children[0].href;
            _rut = ".." + _rut_comp.substr(_rut_comp.indexOf("/docs/"));
            _doc_curso = obj.parentElement.children[0].innerHTML;
            _doc_documento_pos = _rut.indexOf(_doc_curso);
            _doc_documento = _rut.substr(_doc_documento_pos + 10);
            document.getElementById("ruta_nuevo_nombre_doc").value = _rut.substr(0, _rut.length - _doc_documento.length);
            document.getElementById("ext_nuevo_nombre_doc").value = _rut.split('.').pop();
            document.getElementById("m_doc_cod_seg").value = "";
            document.getElementById("camb_ruta").value = _rut;
            document.getElementById("doc_documento").innerHTML = "Curso: " + _doc_curso + " Nombre: " + _doc_documento;
            cod_seg = Math.floor(Math.random() * 1000).toString();
            if (cod_seg.length < 4) {
                aux = "";
                for (i = cod_seg.length; i < 4; i++) {
                    aux += "0";
                }
                cod_seg = aux + cod_seg;
            }
            document.getElementById("m_doc_cod_seg").innerHTML = cod_seg;
            $("#div_dialogs2").dialog({
                autoOpen: true,
                dialogClass: "alert no-close",
                modal: true,
                hide: { effect: "fade", duration: 0 },
                resizable: false,
                show: { effect: "fade", duration: 0 },
                title: "CAMBIO DE NOMBRE DE DODUMENTO DEL EXPEDIENTE",
                maxHeight: 500,
                width: 550,
                close:function(event,ui){
                    $("#div_dialogs2").dialog("destroy");
                }
            });
        }
    });
}



function confirmaCambioNombreDoc() {
    doc_ruta = document.getElementById("camb_ruta").value;
    nuevo_n = document.getElementById("ruta_nuevo_nombre_doc").value + document.getElementById("nuevo_nom").value.trim() + "." + document.getElementById("ext_nuevo_nombre_doc").value;
    if (document.getElementById("nuevo_nom").value.trim().length == 0) {
        alerta("El nuevo nombre de archivo no puede estar vacío.", "ERROR DATOS");
        return;
    }
    if (/^[a-zA-Z0-9_-]+$/.test(document.getElementById("nuevo_nom").value.trim())==false){
        alerta("Sólo se permiten letras mayúsculas, minúsculas, números, guión y guión bajo.", "ERROR DATOS");
        return;
    }

    if (document.getElementById("m_doc_cod_seg").innerHTML == document.getElementById("tm_doc_cod_seg").value) {
        $.post("php/secret_usu_camb_nom_doc_exp.php", { ruta: doc_ruta, nuevo_n: nuevo_n }, function(resp) {
            document.getElementById("tm_doc_cod_seg").value="";
            if (resp == "error") {
                alerta("No se ha podido modificar el nombre del documento.", "ERROR MODIFICACIÓN");
            } else if (resp == "ok") {
                alerta("Modificado el nombre del documento.", "CAMBIO DE NOMBRE OK");
            } else if(resp="duplicado"){
                alerta("No se ha renomnbrado el archivo porque en la misma carpeta ya existe otro fichero con el mismo nombre", "ERROR DE DUPLICADO");
            }
            $('#div_dialogs2').dialog('close');
            obtieneDocsExpediente();
        });
    } else {
        document.getElementById("tm_doc_cod_seg").value = "";
        alerta("Código introducido incorrecto.<br>No queda confirmado el cambio de nombre del documento.<br>Cancele o vuelva a introducir el código.", "CAMBIO DE NOMBRE NO CONFIRMADO");
    }
}


function panelExpedienteUsuario(id_nie,nom) {
    $("#div_dialogs").load("html/secretaria.htm?q="+Date.now()+" #div_expediente_usuario", function(response,status, xhr){
        if ( status == "error" ) {
            var msg = "Error en la carga de procedimiento: " + xhr.status + " " + xhr.statusText;
            alerta(msg,"ERROR DE CARGA");
        }
        else{
            $("#nie_exp").html(id_nie);
            $("#nombre_exp").html(nom);
            document.getElementById("curso_exp").innerHTML="";
            document.getElementById("curso_exp").append(new Option("Todos", "todos"));
            cuenta_annos = anno_ini_curso_docs;
            if (mes == 6) cuenta_annos++;
            for (i = 2020; i <= cuenta_annos; i++) {
                var c = i + "-" + (i + 1);
                document.getElementById("curso_exp").append(new Option(c, c));
            }
            document.getElementById("curso_exp").selectIndex=0;
            obtieneDocsExpediente();
            $("#div_dialogs").dialog({
                autoOpen: true,
                dialogClass: "alert no-close",
                modal: true,
                hide: { effect: "fade", duration: 0 },
                resizable: false,
                show: { effect: "fade", duration: 0 },
                title: "EXPEDIENTE DEL USUARIO",
                width: 800,
                maxHeight:700,
                position: { my: "center top", at: "center top", of: window },
                buttons: [{
                    class: "btn btn-success textoboton",
                    text: "Cerrar",
                    click: function() {
                        $("#div_dialogs").dialog("close");
                        $("#div_dialogs").dialog("destroy");
                    }
                }],
                open: function(event, ui) {
                    $(".ui-dialog-titlebar-close").hide();
                }
            });
        }
    });  
}


function panelEnvioEmail(dir_email) {
    $("#div_dialogs").load("html/secretaria.htm?q="+Date.now()+" #div_email_usuario", function(response,status,xhr){
        if ( status == "error" ) {
            var msg = "Error en la carga de procedimiento: " + xhr.status + " " + xhr.statusText;
            alerta(msg,"ERROR DE CARGA");
        }
        else{
            exp_email = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            if (!exp_email.test(dir_email)) {
                alerta("Email incorrecto.", "ERROR");
                return;
            }
            validFormEmail=$("#form_email_usuario").validate({
                rules: {
                    usu_asunto_email: {
                        required: true
                    },
                    usu_cuerpo_email: {
                        required: true
                    }
                },
                messages: {
                    usu_asunto_email: {
                        required: "No puede dejar el asunto vacío."
                    },
                    usu_cuerpo_email: {
                        required: "No puede dejar vacío el cuerpo del mensaje."
                    }
                },
                errorPlacement: function(error, element) {
                    $(element).prev().prev().html(error);
                }
            });
            $("#div_dialogs").dialog({
                autoOpen: true,
                dialogClass: "alert no-close",
                modal: true,
                hide: { effect: "fade", duration: 0 },
                resizable: false,
                show: { effect: "fade", duration: 0 },
                title: "ENVÍO DE CORREO ELECTRÓNICO",
                width: 750,
                buttons: [{
                        class: "btn btn-success textoboton",
                        text: "Enviar",
                        click: function() {
                            asunto = document.getElementById("usu_asunto_email").value;
                            mensaje = document.getElementById("usu_cuerpo_email").value;
                            if (validFormEmail.form()) {
                                document.getElementById("cargando").style.display = "inherit";
                                $.post("php/secret_usu_enviaremail.php", { email: dir_email, asunto: asunto, mensaje: mensaje }, function() {
                                    document.getElementById("cargando").style.display = "none";
                                    alerta("Correo electrónico enviado.", "EMAIL");
                                    $("#div_dialogs").dialog("close");
                                });
                            }
                        }
                    },
                    {
                        class: "btn btn-success textoboton",
                        text: "Cancelar",
                        click: function() {
                            $("#div_dialogs").dialog("close");
                        }
                    }
                ],
                close: function(event, ui) {
                    $("#div_dialogs").dialog("destroy");
                }
            });
        }
    });

}

function eliminaUsuario(id, nom) {
    $("#div_dialogs").load("html/secretaria.htm?q="+Date.now()+" #div_elimina_usuario", function(response,status,xhr){
        if ( status == "error" ) {
            var msg = "Error en la carga de procedimiento: " + xhr.status + " " + xhr.statusText;
            alerta(msg,"ERROR DE CARGA");
        }
        else{
            document.getElementById("t_cod_seg").value = "";
            cod_seg = Math.floor(Math.random() * 1000).toString();
            document.getElementById("nie_eliminar").value = id;
            document.getElementById("id_usu_elim").innerHTML = id + " - " + nom;
            if (cod_seg.length < 4) {
                aux = "";
                for (i = cod_seg.length; i < 4; i++) {
                    aux += "0";
                }
                cod_seg = aux + cod_seg;
            }
            document.getElementById("cod_seg").innerHTML = cod_seg;
            $("#div_dialogs").dialog({
                autoOpen: true,
                dialogClass: "alert no-close",
                modal: true,
                hide: { effect: "fade", duration: 0 },
                resizable: false,
                show: { effect: "fade", duration: 0 },
                title: "ELIMINACIÓN DE USUARIO",
                maxHeight: 500,
                width: 550,
                close:function(event,ui){
                    $("#div_dialogs").dialog("destroy");
                }
            });
        }
    });
    
}


function confirmadoEliminarUsuario(nie_borrar) {
    if (document.getElementById("cod_seg").innerHTML == document.getElementById("t_cod_seg").value) {
        $.post("php/secret_usu_eliminausuario.php", { id: nie_borrar }, function(resp) {
            if (resp == "server") {
                alerta("Error de servidor. Inténtelo más tarde.", "ERROR EN SERVIDOR");
            } else if (resp == "error") {
                alerta("No se ha podido eliminar el usuario.", "ERROR PARCIAL BORRADO");
            } else if (resp == "error_parcial") {
                alerta("No se ha podido eliminar el usuario en alguna tabla.", "ERROR BORRADO");
            } else if (resp == "error_imagenes") {
                alerta("El usuario ha sido eliminado de la base de datos, pero alguna imagen asociada a él no se ha podido eliminar del servidor.", "ERROR BORRADO");
            } else {
                alerta("Usuario eliminado con éxito.", "BORRADO OK");
            }
            $('#div_dialogs').dialog('close');
            listaUsus();
            listaRegistros(_orden_campo, _orden_direccion);
        });
    } else {
        document.getElementById("t_cod_seg").value = "";
        alerta("Código introducido incorrecto.<br>No queda confirmada la eliminación del usuario.<br>Cancele o vuelva a introducir el código.", "ELIMINACIÓN NO CONFIRMADA");
    }
}

function panelModUsu(id) {
    $("#div_dialogs").load("html/secretaria.htm?q="+Date.now()+" #div_modif_datos_usu", function(response,status,xhr){
        if ( status == "error" ) {
            var msg = "Error en la carga de procedimiento: " + xhr.status + " " + xhr.statusText;
            alerta(msg,"ERROR DE CARGA");
        }
        else{
            document.getElementById("cargando").style.display = 'inherit';
            document.getElementById("dat_idnie").value = id;
            prom1 = Promise.resolve($.post("php/secret_usu_recdatusu.php", { id: document.getElementById("dat_idnie").value }, () => {}, "json"));
            prom2 = prom1.then((resp) => {
                if (resp.error == "server") alerta("Se ha producido un error en el servidor. Inténtelo más tarde", "ERROR DE SERVIDOR");
                else if (resp.error == "duplicado") alerta("Existe más de un usuario con el mismo NIE. Póngse en contacto con el programador de la plataforma.", "REGISTRO DUPLICADO");
                else if (resp.error == "no_existe") alerta("El usuario no se encuentra. Debe haber un problema en la base de datos.", "USUARIO INEXISTENTE");
                else if (resp.error == "ok") {
                    form_modif_datos_usu.mod_nombre.value = resp.registro.nombre;
                    form_modif_datos_usu.mod_apellidos.value = resp.registro.apellidos;
                    form_modif_datos_usu.mod_nif.value = resp.registro.nif;
                    form_modif_datos_usu.mod_email.value = resp.registro.email;
                }
                return $.post("php/usu_recdatospers.php", { id_nie: document.getElementById("dat_idnie").value }, () => {}, "json");
            });
            prom3 = prom2.then((resp) => {
                if (resp.error == "ok") {
                    for (e in resp.datos) {
                        if (typeof(resp.datos[e]) == "undefined" || resp.datos[e] == null) resp.datos[e] = "";
                    }
                    f_nac = resp.datos.fecha_nac;
                    if (f_nac != "") f_nac = f_nac.substr(8, 2) + "/" + f_nac.substr(5, 2) + "/" + f_nac.substr(0, 4);
                    form_modif_datos_usu.dat_sexo.value = resp.datos.sexo;
                    form_modif_datos_usu.dat_fecha_nac.value = f_nac;
                    form_modif_datos_usu.dat_telefono.value = resp.datos.telef_alumno;
                    form_modif_datos_usu.dat_nss.value = resp.datos.num_ss;
                    form_modif_datos_usu.dat_email.value = resp.datos.email;
                    form_modif_datos_usu.dat_direccion.value = resp.datos.direccion;
                    form_modif_datos_usu.dat_cp.value = resp.datos.cp;
                    form_modif_datos_usu.dat_localidad.value = resp.datos.localidad;
                    form_modif_datos_usu.dat_provincia.value = resp.datos.provincia;
                    form_modif_datos_usu.dat_tutor1.value = resp.datos.tutor1;
                    form_modif_datos_usu.dat_telef_tut1.value = resp.datos.tlf_tutor1;
                    form_modif_datos_usu.dat_email_tut1.value = resp.datos.email_tutor1;
                    form_modif_datos_usu.dat_tutor2.value = resp.datos.tutor2;
                    form_modif_datos_usu.dat_telef_tut2.value = resp.datos.tlf_tutor2;
                    form_modif_datos_usu.dat_email_tut2.value = resp.datos.email_tutor2;
                }
                document.getElementById("cargando").style.display = 'none';

                $("#dat_fecha_nac").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: "dd/mm/yy",
                    dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
                    firstDay: 1,
                    monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                    monthNameShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
                    showButtonPanel: true,
                    currentText: "Hoy",
                    closeText: "Cerrar",
                    minDate: new Date(2000, 0, 1),
                    maxDate: "-12y",
                    nextText: "Siguiente",
                    prevText: "Previo"
                });

                $("#form_modif_datos_usu").validate({
                    rules: {
                        mod_nombre: {
                            required: true
                        },
                        mod_apellidos: {
                            required: true
                        },
                        mod_email: {
                            email: true
                        }/*,
                        dat_email: {
                            email: true,
                            required:false
                        },
                        dat_email_tut1: {
                            email: true,
                            required:false
                        },
                        dat_email_tut2: {
                            email: true,
                            required:false
                        }*/
                    },
                    messages: {
                        mod_nombre: {
                            required: "No puede dejar el nombre en blanco"
                        },
                        mod_apellidos: {
                            required: "No puede dejar los apellidos en blanco"
                        },
                        mod_email: {
                            email: "Dirección de email no válida"
                        }/*,
                        dat_email: {
                            email: "No es una dirección de correo electrónico."
                        },
                        dat_email_tut1: {
                            email: "No es una dirección de correo electrónico."
                        },
                        dat_email_tut2: {
                            email: "No es una dirección de correo electrónico."
                        }*/
                    },
                    errorPlacement: function(error, element) {
                        $(element).prev($('.errorTxt')).html(error);
                    }
                });

                $("#div_dialogs").dialog({
                    autoOpen: true,
                    dialogClass: "alert no-close",
                    modal: true,
                    hide: { effect: "fade", duration: 0 },
                    resizable: false,
                    show: { effect: "fade", duration: 0 },
                    title: "MODIFICAR DATOS DE USUARIO",
                    maxHeight: 900,
                    width: 1100,
                    close: function(event,ui){
                        $("#dat_fecha_nac").datepicker("destroy");
                        $("#form_modif_datos_usu").validate().destroy();
                        $("#div_dialogs").dialog("destroy");
                    }
                });
                $("#div_dialogs").dialog("option", "title", "MODIFICAR DATOS DE " + form_modif_datos_usu.dat_idnie.value + "-" + form_modif_datos_usu.mod_apellidos.value + ", " + form_modif_datos_usu.mod_nombre.value);
            });
        }
    });

    
}


function modUsu() {
    var r1, r2;
    if ($("#form_modif_datos_usu").valid()) {
        prom1 = Promise.resolve($.post("php/secret_usu_modifdatos.php", $("#form_modif_datos_usu").serialize()));
        prom2 = prom1.then((resp1) => {
            r1 = resp1;
            return $.post("php/usu_moddatospers.php", $("#form_modif_datos_usu").serialize());
        });
        prom3 = prom2.then((resp2) => {
            r2 = resp2;
            if (r2 == "ok" && r1 == "ok") alerta("Datos de usuario modificados correctamente", "MODIFICACIÓN OK");
            else if (r1 == "server") alerta("Algunos datos  no se han podido modificar.", "ERROR EN TABLA USUARIOS");
            else if (r2 == "server") alerta("Algunos datos  no se han podido modificar.", "ERROR EN TABLA USUARIOS_DAT");
            else if (r2 == "server" && r1 == "server") alerta("No se han podido modificar los datos del usuario.", "ERROR BASE DE DATOS");
            else if (r1 == "fallo") alerta("La modificación del usuario no ha sido posible en todas las tablas.", "FALLO MODIFICACIÓN TABLA USUARIOS");
            else alerta(r2, "FALLO EN TABLA USUARIOS_DAT")
            $("#div_dialogs").dialog('close');
            listaUsus();
            listaRegistros(_orden_campo, _orden_direccion);
        });
    }
}


function subeDocExpediente(id, nom) {
    $("#div_dialogs").load("html/secretaria.htm?q="+Date.now()+" #div_sube_docs", function(response,status,xhr){
        if ( status == "error" ) {
            var msg = "Error en la carga de procedimiento: " + xhr.status + " " + xhr.statusText;
            alerta(msg,"ERROR DE CARGA");
        }
        else{
            generaSelectCurso(document.getElementById("curso_doc"));
            validFormSubeDoc = $("#form_sube_doc").validate({
                rules: {
                    tipo_doc: {
                        required: true
                    },
                    documento: {
                        required: true
                    }
                },
                messages: {
                    tipo_doc: {
                        required: "Debe seleccionar un tipo de documento."
                    },
                    
                    documento: {
                        required: "No hay seleccinado documento."
                    }
                },
                errorPlacement: function(error, element) {
                    $(element).prev().html(error);
                }
            });
            document.getElementById("id_nie").value = id;
            document.getElementById("curso_doc").value = anno_ini_curso_docs + "-" + (anno_ini_curso_docs + 1);
            if (id == "varios") {
                document.getElementById("documento").multiple = true;
                document.getElementById("form_sube_doc").enctype = "multipart/form-data";
                document.getElementById("tipo_envio").value = "multiple";
                document.getElementById("rotulo").innerHTML = "SUBIDA MASIVA DE DOCUMENTOS";
                $("option[data=simple]").hide();
                document.getElementById("div_nomArchOriginal").style.display="none";
            } else {
                document.getElementById("documento").multiple = false;
                document.getElementById("form_sube_doc").enctype = "application/x-www-form-urlencoded";
                document.getElementById("tipo_envio").value = "simple";
                document.getElementById("rotulo").innerHTML = "Subida al expediente de " + id + " - " + nom;
                document.getElementById("div_nomArchOriginal").style.display="inherit";
                document.getElementById("nomArchOriginal").disabled=false;
                $("option[data=simple]").show();
            }
            $("#div_dialogs").dialog({
                autoOpen: true,
                dialogClass: "alert no-close",
                modal: true,
                hide: { effect: "fade", duration: 0 },
                resizable: false,
                show: { effect: "fade", duration: 0 },
                title: "SUBIR DOCUMENTOS A EXPEDIENTE",
                maxHeight: 600,
                width: 600,
                close:function(event,ui){
                    $("#div_dialogs").dialog("destroy");
                }
            });
        }
    });
    
}

function selTipoDoc(obj) {
    /*if (obj.value == "Otro") {
        document.getElementById("div_nom_doc").style.display = "";
    } else {
        document.getElementById("div_nom_doc").style.display = "none";
    }*/
    if (obj.value == "Documento de Identificación-Anverso" || obj.value == "Documento de Identificación-Reverso" || obj.value == "Fotografía del alumno" || obj.value == "Seguro Escolar") {
        document.getElementById("documento").accept = "image/jpeg";
        document.getElementById("nom_doc").readOnly=true;
        document.getElementById("nomArchOriginal").checked=false;
        document.getElementById("nomArchOriginal").disabled=true;
        
    }
    else if(obj.value == "Certificado Notas"){
        document.getElementById("nom_doc").readOnly=false;
        document.getElementById("nomArchOriginal").disabled=false;
        bloqueaNomArch();
    }
    else if(obj.value == "Convalidaciones"){
        alerta("Para adjuntar al expediente la resolución de convalidaciones del Ministerio o Consejería u otro tipo de documento adjunto, debe hacerse a través del botón 'Adjuntar Documento', seleccionando la solicitud correspondiente del listado que aparece en  'Tipo de formulario: Convalidaciones' (en la pantalla inicial).","OPERACIÓN NO DISPONIBLE AQUÍ",false,500);
        obj.value="";
    }
    else if(obj.value=="exencion_fct"){
        alerta("Para adjuntar al expediente algun documento adicional relacionado con la exención de formación en empresas, debe hacerse a través del botón 'Adjuntar Documento', seleccionando la solicitud correspondiente del listado que aparece en  'Tipo de formulario: Exención PFE' (en la pantalla inicial).","OPERACIÓN NO DISPONIBLE AQUÍ",false,500);
        obj.value="";
    }
    else {
        document.getElementById("documento").accept = "application/pdf";
        document.getElementById("nom_doc").readOnly=false;
        document.getElementById("nomArchOriginal").disabled=false;
        bloqueaNomArch();
    }
}

function subeDoc() {
    if (!validFormSubeDoc.form()) return;
    td = document.getElementById("tipo_doc").value;
    if (td=="Otro" && document.getElementById("nom_doc").value.trim().length==0){
        alerta("Si el tipo de documento es OTRO hay que dar un nombre para el archivo.","ERROR CAMPO VACÍO");
        return;
    }
    if(!document.getElementById("nom_doc").disabled && document.getElementById("nom_doc").value.trim().length>0){
        if (/^[a-zA-Z0-9_-]+$/.test(document.getElementById("nom_doc").value.trim())==false){
            alerta("Sólo se permiten letras mayúsculas, minúsculas, números, guión y guión bajo.", "ERROR DATOS");
            return;
        }
    }
    if(document.getElementById("documento").files.length==0){
        alerta("No hay seleccionado ningún archivo.", "ERROR");
        return;
    }
    if (document.getElementById("tipo_envio").value == "simple") { 
        if (td == "Documento de Identificación-Anverso" || td == "Documento de Identificación-Reverso" || td == "Fotografía del alumno" || td == "Seguro Escolar") {
            if (document.getElementById("documento").files[0].type != "image/jpeg") {
                alerta("Formato de archivo no válido", "NO VÁLIDO");
                document.getElementById("documento").value = null;
                return;
            }
        } else if (document.getElementById("documento").files[0].type != "application/pdf") {
            alerta("Formato de archivo no válido", "NO VÁLIDO");
            document.getElementById("documento").value = null;
            return;
        }
        documento=document.getElementById("documento").files[0];
        
        if(document.getElementById("nomArchOriginal").checked){
            ultimoPunto = documento.name.lastIndexOf('.');
            if (ultimoPunto === -1) {
                // El archivo no tiene extensión
                nom_doc=documento.name;
            } else {
                nom_doc=documento.name.substring(0, ultimoPunto);
            }
            
            if (/^[a-zA-Z0-9_-]+$/.test(nom_doc)==false){
                alerta("Nombre de archivo incorrecto. Sólo se permiten letras mayúsculas, minúsculas, números, guión y guión bajo.", "ERROR NOMBRE ARCHIVO");
                return;
            }
        }
        else nom_doc=document.getElementById("nom_doc").value.trim();

        
        datos = new FormData();
        datos.set("documento", documento);
        datos.set("id_nie", document.getElementById("id_nie").value);
        datos.set("tipo_doc", td);
        datos.set("anno_curso", document.getElementById("curso_doc").value);
        datos.set("nom_doc", nom_doc);
        datos.set("tipo_envio", document.getElementById("tipo_envio").value);
        if (td == "Documento de Identificación-Anverso") datos.set("parte", "A");
        else if (td == "Documento de Identificación-Reverso") datos.set("parte", "R");
        tipoContenido = false;
    } else if (document.getElementById("tipo_envio").value == "multiple") {
        for (i = 0; i < document.getElementById("documento").files.length; i++) {
            if (document.getElementById("documento").files[i].type != "application/pdf") {
                alerta("Alguno de los archivos no tiene un formato válido.", "NO VÁLIDO");
                document.getElementById("documento").value = null;
                return;
            }
        }
        datos = new FormData(document.getElementById("form_sube_doc"));
        datos.set("anno_curso", document.getElementById("curso_doc").value);
        tipoContenido = "multipart/form-data";
    }
    if (document.getElementById("tipo_envio").value == "simple"){
        document.getElementById("cargando").style.display = 'inherit';
    }
    else {
        document.getElementById("progreso").style.display = 'inherit';
    }
    document.getElementById("bar_prog").style.width="0%";
    document.getElementById("num_procesados").innerHTML="";
    $.ajax({
            url: "php/secret_usu_subedoc.php",
            type: 'POST',
            data: datos,
            //contentType: tipoContenido,
            contentType: false,
            processData: false,
            cache: false,
            xhrFields:{
                onprogress: function(e) {
					if (document.getElementById("tipo_envio").value == "multiple"){
                        // Procesar mensajes de progreso
                        var progressLines = e.currentTarget.responseText.split("\n");
                        var progress = parseFloat(progressLines[progressLines.length - 2]);
                        document.getElementById("bar_prog").style.width=progress*100/document.getElementById("documento").files.length+"%";
                        document.getElementById("num_procesados").innerHTML="Procesados "+progress + " documentos de " + document.getElementById("documento").files.length;
                        // Actualizar barra de progreso
                        //$("#progress").css("width", progress + "%");
                    }	    
                }
            }
        })
        .done(function(resp) {
            resp=resp.split("\n").pop();
            document.getElementById("cargando").style.display = 'none';
            document.getElementById("progreso").style.display = 'none';
            if (document.getElementById("tipo_envio").value == "simple") {
                if (resp == "archivo") {
                    alerta("Ha habido un error al subir el archivo.", "ERROR CARGA");
                } else if (resp == "almacenar") {
                    alerta("Ha habido un error al copiar el archivo.", "ERROR COPIA");
                }
                else if(resp.split(".")[0]=="existe"){
                    alerta("Ya existe un documento con el mismo nombre. Al nombre del documento se le ha añadido '-"+resp.split(".").pop()+"'", "RENOMBRADO POR DUPLICADO");
                }
                else if (resp == "ok") {
                    alerta("Documento añadido al expediente.", "OK");
                }
            } else {
                if (resp == "ok") {
                    alerta("Todos los documentos subidos se han adjuntado a los expedientes.", "SUBIDA CORRECTA");
                } else if (resp == "archivo") {
                    alerta("Ha habido un error al subir los archivos.", "ERROR CARGA");
                } else if (resp.substr(0,5) == "logs/") {
                    alerta("Haz clic => <a href='"+resp+"' target='_blank'>Informe de Errores</a>", "PROCESO TERMINADO");
                    //var ventana = window.open('', '_blank');
                    //ventana.location.href = resp["pdf"];
                }
            }
            //$("#form_sube_doc").trigger("reset");
            document.getElementById("tipo_doc").value="";
            document.getElementById("nomArchOriginal").checked=false;
            document.getElementById("nomArchOriginal").disabled=false;
            document.getElementById("nom_doc").readOnly=false;
            document.getElementById("nom_doc").value="";
            document.getElementById("nom_doc").disabled=false;
        });
}

function bloqueaNomArch(){
    document.getElementById("nom_doc").disabled=document.getElementById("nomArchOriginal").checked;
    obj=document.getElementById("tipo_doc");
    if (obj.value == "Documento de Identificación-Anverso" || obj.value == "Documento de Identificación-Reverso" || obj.value == "Fotografía del alumno" || obj.value == "Seguro Escolar") {
        document.getElementById("nom_doc").disabled=true;
    }
}


function adjuntosConvalid(registro,procedimiento){
    registro_adjuntos_convalid=registro.slice(0, -4);
    document.getElementById("cargando").style.display = '';
    if (procedimiento=="convalidaciones") url="php/secret_convalid_adjuntos.php";
    else if(procedimiento=="exencion_fct") url="php/secret_exencion_fct_adjuntos.php";
    $.post(url,{registro:registro_adjuntos_convalid},(resp)=>{
        document.getElementById("cargando").style.display = 'none';
        contenido = "<span class='verReg_label'>DOCUMENTOS ADJUNTOS de "+registro+"</span><br>";
        if(resp.error=="server") contenido += "<span class='verReg_label'>Hay un problema en sel servidor y no se han podido recuperar los documentos adjuntos.</span>";
        else if(resp.error=="sin_adjuntos") contenido += "<span class='verReg_label'>No hay documentos adjuntos a la solicitud.</span>";
        else {
            contenido+="";
            for(i=0;i<resp.datos.length;i++){
                contenido += "<a style='color:GREEN;font-size:0.75em; margin-right:10px;' target='_blank' href='"+resp.datos[i].ruta+"'>"+resp.datos[i].descripcion+"</a>";
                if (resp.datos[i].subidopor=="secretaria"){
                    contenido += "<button onclick='borraAdjuntos(\""+resp.datos[i].ruta+"\",\""+resp.datos[i].descripcion+"\",\""+registro+"\",0)' class='textoboton btn btn-danger' data-toggle='tooltip' data-placement='right' title='Borrar adjunto de convalidación' style='color:white;font-weight:bold; font-size:0.5em !important'><i class='bi bi-trash'></i></button>";
                }
                contenido += "<br>";            
            }
        }
        document.getElementById("div_dialogs_adjuntosconvalid").innerHTML=contenido;
        $("#div_dialogs_adjuntosconvalid").dialog({
            autoOpen: true,
            dialogClass: "alert no-close",
            modal: true,
            hide: { effect: "fade", duration: 0 },
            resizable: false,
            show: { effect: "fade", duration: 0 },
            title: "ADJUNTOS DE CONVALIDACIÓN",
            width: 600,
            position: { my: "center top", at: "center top", of: window },
            buttons: [{
                class: "btn btn-success textoboton",
                text: "Cerrar",
                click: function() {
                    $("#div_dialogs_adjuntosconvalid").dialog("close");
                    $("#div_dialogs_adjuntosconvalid").dialog("destroy");
                }
            }]
        });
    },"json");
}

function borraAdjuntos(procedimiento,ruta,descripcion,registro,refrescaDocs){
    $("#div_dialogs2").load("html/secretaria.htm?q="+Date.now()+" #div_borra_adjuntosconvalid", function(response,status, xhr){
        if ( status == "error" ) {
            var msg = "Error en la carga de procedimiento: " + xhr.status + " " + xhr.statusText;
            alerta(msg,"ERROR DE CARGA");
        }
        else{
            _del_ruta = "../" + ruta;
            document.getElementById("doc_cod_seg").value = "";
            document.getElementById("del_ruta").value = _del_ruta;
            document.getElementById("registro").value = registro;
            document.getElementById("refresca_docs").value = refrescaDocs;
            document.getElementById("del_documento").innerHTML = descripcion;
            document.getElementById("tipo_procedimiento").value = procedimiento;
            cod_seg = Math.floor(Math.random() * 1000).toString();
            if (cod_seg.length < 4) {
                aux = "";
                for (i = cod_seg.length; i < 4; i++) {
                    aux += "0";
                }
                cod_seg = aux + cod_seg;
            }
            document.getElementById("doc_cod_seg").innerHTML = cod_seg;
            if (procedimiento=="convalidaciones_docs") titulo="BORRADO DE DOCUMENTO ADJUNTO DE CONVALIDACIÓN";
            else if(procedimiento=="exencion_fct_docs") titulo="BORRADO DE DOCUMENTO ADJUNTO DE EXENCIÓN DE PFE";
            else titulo="";
            $("#div_dialogs2").dialog({
                autoOpen: true,
                dialogClass: "alert no-close",
                modal: true,
                hide: { effect: "fade", duration: 0 },
                resizable: false,
                show: { effect: "fade", duration: 0 },
                title: titulo,
                maxHeight: 500,
                width: 550,
                close:function(event,ui){
                    $("#div_dialogs2").dialog("destroy");
                }
            });
        }
    });
}

function confirmadoBorradoAdjunto(procedim) {
    registro=document.getElementById("registro").value;
    registro_adjuntos_exenc_fct=registro.slice(0, -4);
    refresca_docs=document.getElementById("refresca_docs").value;
    doc_ruta = document.getElementById("del_ruta").value;
    if (document.getElementById("doc_cod_seg").innerHTML == document.getElementById("t_doc_cod_seg").value) {
        $.post("php/secret_usu_borra_adjunto.php", {tabla:procedim, ruta: doc_ruta }, function(resp) {
            document.getElementById("t_doc_cod_seg").value="";
            if (resp == "error") {
                alerta("No se ha podido borrar el documento.", "ERROR BORRADO");
            } else if (resp == "ok") {
                alerta("Documento borrado con éxito.", "BORRADO OK");
                if (refresca_docs=='0'){
                    if (procedim=="convalidaciones_docs") regeneraListaAdjuntosConvalid();
                    else if (procedim=="exencion_fct_docs") regeneraListaAdjuntosExencFCT();
                }
                else {
                    if (procedim=="convalidaciones_docs") verRegAdjuntosConvalid(registro);
                    else if (procedim=="exencion_fct_docs") verRegAdjuntosExencFCT(registro);
                    listaRegistros();
                }
            }
            else if (resp == "server") {
                alerta("Documento adjunto no eliminado, porque no se ha podido eliminar el registro asociado en la base de datos", "ERROR DB");
            }
            $('#div_dialogs2').dialog('close');
        });
    } else {
        document.getElementById("t_doc_cod_seg").value = "";
        alerta("Código introducido incorrecto.<br>No queda confirmado el borrado del documento.<br>Cancele o vuelva a introducir el código.", "BORRADO NO CONFIRMADO");
    }
}

function regeneraListaAdjuntosConvalid(){
    document.getElementById("cargando").style.display = 'inherit';
    $.post("php/secret_convalid_adjuntos.php",{registro:registro_adjuntos_convalid},(resp)=>{
        document.getElementById("cargando").style.display = 'none';
        contenido = "<span class='verReg_label'>DOCUMENTOS ADJUNTOS de iesulabto_convcm_"+registro_adjuntos_convalid+"</span><br>";
        if(resp.error=="server") contenido += "<span class='verReg_label'>Hay un problema en sel servidor y no se han podido recuperar los documentos adjuntos.</span>";
        else if(resp.error=="sin_adjuntos") contenido += "<span class='verReg_label'>No hay documentos adjuntos a la solicitud.</span>";
        else {
            for(i=0;i<resp.datos.length;i++){
                contenido += "<button onclick='borraAdjuntos(\""+resp.datos[i].ruta+"\")' class='textoboton btn btn-danger' data-toggle='tooltip' data-placement='right' title='Borrar documento del expediente' style='color:white;font-weight:bold; font-size:1em !important'><i class='bi bi-trash'></i></button>";
                contenido += "<a style='color:GREEN;font-size:0.75em;margin-left:10px;' target='_blank' href='"+resp.datos[i].ruta+"'>"+resp.datos[i].descripcion+"</a><br>";
            }
        }
        document.getElementById("div_dialogs_adjuntosconvalid").innerHTML=contenido;
    },"json");
}


function regeneraListaAdjuntosExencFCT(){
    document.getElementById("cargando").style.display = 'inherit';
    $.post("php/secret_exencion_fct_adjuntos.php",{registro:registro_adjuntos_exenc_fct},(resp)=>{
        document.getElementById("cargando").style.display = 'none';
        contenido = "<span class='verReg_label'>DOCUMENTOS ADJUNTOS de iesulabto_convcm_"+registro_adjuntos_exenc_fct+"</span><br>";
        if(resp.error=="server") contenido += "<span class='verReg_label'>Hay un problema en sel servidor y no se han podido recuperar los documentos adjuntos.</span>";
        else if(resp.error=="sin_adjuntos") contenido += "<span class='verReg_label'>No hay documentos adjuntos a la solicitud.</span>";
        else {
            for(i=0;i<resp.datos.length;i++){
                contenido += "<button onclick='borraAdjuntos(\""+resp.datos[i].ruta+"\")' class='textoboton btn btn-danger' data-toggle='tooltip' data-placement='right' title='Borrar documento del expediente' style='color:white;font-weight:bold; font-size:1em !important'><i class='bi bi-trash'></i></button>";
                contenido += "<a style='color:GREEN;font-size:0.75em;margin-left:10px;' target='_blank' href='"+resp.datos[i].ruta+"'>"+resp.datos[i].descripcion+"</a><br>";
            }
        }
        document.getElementById("div_dialogs_adjuntosconvalid").innerHTML=contenido;
    },"json");
}




function descargarExpediente(id,nom){
    nom = nom.replace(/,/g, '_');
    document.getElementById("cargando").style.display = 'inherit';
    formDownload=document.createElement("form");
    formDownload.id="form_downloadExp";
    formDownload.method="POST";
    formDownload.action="php/secret_usu_download_expediente.php";

    input_id=document.createElement("input");
    input_id.type="hidden";
    input_id.name="id_down";
    input_id.value=id;
    formDownload.appendChild(input_id);
    input_nom=document.createElement("input");
    input_nom.type="hidden";
    input_nom.name="nombre_down";
    input_nom.value=nom;
    formDownload.appendChild(input_nom);
    document.body.appendChild(formDownload);
    formDownload.submit();
    document.body.removeChild(formDownload);
    document.getElementById("cargando").style.display = 'none';
}

function pdfEVAU(id,nom){
    nom = nom.replace(/,/g, '_');
    document.getElementById("cargando").style.display = 'inherit';
    formDownload=document.createElement("form");
    formDownload.id="form_downloadExp";
    formDownload.method="POST";
    formDownload.action="php/secret_usu_generapdf_evau.php";

    input_id=document.createElement("input");
    input_id.type="hidden";
    input_id.name="id_down";
    input_id.value=id;
    formDownload.appendChild(input_id);
    input_nom=document.createElement("input");
    input_nom.type="hidden";
    input_nom.name="nombre_down";
    input_nom.value=nom;
    formDownload.appendChild(input_nom);
    document.body.appendChild(formDownload);
    formDownload.submit();
    document.body.removeChild(formDownload);
    document.getElementById("cargando").style.display = 'none';
}