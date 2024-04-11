var actualizar = false;
campos = new Array();
encabezamiento = new Array();
var anno_ini_curso = 0;
var mes;
var _orden_campo;
var _orden_direccion;
var incidencia_si = 0;
var curso_actual;
var sesion_id;
var num_registros;
var num_reg_pagina = 25;
var numero_paginas;
var pagina = 1;
var orden_direccion_usu = "🡅";



$(function() {
    if (document.location.hostname!="registro.ulaboral.org")document.getElementById("servidor_pruebas").style.display="inherit";
    else document.getElementById("servidor_pruebas").style.display="none";
   
    document.getElementById("cargando").style.display = 'inherit';
    prom1=Promise.resolve($.post("php/sesion.php", { tipo_usu: "residencia" },()=>{},"json"));
    prom2=prom1.then((resp)=> {
        if (resp["error"] != "ok") document.write(resp["error"]);
        else {
            document.getElementById("rotulo_tipo_usu").innerHTML="RESIDENCIA - GESTIÓN DEL REGISTRO ONLINE"; 
            anno_ini_curso = resp["anno_ini_curso"];
            anno_ini_curso_docs = resp["anno_ini_curso_docs"];
            mes = resp["mes"];
            _curso = anno_ini_curso + "-" + (anno_ini_curso + 1);
            curso_actual=_curso;
            generaSelectCurso();
            document.getElementById("curso").value = _curso;
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


function ordenListado(obj) {
    if (obj.innerHTML == "Docs" || obj.innerHTML == "Incidencias" || obj.innerHTML == "Listado") return;
    if (obj.innerHTML.indexOf("🡅") == -1 && obj.innerHTML.indexOf("🡇") == -1) {
        enc = obj.innerHTML;
        sim_dir = "🡅";
    } else {
        enc = obj.innerHTML.substring(0, obj.innerHTML.length - 3);
        sim_dir = obj.innerHTML.substring(obj.innerHTML.length - 2, obj.innerHTML.length);
        if (sim_dir == "🡅") sim_dir = "🡇";
        else if (sim_dir == "🡇") sim_dir = "🡅";
    }
    campo = campos[encabezamiento.indexOf(obj.innerHTML)];
    if (campo == "nombre") campo = "apellidos";
    _orden_campo = campo;
    _orden_direccion = sim_dir;
    listaRegistros(campo, sim_dir);
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

    estilo_usu = ["width:80px", "width:270px", "width:270px", "width:90px", "width:40px", "width:80px"];
    encabezamiento_usu = ["NIE", "Alumno", "Email", "Residente", "No Matr."];

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
        tipo_residente:document.getElementById("tipo_residente").value
    }
    $.post("php/residencia_listausuarios.php", datos, function(resp) {
        if (resp.error == "server") alerta("Error en el servidor. Inténtalo más tarde.", "Error de servidor");
        else if (resp.error == "sin_registros") {
            document.getElementById("div_notabla_usus").style.display = "inline-block";
            document.getElementById("div_tabla_usus").style.display = "none";
        } else {
            document.getElementById("div_notabla_usus").style.display = "none";
            document.getElementById("div_tabla_usus").style.display = "inline-block";
            data = "";
            data_array = resp["registros"];
            for (i = 0; i < data_array.length; i++) {
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
                data += "<td style='" + estilo_usu[4] + ";text-align:center' ";
                //columna EXPEDIENTE
                data += "<td style='" + estilo_usu[5] + ";text-align:center' ";
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


function panelEnvioEmail(dir_email) {
    $("#div_dialogs").load("html/secretaria.txt #div_email_usuario", function(response,status,xhr){
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
