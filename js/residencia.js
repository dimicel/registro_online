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
   
    document.getElementById("res_cargando").style.display = 'inherit';
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
            document.getElementById("res_cargando").style.display = 'none';
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
                if (data_array[i]["id_nie"].substring(0,9) == "S4500175G") continue;
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
    $("#res_div_dialogs").load("html/secretaria.htm?q="+Date.now()+" #div_email_usuario", function(response,status,xhr){
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
            $("#res_div_dialogs").dialog({
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
                                document.getElementById("res_cargando").style.display = "inherit";
                                $.post("php/residencia_enviaremail.php", { email: dir_email, asunto: asunto, mensaje: mensaje }, function() {
                                    document.getElementById("res_cargando").style.display = "none";
                                    alerta("Correo electrónico enviado.", "EMAIL");
                                    $("#res_div_dialogs").dialog("close");
                                });
                            }
                        }
                    },
                    {
                        class: "btn btn-success textoboton",
                        text: "Cancelar",
                        click: function() {
                            $("#res_div_dialogs").dialog("close");
                        }
                    }
                ],
                close: function(event, ui) {
                    $("#res_div_dialogs").dialog("destroy");
                }
            });
        }
    });

}


function cambioEmailJefeRes(){
    $.post("php/secret_recupera_param_centro.php",{},(resp)=>{
        if (resp.error=="ok"){
            document.getElementById("email_jr").value=resp.registro.email_jefe_residencia;
        }
        else if (resp.datos=="server"){
            alerta("Error en servidor. No se puede cambiar el email del Jefe de Residencia","ERROR SERVIDOR");
            volver=true;
        }
    },"json");

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

    $("#div_cambio_email_jef_res").dialog({
        autoOpen: true,
        dialogClass: "no-close",
        modal: true,
        draggable: false,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        title: "CAMBIO DE DATO",
        width: 500,
        position: { my: "center", at: "center", of: window },
        buttons: [
            {
                class: "btn btn-success textoboton",
                text: "Guardar Cambios",
                click: function() {
                    if ($("#cambio_email_jef_res").valid()){
                        document.getElementById("res_cargando").style.display = 'inherit';
                        $.post({
                            url:"php/residencia_actualiza_email_jr.php" ,
                            data: $("#cambio_email_jef_res").serialize(),
                            success: function(resp) {
                                document.getElementById("res_cargando").style.display = 'none';
                                if (resp == "servidor") alerta("Hay un problema con el servidor. Inténtelo más tarde.", "ERROR SERVIDOR");
                                else if (resp == "database") alerta("No se actualizó ningún registro. Es posible que el valor no haya cambiado.", "FALLO AL ACTUALIZAR");
                                else if (resp == "ok"){
                                    alerta("Email actualizado correctamente.","ACTUALIZACIÓN CORRECTA");
                                }
                                else{
                                    alerta(resp,"ERROR");
                                }
                                $("#div_cambio_email_jef_res").dialog("close");
                                $("#div_cambio_email_jef_res").dialog("destroy");
                            },
                            error: function(xhr, status, error) {
                                document.getElementById("res_cargando").style.display = 'none';
                                alerta("Error en servidor. Código " + error + "<br>Inténtelo más tarde.", "ERROR DE SERVIDOR");
                                $("#div_cambio_email_jef_res").dialog("close");
                                $("#div_cambio_email_jef_res").dialog("destroy");
                            }
                        });
                    }
                    
                }
            },
            {
            class: "btn btn-success textoboton",
            text: "Cancelar",
            click: function() {
                $("#div_cambio_email_jef_res").dialog("close");
                $("#div_cambio_email_jef_res").dialog("destroy");
            }
        }]
    });
}


function estadoBonificado(__registro,celda){
    if (celda.innerHTML=="NO"){
        mensaje="<p>Va a cambiar el estado del residente de NO BONIFICADO a BONIFICADO.</p>";
        bonificado=1;
    }
    else{
        mensaje="<p>Va a cambiar el estado del residente de BONIFICADO a NO BONIFICADO.</p>";
        bonificado=0;
    }
    document.getElementById("res_div_dialogs").innerHTML=mensaje;
    $("#res_div_dialogs").dialog({
        autoOpen: true,
        dialogClass: "no-close",
        modal: true,
        draggable: false,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        title: "CAMBIO ESTADO BONIFICADO/NO BONIFICADO",
        width: 700,
        position: { my: "center", at: "center", of: window },
        buttons: [
            {
                class: "btn btn-success textoboton",
                text: "Confirmar cambio",
                click: function() {
                    document.getElementById("res_cargando").style.display = 'inherit';
                    $.post({
                        url:"php/residencia_cambio_estado_bonificado.php" ,
                        data: {registro:__registro,bonificado:bonificado},
                        success: function(resp) {
                            document.getElementById("res_cargando").style.display = 'none';
                            if (resp == "servidor") alerta("Hay un problema con el servidor. Inténtelo más tarde.", "ERROR SERVIDOR");
                            else if (resp == "database") alerta("No se actualizó ningún registro. Es posible que el valor no haya cambiado.", "FALLO AL ACTUALIZAR");
                            else if (resp == "ok"){
                                alerta("Cambio de estado realizado correctamente.","ACTUALIZACIÓN CORRECTA");
                                res_listaUsus();
                            }
                            else{
                                alerta(resp,"ERROR");
                            }
                            $("#res_div_dialogs").dialog("close");
                            $("#res_div_dialogs").dialog("destroy");
                        },
                        error: function(xhr, status, error) {
                            document.getElementById("res_cargando").style.display = 'none';
                            alerta("Error en servidor. Código " + error + "<br>Inténtelo más tarde.", "ERROR DE SERVIDOR");
                            $("#res_div_dialogs").dialog("close");
                            $("#res_div_dialogs").dialog("destroy");
                        }
                    });
                }
            },
            {
            class: "btn btn-success textoboton",
            text: "Cancelar",
            click: function() {
                $("#res_div_dialogs").dialog("close");
                $("#res_div_dialogs").dialog("destroy");
            }
        }]
    });
}

