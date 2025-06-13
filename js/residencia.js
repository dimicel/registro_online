res_campos = new Array();
res_encabezamiento = new Array();
var res_anno_ini_curso = 0;
var res_mes;
var res_orden_campo;
var res_orden_direccion;
var res_curso_actual;
var res_num_registros;
var res_num_reg_pagina = 25;
var res_numero_paginas;
var res_pagina = 1;
var res_orden_direccion_usu = "🡅";



$(function() {
    if (document.location.hostname!="registro.ulaboral.org")document.getElementById("servidor_pruebas").style.display="inherit";
    else document.getElementById("servidor_pruebas").style.display="none";
   
    mostrarPantallaEspera();
    $.post("php/secret_recupera_nombre_centro.php",{},(resp)=>{
        document.getElementById("centro").innerHTML=resp["registro"]["centro"].toUpperCase();
        document.getElementById("titulo").innerHTML+=resp["registro"]["centro"].toUpperCase();
    },"json");
    prom1=Promise.resolve($.post("php/sesion.php", { tipo_usu: "residencia" },()=>{},"json"));
    prom2=prom1.then((resp)=> {
        if (resp["error"] != "ok") document.write(resp["error"]);
        else {
            if (resp["tipo_usu"]=="secretaria"){
                document.getElementById("secretaria").style.display='';
                document.getElementById("csv_remesas").style.display='';
                document.getElementById("boton_salir").style.display='none';
            }
            if (document.getElementById("rotulo_tipo_usu")) document.getElementById("rotulo_tipo_usu").innerHTML="RESIDENCIA - GESTIÓN DEL REGISTRO ONLINE"; 
            document.getElementById("res_rotulo_tipo_usu").innerHTML="RESIDENCIA - GESTIÓN DEL REGISTRO ONLINE"; 
            res_anno_ini_curso = resp["anno_ini_curso"];
            res_mes = resp["mes"];
            _curso = res_anno_ini_curso + "-" + (res_anno_ini_curso + 1);
            res_curso_actual=_curso;
            res_generaSelectCurso();
            document.getElementById("res_curso").value = _curso;

            $('#res_navegacion_usus_top,#res_navegacion_usus_bottom').bootpag({
                total: 1,
                page: res_pagina,
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
                res_pagina = num;
                res_listaUsus();
            });
            $('#res_navegacion_usus_top li').addClass('page-item');
            $('#res_navegacion_usus_top a').addClass('page-link');
            $('#res_navegacion_usus_bottom li').addClass('page-item');
            $('#res_navegacion_usus_bottom a').addClass('page-link');

            res_listaUsus();
            ocultarPantallaEspera();
        } 
    });

    
    
});

function res_generaSelectCurso(){
    if (res_mes<6) a_final=res_anno_ini_curso;
    else a_final=res_anno_ini_curso+1;

    const miSelect = document.getElementById("res_curso");
    for (var i=2020;i<=a_final;i++){
        const elemento = document.createElement("option");
        elemento.value = i+"-"+(parseInt(i)+1);
        elemento.textContent = elemento.value;
        miSelect.appendChild(elemento);
    }
}


/*function ordenListado(obj) {
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
    campo = res_campos[res_encabezamiento.indexOf(obj.innerHTML)];
    if (campo == "nombre") campo = "apellidos";
    res_orden_campo = campo;
    res_orden_direccion = sim_dir;
    res_listaUsus(campo, sim_dir);
}*/


function res_cierrasesion() {
    $.post("php/logout.php", {}, function(resp) {
        open("index.php?q=" + Date.now().toString(), "_self");
    });
}

function res_listaUsus() {
    direccion = new Array();
    direccion["🡅"] = "ASC";
    direccion["🡇"] = "DESC";

    estilo_usu = ["width:80px", "width:220px", "width:220px", "width:80px;text-align:center","width:120px;text-align:center","width:60px;text-align:center","width:70px;text-align:center","width:40px;text-align:center"];
    encabezamiento_usu = ["NIE", "Alumno", "Email", "Bonificado","Devolución Fianza(€)","Baja","Fecha Baja","SEPA"];

    //Construcción del res_encabezamiento de la tabla
    encab_usus = "<tr>";
    for (i = 0; i < encabezamiento_usu.length; i++) {
        if (encabezamiento_usu[i] == "Alumno") encab_usus += "<td style='" + estilo_usu[i] + "'onclick='res_ordenUsus()'>" + encabezamiento_usu[i] + " " + res_orden_direccion_usu + "</td>";
        else encab_usus += "<td style='" + estilo_usu[i] + "'>" + encabezamiento_usu[i] + "</td>";
    }
    ///////////////////////////////////////////////
    datos = {
        res_buscar: document.getElementById("res_busqueda_usus").value,
        res_orden_direccion_usu: direccion[res_orden_direccion_usu],
        res_pagina: res_pagina,
        res_num_reg_pagina: res_num_reg_pagina,
        res_curso:document.getElementById("res_curso").value,
        filtro_bajas:document.getElementById("filtro_bajas").value
    }
    $.post("php/residencia_listausuarios.php", datos, function(resp) {
        if (resp.error == "server") alerta("Error en el servidor. Inténtalo más tarde.", "Error de servidor");
        else if (resp.error == "sin_registros") {
            document.getElementById("div_res_notabla_usus").style.display = "inline-block";
            document.getElementById("div_res_tabla_usus").style.display = "none";
            res_numero_paginas=1;
            res_pagina=1;
            $('#res_navegacion_usus_top,#res_navegacion_usus_bottom').bootpag({
                total: res_numero_paginas
            });
            $('#res_navegacion_usus_top li').addClass('page-item');
            $('#res_navegacion_usus_top a').addClass('page-link');
            $('#res_navegacion_usus_bottom li').addClass('page-item');
            $('#res_navegacion_usus_bottom a').addClass('page-link');
        } else {
            document.getElementById("div_res_notabla_usus").style.display = "none";
            document.getElementById("div_res_tabla_usus").style.display = "inline-block";
            data = "";
            data_array = resp["registros"];
            for (i = 0; i < data_array.length; i++) {
                data += "<tr>";
                data += "<td style='" + estilo_usu[0] + "'>" + data_array[i]["id_nie"] + "</td>";
                data += "<td style='" + estilo_usu[1] + "'><a href='docs/"+data_array[i]["id_nie"]+"/residencia/"+document.getElementById("res_curso").value+"/"+data_array[i]["registro"]+".pdf' target='_blank'>" + data_array[i]["nombre"] + "</a></td>";
                data += "<td style='" + estilo_usu[2] + "'><a href='javascript:void(0)' onclick='res_panelEnvioEmail(\"" + data_array[i]["email"] + "\")'>" + data_array[i]["email"] + "</a></td>";
                if (data_array[i]["bonificado"]==1){
                    data += "<td style='" + estilo_usu[3] + ";text-align:center' ondblclick='estadoBonificado(\""+data_array[i]["registro"]+"\",this)'>SÍ</td>";
                }
                else{
                    data += "<td style='" + estilo_usu[3] + ";text-align:center' ondblclick='estadoBonificado(\""+data_array[i]["registro"]+"\",this)'>NO</td>";
                }
                data += "<td style='" + estilo_usu[4] + ";text-align:center' ondblclick='fianza(\""+data_array[i]["registro"]+"\",this)'>" + data_array[i]["devolucion_fianza"] + "</td>";
                if (data_array[i]["baja"]==1){
                    data += "<td style='" + estilo_usu[5] + ";text-align:center' ondblclick='altaBaja(\""+data_array[i]["registro"]+"\",this)'>SÍ</td>";
                }
                else{
                    data += "<td style='" + estilo_usu[5] + ";text-align:center' ondblclick='altaBaja(\""+data_array[i]["registro"]+"\",this)'>NO</td>";
                }
                let partes = data_array[i]["fecha_baja"].split('-');
                let fechaConvertida = partes[2] + '-' + partes[1] + '-' + partes[0];
                if (data_array[i]["baja"]==1){
                    data += "<td style='" + estilo_usu[6] + ";text-align:center'>"+fechaConvertida+"</td>";
                }
                else{
                    data += "<td style='" + estilo_usu[6] + ";text-align:center'>-</td>";
                }
                if (data_array[i]["sepa"]!=""){
                    data += "<td style='" + estilo_usu[7] + ";text-align:center'><a href='"+data_array[i]["sepa"]+"' target='_blank'>Ver</a></td>";
                }
                else{
                    data += "<td style='" + estilo_usu[7] + ";text-align:center'>-</td>";
                }
                data += "</tr>";
            }
            document.getElementById("res_encabezado_usus").innerHTML = encab_usus;
            document.getElementById("res_registros_usus").innerHTML = data;
            res_num_registros = resp.num_registros;
            res_numero_paginas = Math.ceil(res_num_registros / res_num_reg_pagina);
            if (res_pagina > res_numero_paginas) res_pagina = res_numero_paginas;
            
            $('#res_navegacion_usus_top,#res_navegacion_usus_bottom').bootpag({
                total: res_numero_paginas
            });
            $('#res_navegacion_usus_top li').addClass('page-item');
            $('#res_navegacion_usus_top a').addClass('page-link');
            $('#res_navegacion_usus_bottom li').addClass('page-item');
            $('#res_navegacion_usus_bottom a').addClass('page-link');
        }
    }, "json");
}