function altaBaja(__registro,celda){
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
    document.getElementById("res_div_dialogs").innerHTML=mensaje;
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
    
    $("#res_div_dialogs").dialog({
        autoOpen: true,
        dialogClass: "no-close",
        modal: true,
        draggable: false,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        title: "ALTA/BAJA DE RESIDENTE",
        width: 400,
        position: { my: "center", at: "center", of: window },
        buttons: [
            {
                class: "btn btn-success textoboton",
                text: "Confirmar cambio",
                click: function() {
                    if (baja==1) if (!$("#form_baja").valid()) return;
                    if (baja==1) fecha_baja=document.getElementById('fech_baja').value;
                    else fecha_baja="";
                    document.getElementById("res_cargando").style.display = 'inherit';
                    $.post({
                        url:"php/residencia_alta_baja.php",
                        data: {registro:__registro,baja:baja,fecha_baja:fecha_baja},
                        success: function(resp) {
                            document.getElementById("res_cargando").style.display = 'none';
                            if (resp == "servidor") alerta("Hay un problema con el servidor. Inténtelo más tarde.", "ERROR SERVIDOR");
                            else if (resp == "database") alerta("No se actualizó ningún registro. Es posible que el valor no haya cambiado.", "FALLO AL ACTUALIZAR");
                            else if (resp == "ok"){
                                alerta("Cambio de estado realizado correctamente.","ACTUALIZACIÓN CORRECTA");
                                res_listaUsus();
                            }
                            else{
                                alerta(resp,"ERROR");
                            }
                            $("#res_div_dialogs").dialog("close");
                            $("#res_div_dialogs").dialog("destroy");
                        },
                        error: function(xhr, status, error) {
                            document.getElementById("res_cargando").style.display = 'none';
                            alerta("Error en servidor. Código " + error + "<br>Inténtelo más tarde.", "ERROR DE SERVIDOR");
                            $("#res_div_dialogs").dialog("close");
                            $("#res_div_dialogs").dialog("destroy");
                        }
                    });
                }
            },
            {
            class: "btn btn-success textoboton",
            text: "Cancelar",
            click: function() {
                $("#res_div_dialogs").dialog("close");
                $("#res_div_dialogs").dialog("destroy");
            }
        }]
    });
}


function fianza(__registro,celda){
    _ff=celda.innerText;
    mensaje="<div class='form-row'><div class='col form-group'>";
    mensaje+="<label for='_fianz'>Fianza (€):</label>";
    mensaje+="<input type='number' name='_fianz' id='_fianz' class='form-control' value='"+celda.innerText+"' step='0.01' min='0' /></div></div>";
    document.getElementById("res_div_dialogs").innerHTML=mensaje;
    $("#res_div_dialogs").dialog({
        autoOpen: true,
        dialogClass: "no-close",
        modal: true,
        draggable: false,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        title: "FIANZA ASIGNADA PARA SER DEVUELTA",
        width: 400,
        position: { my: "center", at: "center", of: window },
        buttons: [
            {
                class: "btn btn-success textoboton",
                text: "Confirmar cambio",
                click: function() {
                    document.getElementById("res_cargando").style.display = 'inherit';
                    $.post({
                        url:"php/residencia_cambio_fianza.php" ,
                        data: {registro:__registro,fianza:document.getElementById("_fianz").value},
                        success: function(resp) {
                            document.getElementById("res_cargando").style.display = 'none';
                            if (resp == "servidor") alerta("Hay un problema con el servidor. Inténtelo más tarde.", "ERROR SERVIDOR");
                            else if (resp == "database") alerta("No se actualizó ningún registro. Es posible que el valor no haya cambiado.", "FALLO AL ACTUALIZAR");
                            else if (resp == "ok"){
                                alerta("Cambio de estado realizado correctamente.","ACTUALIZACIÓN CORRECTA");
                                res_listaUsus();
                            }
                            else{
                                alerta(resp,"ERROR");
                            }
                            $("#res_div_dialogs").dialog("close");
                            $("#res_div_dialogs").dialog("destroy");
                        },
                        error: function(xhr, status, error) {
                            document.getElementById("res_cargando").style.display = 'none';
                            alerta("Error en servidor. Código " + error + "<br>Inténtelo más tarde.", "ERROR DE SERVIDOR");
                            $("#res_div_dialogs").dialog("close");
                            $("#res_div_dialogs").dialog("destroy");
                        }
                    });
                }
            },
            {
            class: "btn btn-success textoboton",
            text: "Cancelar",
            click: function() {
                $("#res_div_dialogs").dialog("close");
                $("#res_div_dialogs").dialog("destroy");
            }
        }]
    });
}

function res_cierrasesion() {
    $.post("php/logout.php", {}, function(resp) {
        open("index.php?q=" + Date.now().toString(), "_self");
    });
}

function remesasBanco() {
    document.getElementById("curso_csv_remesas").value = document.getElementById("res_curso").value;
    document.getElementById("descarga_csv_remesas").submit();
}