function res_ordenUsus() {
    if (res_orden_direccion_usu == "🡅") res_orden_direccion_usu = "🡇";
    else res_orden_direccion_usu = "🡅";
    res_listaUsus();
}


function res_panelEnvioEmail(dir_email) {
    mostrarPantallaEspera("Cargando ...");
    cargaHTML("html/secretaria_usu.htm", "div_email_usuario","ENVÍO DE CORREO ELECTRÓNICO",750,2000,"center center","center center",
        [{text:"Cancelar",
            class: "textoboton btn btn-success btn-sm",
            click:function(){
                $(this).closest('.ui-dialog-content').dialog("close");
            }
        }, 
        {text:"Enviar",
            class: "textoboton btn btn-success btn-sm",
            click:function() {
                asunto = document.getElementById("usu_asunto_email").value;
                mensaje = document.getElementById("usu_cuerpo_email").value;
                const _dialog = $(this).closest('.ui-dialog-content');
                if (validFormEmail.form()) {
                    mostrarPantallaEspera();
                    $.post("php/secret_usu_enviaremail.php", { email: dir_email, asunto: asunto, mensaje: mensaje }, function() {
                        ocultarPantallaEspera();
                        alerta("Correo electrónico enviado.", "EMAIL");
                        _dialog.dialog("close");
                    });
                }
            }    
        }]).then((dialogo)=>{
            ocultarPantallaEspera();
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
    })
    .catch (error=>{
        ocultarPantallaEspera();
        var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
        alerta(msg,"ERROR DE CARGA");
    });

}


function cambioEmailJefeRes(){
    mostrarPantallaEspera("Cargando ...");
    cargaHTML("html/residencia.htm", "div_cambio_email_jef_res","CAMBIO DE DATO",600,2000,"center center","center center",
        [
            {
                class: "btn btn-success textoboton",
                text: "Guardar Cambios",
                click: function() {
                    if ($("#cambio_email_jef_res").valid()){
                        mostrarPantallaEspera();
                        $.post({
                            url:"php/residencia_actualiza_email_jr.php" ,
                            data: $("#cambio_email_jef_res").serialize(),
                            success: function(resp) {
                                ocultarPantallaEspera();
                                if (resp == "servidor") alerta("Hay un problema con el servidor. Inténtelo más tarde.", "ERROR SERVIDOR");
                                else if (resp == "database") alerta("No se actualizó ningún registro. Es posible que el valor no haya cambiado.", "FALLO AL ACTUALIZAR");
                                else if (resp == "ok"){
                                    alerta("Email actualizado correctamente.","ACTUALIZACIÓN CORRECTA");
                                }
                                else{
                                    alerta(resp,"ERROR");
                                }
                                $(this).closest('.ui-dialog-content').dialog("destroy").remove();
                            },
                            error: function(xhr, status, error) {
                                ocultarPantallaEspera();
                                alerta("Error en servidor. Código " + error + "<br>Inténtelo más tarde.", "ERROR DE SERVIDOR");
                                $(this).closest('.ui-dialog-content').dialog("destroy").remove();
                            }
                        });
                    }
                    
                }
            },
            {
            class: "btn btn-success textoboton",
            text: "Cancelar",
            click: function() {
                $(this).closest('.ui-dialog-content').dialog("destroy").remove();
            }
        }]
    )
    .then((dialogo)=>{
        $.post("php/secret_recupera_param_centro.php",{},(resp)=>{
            ocultarPantallaEspera();
            if (resp.error=="ok"){
                document.getElementById("email_jr").value=resp.registro.email_jefe_residencia;
                $("#cambio_email_jef_res").validate({
                    rules: {
                        email_jr: {
                            email:true,
                            required:true
                        }
                    },
                    messages: {
                        email_jr:{
                            email:"Dirección no válida",
                            required: "Complete el campo"
                        }
                    },
                    errorPlacement: function(error, element) {
                        $(element).prev().prev($('.errorTxt')).html(error);
                    }
                });
            }
            else if (resp.datos=="server"){
                alerta("Error en servidor. No se puede cambiar el email del Jefe de Residencia","ERROR SERVIDOR");
                $(dialogo).dialog("destroy").remove();
            }
        },"json");
    })
    .catch (error=>{
        ocultarPantallaEspera();
        var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
        alerta(msg,"ERROR DE CARGA");
    });
}


function estadoBonificado(__registro,celda){
    mostrarPantallaEspera("Cargando ...");
    cargaHTML("","","CAMBIO ESTADO BONIFICADO/NO BONIFICADO",600,2000,"center center","center center",
        [
            {
                class: "btn btn-success textoboton",
                text: "Confirmar cambio",
                click: function() {
                    mostrarPantallaEspera();
                    obj=this;
                    $.post({
                        url:"php/residencia_cambio_estado_bonificado.php" ,
                        data: {registro:__registro,bonificado:bonificado},
                        success: function(resp) {
                            ocultarPantallaEspera();
                            if (resp == "servidor") alerta("Hay un problema con el servidor. Inténtelo más tarde.", "ERROR SERVIDOR");
                            else if (resp == "database") alerta("No se actualizó ningún registro. Es posible que el valor no haya cambiado.", "FALLO AL ACTUALIZAR");
                            else if (resp == "ok"){
                                alerta("Cambio de estado realizado correctamente.","ACTUALIZACIÓN CORRECTA");
                                res_listaUsus();
                            }
                            else{
                                alerta(resp,"ERROR");
                            }
                            $(obj).closest(".ui-dialog-content").dialog("destroy").remove();
                        },
                        error: function(xhr, status, error) {
                            ocultarPantallaEspera();
                            alerta("Error en servidor. Código " + error + "<br>Inténtelo más tarde.", "ERROR DE SERVIDOR");
                            $(obj).closest(".ui-dialog-content").dialog("destroy").remove();
                        }
                    });
                }
            },
            {
            class: "btn btn-success textoboton",
            text: "Cancelar",
            click: function() {
                $(this).closest(".ui-dialog-content").dialog("destroy");
            }
        }]
    ).then((dialogo)=>{
            ocultarPantallaEspera();
            if (celda.innerHTML=="NO"){
                mensaje="<p>Va a cambiar el estado del residente de NO BONIFICADO a BONIFICADO.</p>";
                bonificado=1;
            }
            else{
                mensaje="<p>Va a cambiar el estado del residente de BONIFICADO a NO BONIFICADO.</p>";
                bonificado=0;
            }
            dialogo.innerHTML=mensaje;
    })
    .catch (error=>{
        ocultarPantallaEspera();
        var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
        alerta(msg,"ERROR DE CARGA");
    });
    
}

function altaBaja(__registro,celda){
    mostrarPantallaEspera("Cargando ...");
    cargaHTML("","","ALTA/BAJA DE RESIDENTE",400,2000,"center center","center center",
        [
            {
                class: "btn btn-success textoboton",
                text: "Confirmar cambio",
                click: function() {
                    if (baja==1) if (!$("#form_baja").valid()) return;
                    if (baja==1) fecha_baja=document.getElementById('fech_baja').value;
                    else fecha_baja="";
                    mostrarPantallaEspera();
                    obj=this
                    $.post({
                        url:"php/residencia_alta_baja.php",
                        data: {registro:__registro,baja:baja,fecha_baja:fecha_baja},
                        success: function(resp) {
                            ocultarPantallaEspera();
                            if (resp == "servidor") alerta("Hay un problema con el servidor. Inténtelo más tarde.", "ERROR SERVIDOR");
                            else if (resp == "database") alerta("No se actualizó ningún registro. Es posible que el valor no haya cambiado.", "FALLO AL ACTUALIZAR");
                            else if (resp == "ok"){
                                alerta("Cambio de estado realizado correctamente.","ACTUALIZACIÓN CORRECTA");
                                res_listaUsus();
                            }
                            else{
                                alerta(resp,"ERROR");
                            }
                            $(obj).closest(".ui-dialog-content").dialog("destroy").remove();
                        },
                        error: function(xhr, status, error) {
                            ocultarPantallaEspera();
                            alerta("Error en servidor. Código " + error + "<br>Inténtelo más tarde.", "ERROR DE SERVIDOR");
                            $(obj).closest(".ui-dialog-content").dialog("destroy").remove();
                        }
                    });
                }
            },
            {
            class: "btn btn-success textoboton",
            text: "Cancelar",
            click: function() {
                $(this).closest(".ui-dialog-content").dialog("destroy").remove();
            }
        }]).then((dialogo)=>{
            ocultarPantallaEspera();
            if (celda.innerHTML=="NO"){
                mensaje="<form id='form_baja'><p>El residente causa BAJA en la residencia.</p>";
                mensaje+="<div class='form-row '>";
                mensaje+="<div class='col-12 form-group' style='display: flex;align-items: center;'><label style='margin-right: 10px;' for='fech_baja'>Fecha baja <small>(dd/mm/aaaa)</small>:</label><span class='errorTxt' style='font-size: 1em;'></span>";
                mensaje+="<input type='text' name='fech_baja' id='fech_baja' class='form-control' maxlength='10' size='15'  placeholder='Ej. 02/05/2000'></div></div></form>";
                baja=1;
            }
            else{
                mensaje="<p>El residente vuelve a estar de ALTA en la residencia.</p>";
                baja=0;
            }
            dialogo.innerHTML=mensaje;
            if (celda.innerHTML=="NO"){
                $("#form_baja").validate({
                    rules: {
                        fech_baja: {
                            required: true
                        }
                    },
                    messages: {
                        fech_baja: {
                            required: "Falta fecha de baja"
                        }
                    },
                    errorPlacement: function(error, element) {
                        $(element).prev($('.errorTxt')).html(error);
                    }
                });
                $("#fech_baja").datepicker({
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
                    maxDate: "0y",
                    nextText: "Siguiente",
                    prevText: "Previo"
                });
                var today = new Date();
                var day = String(today.getDate()).padStart(2, '0');
                var month = String(today.getMonth() + 1).padStart(2, '0'); // Enero es 0
                var year = today.getFullYear();

                var todayFormatted = day + '/' + month + '/' + year;
                document.getElementById('fech_baja').value = todayFormatted;
            }
        }).catch (error=>{
            ocultarPantallaEspera();
            var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
            alerta(msg,"ERROR DE CARGA");
    });
}


function fianza(__registro,celda){
    mostrarPantallaEspera("Cargando ...");
    cargaHTML("","","CAMBIO DE FIANZA",400,2000,"center center","center center",
    [
            {
                class: "btn btn-success textoboton",
                text: "Confirmar cambio",
                click: function() {
                    mostrarPantallaEspera();
                    obj=this
                    $.post({
                        url:"php/residencia_cambio_fianza.php" ,
                        data: {registro:__registro,fianza:document.getElementById("_fianz").value},
                        success: function(resp) {
                            ocultarPantallaEspera();
                            if (resp == "servidor") alerta("Hay un problema con el servidor. Inténtelo más tarde.", "ERROR SERVIDOR");
                            else if (resp == "database") alerta("No se actualizó ningún registro. Es posible que el valor no haya cambiado.", "FALLO AL ACTUALIZAR");
                            else if (resp == "ok"){
                                alerta("Cambio de estado realizado correctamente.","ACTUALIZACIÓN CORRECTA");
                                res_listaUsus();
                            }
                            else{
                                alerta(resp,"ERROR");
                            }
                            $(obj).closest(".ui-dialog-content").dialog("destroy").remove();
                        },
                        error: function(xhr, status, error) {
                            ocultarPantallaEspera();
                            alerta("Error en servidor. Código " + error + "<br>Inténtelo más tarde.", "ERROR DE SERVIDOR");
                            $(obj).closest(".ui-dialog-content").dialog("destroy").remove();
                        }
                    });
                }
            },
            {
            class: "btn btn-success textoboton",
            text: "Cancelar",
            click: function() {
                $(this).closest(".ui-dialog-content").dialog("destroy").remove();
            }
        }]    
    ).then((dialogo)=>{
        ocultarPantallaEspera();
        _ff=celda.innerText;
        mensaje="<div class='form-row'><div class='col form-group'>";
        mensaje+="<label for='_fianz'>Fianza (€):</label>";
        mensaje+="<input type='number' name='_fianz' id='_fianz' class='form-control' value='"+celda.innerText+"' step='0.01' min='0' /></div></div>";
        dialogo.innerHTML=mensaje;
    }).catch (error=>{
        ocultarPantallaEspera();
        var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
        alerta(msg,"ERROR DE CARGA");
    });
}

function res_cierrasesion() {
    $.post("php/logout.php", {}, function(resp) {
        open("index.php?q=" + Date.now().toString(), "_self");
    });
}

function remesasBanco() {
    enviarFormularioSubmit(
        {
            url: "php/residencia_csv_remesas.php",
            data: {
                curso_csv_remesas: document.getElementById("res_curso").value
            }
        }
    );
}

function res_GestionComedor(){
    mostrarPantallaEspera();
    cargaHTML("html/residencia.htm","div_lista_comedor","ASISTENCIA AL COMEDOR",800,2000,"center top","center top",
        [
            {
                class: "btn btn-success textoboton",
                text: "Guardar listado",
                click: function() {
                    $(this).dialog("destroy").remove();
                }
            },
            {
                class: "btn btn-danger textoboton",
                text: "Salir",
                click: function() {
                    $(this).dialog("destroy").remove();
                }
            }
        ]
    ).then((dialogo)=>{
        $("#fecha_lista_comedor").datepicker({
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
            maxDate: "0y",
            nextText: "Siguiente",
            prevText: "Previo"
        });
        var today = new Date();
        var day = String(today.getDate()).padStart(2, '0');
        var month = String(today.getMonth() + 1).padStart(2, '0'); // Enero es 0
        var year = today.getFullYear();

        var todayFormatted = day + '/' + month + '/' + year;
        document.getElementById('fecha_lista_comedor').value = todayFormatted;
        ocultarPantallaEspera();
        res_listadoRevisionAsistencia();
    }).catch (error=>{
        ocultarPantallaEspera();
        var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
        alerta(msg,"ERROR DE CARGA");
    });
}


function res_listadoRevisionAsistencia(){
    //Verifica que la fecha es valida y si no no se hace la consulta
    // formato esperado: dd/mm/yyyy
    var fechaStr=document.getElementById("fecha_lista_comedor").value;
    const partes = fechaStr.split('/');
    if (partes.length !== 3) return false;
    const dia = parseInt(partes[0], 10);
    const mes = parseInt(partes[1], 10) - 1; // Mes en JS: 0-11
    const anio = parseInt(partes[2], 10);

    const fecha = new Date(anio, mes, dia);
    var validez=fecha.getFullYear() === anio && fecha.getMonth() === mes && fecha.getDate() === dia
    if (!validez) return;
    mostrarPantallaEspera();
    $.post("php/residencia_comedor_listado.php",{curso:document.getElementById("res_curso").value,fecha:fechaStr},(resp)=>{
        ocultarPantallaEspera();
        if (resp.error=="ok"){
            _lt="";
            for (let i=0;i<resp.registros.length;i++){
                if (resp.registros[i].avisado==1) _lt+="<tr style='background-color:\"yellow\";color:\"brown\";'>";
                else "<tr>";
                _lt+="<td width='20%'>"+resp.registros[i].id_nie+"</td>";
                _lt+="<td width='65%'>"+resp.registros[i].nombre+"</td>";
                _lt+="<td width='5%' style='text-align:center' onclick='javascript:alert(this.innerHTML);if(this.innerHTML==\"X\")this.innerHTML=\"X\";else this.innerHTML=\"\";'>X</td>";
                _lt+="<td width='5%' style='text-align:center' onclick='javascript:if(this.innerHTML==\"X\")this.innerHTML=\"X\";else this.innerHTML=\"\";'></td>";
                _lt+="<td width='5%' style='text-align:center' onclick='javascript:if(this.innerHTML==\"X\")this.innerHTML=\"X\";else this.innerHTML=\"\";'></td>";
                _lt+="</tr>";
            }
            document.getElementById("asistencia_comedor").innerHTML=_lt;
        }
        else if (resp.error == "server"){
            alerta("Hay un problema en el servidor.", "ERROR DE SERVIDOR");
        } 
        else if (resp.error == "sin_registros"){
            alerta("La lista de residentes está vacía.", "SIN REGISTROS");
        }
        else{
            alerta("Error en la base de datos.", "ERROR DB");
        }

    },"json");
}
