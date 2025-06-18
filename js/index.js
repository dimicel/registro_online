var _nif_duplicado;
var residente_baja=false;
var mes,anno_ini;

$(function() {
    /*
    $.post("php/index_redireccion.php", {}, (resp) => {
        if (resp == "modo_obras.html") document.location = resp;
    });
    */
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');

    if (!token) {
        // Si no se ha visitado el index.php, redirigir al index.php
        window.location.href = 'index.php';
    } else {
        try {
            // Decodificar el token y obtener la información de la sesión
            const sessionData = JSON.parse(atob(token));
            const visitadoIndex = sessionData.visitado_index;

            if (!visitadoIndex) {
                window.location.href = 'index.php';
            }
        } catch (error) {
            console.error('Error al decodificar el token:', error);
            window.location.href = 'index.php';
        }
    }

    document.getElementById("usuario").focus();
    
    if (document.location.hostname!="registro.ulaboral.org")document.getElementById("servidor_pruebas").style.display="inherit";
    else document.getElementById("servidor_pruebas").style.display="none";

    

    jQuery.validator.addMethod("rep_email", function(value, element) {
        return document.getElementById("nu_email").value == value ? true : false;
    });

    

    jQuery.validator.addMethod("rep_password", function(value, element) {
        return document.getElementById("nu_password").value === value ? true : false;
    });

    jQuery.validator.addMethod("nif_noduplicado", function(value, element) {
        if (value.miTrim() == '') return true;
        $.ajaxSetup({ async: false });
        var a = $.post("php/index_nifduplicado.php", { id_nie:document.getElementById("usuario").value,nu_nif: value }, function(resp) {
            if (resp == "ok") _nif_duplicado = true;
            else _nif_duplicado = false;
        });
        $.ajaxSetup({ async: true });
        return _nif_duplicado;
    });

    $.post("php/secret_recupera_nombre_centro.php",{},(resp)=>{
        document.getElementById("centro").innerHTML=resp["registro"]["centro"].toUpperCase();
        document.getElementById("titulo").innerHTML+=resp["registro"]["centro"].toUpperCase();
    },"json");

});


function entra() {
    if (document.getElementById("form_login").checkValidity()) {
            mostrarPantallaEspera();
            $.post("php/index_login.php", $("#form_login").serialize(), function(resp) {
                ocultarPantallaEspera();
                if (resp.error == "server") alerta("Fallo de conexión al servidor", "ERROR SERVIDOR");
                else if (resp.error == "password") alerta("Contraseña inválida", "ERROR PASSWORD");
                else if (resp.error == "nousu") alerta("El usuario no existe. Consulte en la Secretaría del Centro.", "ERROR USUARIO");
                else if(resp.error=="inhabilitado") alerta("El usuario se ha inhabilitado por decisión del mismo, o por no ser ya alumno del centro. No podrá operar ni recibirá notificaciones.", "USUARIO INHABILITADO");
                else if (resp.error == "primera_vez") {
                    mostrarPantallaEspera("Cargando ...");
                    cargaHTML("html/index.htm","nuevoUsuario_div","COMPLETE O REVISE SUS DATOS INICIALES",650,2000)
                    .then((dialogo)=>{
                        ocultarPantallaEspera();
                        document.getElementById("nu_repemail").onpaste = function(e) {
                            e.preventDefault();
                            alerta('Esta acción está prohibida. Introduzca manualmente el email.', 'PEGAR');
                        }

                        document.getElementById("nu_reppassword").onpaste = function(e) {
                            e.preventDefault();
                            alerta('Esta acción está prohibida. Introduzca manualmente la contraseña.', 'PEGAR');
                        }
                        $("#form_nuevoUsuario").validate({
                            rules: {
                                nu_nif: {
                                    numero_nif: true,
                                    nif_noduplicado: true
                                },
                                nu_nombre: {
                                    required: true
                                },
                                nu_apellidos: {
                                    required: true
                                },
                                nu_email: {
                                    required: true,
                                    email: true
                                },
                                nu_repemail: {
                                    required: true,
                                    rep_email: true
                                },
                                nu_password: {
                                    required: true,
                                    minlength: 8,
                                    password: true
                                },
                                nu_reppassword: {
                                    required: true,
                                    rep_password: true
                                }/*,
                                nu_condiciones: {
                                    required: true
                                }*/
                            },
                            messages: {
                                nu_nif: {
                                    numero_nif: "NIF incorrecto.",
                                    nif_noduplicado: "NIF ya registrado."
                                },
                                nu_nombre: {
                                    required: "Debe completar el campo."
                                },
                                nu_apellidos: {
                                    required: "Debe completar el campo."
                                },
                                nu_email: {
                                    required: "Debe completar el campo.",
                                    email: "No es una dirección de correo electrónico."
                                },
                                nu_repemail: {
                                    required: "Debe completar el campo.",
                                    rep_email: "Los email no coinciden."
                                },
                                nu_password: {
                                    required: "Debe completar el campo.",
                                    minlength: "Debe contener 8 caracteres como mínimo.",
                                    password: "Debe contener, al menos, una minúscula, una mayúscula y un número."
                                },
                                nu_reppassword: {
                                    required: "Debe completar el campo.",
                                    rep_password: "Las contraseñas no coinciden."
                                }/*,
                                nu_condiciones: {
                                    required: "Debe aceptar las condiciones de uso."
                                }*/
                            },
                            errorPlacement: function(error, element) {
                                $(element).prev($('.errorTxt')).html(error);
                                /*if ($(element).attr('id') != 'nu_condiciones') $(element).prev($('.errorTxt')).html(error);
                                else $(element).next().next($('.errorTxt')).html(error);*/
                            }
                        });
                        document.getElementById("nu_nie").value = document.getElementById("usuario").value;
                        document.getElementById("nu_apellidos").value=resp.datos.apellidos;
                        document.getElementById("nu_nombre").value=resp.datos.nombre;
                        document.getElementById("nu_email").value=resp.datos.email;
                        document.getElementById("nu_repemail").value=resp.datos.email;
                        document.getElementById("nu_nif").value=resp.datos.id_nif;
                    })
                    .catch (error=>{
                        ocultarPantallaEspera();
                        var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
                        alerta(msg,"ERROR DE CARGA");
                    });
                } else if (resp.error == "ok") {
                    document.location = resp.pagina;
                } else if(resp.error == "nodpto"){
                    alerta ("No existe ningún Jefe de Departamento con esas credenciales.","ERROR USUARIO");
                }
            }, "json");
    } else document.getElementById("form_login").classList.add("was-validated");
}

function solicitaRegistro() {
    if ($("#form_nuevoUsuario").valid()) {
        mostrarPantallaEspera("Registrando usuario...");
        $.post("php/index_nuevousuario.php", $("#form_nuevoUsuario").serialize(), function(resp) {
            ocultarPantallaEspera();
            if (resp == "server") alerta("Problemas de conexión con el servidor. Inténtelo más tarde.", "Error de servidor");
            else if (resp == "registrado") alerta("Ya hay un usuario operando con este NIE (Número de Identificación Escolar). Por favor, póngase en contacto con la secretaría del centro.", "Usuario duplicado");
            else if (resp == "fallo_alta") alerta("No se han podido grabar los datos por un problema en el servidor. Inténtelo más tarde.", "Error de servidor");
            else if (resp == "ok") {
                alerta("Los datos se han grabado correctamente. Ya puede acceder al sistema.", "Proceso Correcto");
            }
            $("#nuevoUsuario_div").closest('.ui-dialog-content').dialog('destroy').remove();
            document.getElementById("form_login").reset();
        });
    }
}


function recuperaPass() {
    mostrarPantallaEspera("Cargando ...");
    cargaHTML("html/index.htm", "nuevaPass_div","RECUPERAR CONTRASEÑA",275,2000,"center center","center center")
    .then((dialogo)=>{
        ocultarPantallaEspera();
    })
    .catch (error=>{
        ocultarPantallaEspera();
        var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
        alerta(msg,"ERROR DE CARGA");
    });
}

function condiciones(){
    mostrarPantallaEspera("Cargando ...");
    cargaHTML("html/index.htm", "condiciones_div","CONDICIONES DE USO",600,2000,"center center","center center",
        [
            {
                text:"Cerrar",
                class: "textoboton btn btn-success btn-sm",
                click:function(){
                    $(this).dialog("destroy").remove();
                }
            }
        ]
    )
    .then((dialogo)=>{
            ocultarPantallaEspera();
    })
    .catch (error=>{
        ocultarPantallaEspera();
        var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
        alerta(msg,"ERROR DE CARGA");
    });
}


function generaContrasena() {
    if (document.getElementById("form_solicitaPass").checkValidity()) {
        mostrarPantallaEspera("Generando contraseña...");
        $.post("php/index_generapass.php", { nie: $("#np_nie").val().miTrim() }, function(resp) {
            ocultarPantallaEspera();
            if (resp == "server") alerta("Problemas de conexión con el servidor. Inténtelo más tarde.", "Error de servidor");
            else if (resp == "envio") alerta("Fallo de envío.<br>El correo electrónico puede no ser correcto", "Error email");
            else if (resp == "usuario") alerta("El NIE no corresponde a ningún usuario registrado.", "Error usuario");
            else if (resp == "grabar") alerta("Ha habido un fallo al registrar la nueva contraseña.", "Error password");
            else if (resp == "reservado") alerta("Número de identificación reservado.", "NO PERMITIDO");
            else if (resp == "primer_acceso") alerta("Entre con su usuario (NIE) y contraseña asignados en el Centro educativo, y complete el formulario de datos.", "Usuario Nuevo SIN Acceso");
            else if (resp == "ok") {
                alerta("Se ha enviado al correo asociado al NIE la nueva contraseña.<br>Si no la recibe, por favor, compruebe la carpeta 'spam' de su cuenta de correo", "Password enviada");
            }
            else alerta(resp,"ERROR");
            $('#nuevaPass_div').closest('.ui-dialog-content').dialog('destroy').remove();
        });
    } else {
        document.getElementById("form_solicitaPass").classList.add("was-validated");
    }
}


function compruebaEsResidente(){
    $.post("php/index_esresidente.php",{usuario:document.getElementById("usuario").value},(resp)=>{
        mes=parseInt(resp.mes);
        anno_ini=parseInt(resp.anno_inicio);
        if (resp.esresidente=="si" || resp.esresidente=="baja"){
            document.getElementById("comedor").style.display="inherit";
            if(resp.esresidente=="baja")residente_baja=true;
            else residente_baja=false;
        } 
        else{
            document.getElementById("comedor").style.display="none";
        } 
    },"json");
}

function comedor(){
    //alerta("Funcionalidad en desarrollo.","WORKING...");
    //return;
    if (document.getElementById("password").value.trim()==""){
        alerta("Debe introducir su contraseña de usuario.","NO PASSWORD");
        return;
    }
    if (residente_baja){
        alerta("El usuario causó baja en la residencia. Si cree que hay un error, póngase en contacto con el Jefe de Residencia para que proceda a subsanarlo lo antes posible.","BAJA");
        return;
    }
    mostrarPantallaEspera();
    $.post("php/index_login_comedor.php",{id_nie:document.getElementById("usuario").value,pass:document.getElementById("password").value},(resp)=>{
        if (resp.error=="ok" && resp.dia>=1 && resp.dia<=4){
            cargaHTML("html/index.htm","residencia_comedor","SELECCIÓN DE DÍAS NO ASISTENCIA A COMEDOR",500,400,"center center","center center",
                [
                    {
                        class: "btn btn-success textoboton",
                        text: "Guardar Selección",
                        click: function() {
                            mostrarPantallaEspera();
                            let fechas_semana_no_comedor=[];
                            let _t=document.getElementById("comedor_dias").rows[0].cells;
                            for (let i=0; i<5;i++){
                                if(_t[i].style.color=="brown") fechas_semana_no_comedor.push([_t[i].id,1]);
                                else fechas_semana_no_comedor.push([_t[i].id,0]);   
                            }
                            $.post({
                                url:"php/index_comedor_graba_fechas.php" ,
                                data: {lista_fechas: JSON.stringify(fechas_semana_no_comedor),id_nie:document.getElementById("usuario").value},
                                success: function(resp) {
                                    ocultarPantallaEspera();
                                    if (resp == "servidor") alerta("Hay un problema con el servidor. Inténtelo más tarde.", "ERROR SERVIDOR");
                                    else if (resp == "ok"){
                                        alerta("Fechas grabadas correctamente.","FECHAS GUARDADAS");
                                    }
                                    else{
                                        alerta(resp,"ERROR");
                                    }
                                },
                                error: function(xhr, status, error) {
                                    ocultarPantallaEspera();
                                    alerta("Error en servidor. Código " + error + "<br>Inténtelo más tarde.", "ERROR DE SERVIDOR");
                                }
                            });
                            $(this).dialog("destroy").remove();
                        }
                    },
                    {
                        class: "btn btn-success textoboton",
                        text: "Cancelar",
                        click: function() {
                            $(this).dialog("destroy").remove();
                        }
                    }
                ]
            ).then((dialogo)=>{
                ocultarPantallaEspera();
                //Select de meses para el informe
                const select = document.getElementById("mes_comedor");
                const meses = ["", "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sept", "Oct", "Nov", "Dic"];
                let optionsHTML = "<option value=''>Seleccione mes...</option>";

                if (mes >= 9 && mes <= 12) {
                    // Solo meses de septiembre a mes actual en anno_ini
                    for (let m = 9; m <= mes; m++) {
                    optionsHTML += `<option value="${meses[m]}/${anno_ini}">${meses[m]}/${anno_ini}</option>`;
                    }
                } else if (mes >= 1 && mes <= 6) {
                    // De septiembre a diciembre en anno_ini
                    for (let m = 9; m <= 12; m++) {
                    optionsHTML += `<option value="${meses[m]}/${anno_ini}">${meses[m]}/${anno_ini}</option>`;
                    }
                    // De enero a mes actual en anno_ini + 1
                    for (let m = 1; m <= mes; m++) {
                    optionsHTML += `<option value="${meses[m]}/${anno_ini + 1}">${meses[m]}/${anno_ini + 1}</option>`;
                    }
                }
                select.innerHTML = optionsHTML;      

                // Tabla con los días seleccionables del comedor
                tabla_com="<tr>";
                for (let i=0; i<5;i++){
                    if(resp.fechas_no_comedor.length>0){
                        let encontrado=false;
                        for(let j=0;j<resp.fechas_no_comedor.length;j++){
                            //let fecha_elegida= new Date(resp.fechas_no_comedor[j]);
                            //let fecha_calendario=new Date(resp.fechas[i].fecha);
                            let fecha_elegida= resp.fechas_no_comedor[j];
                            let fecha_calendario=resp.fechas[i].fecha;
                            if (fecha_elegida==fecha_calendario){
                                encontrado=true;
                                break;
                            }
                        }
                        if (encontrado){
                            tabla_com+="<td id='"+resp.fechas[i].fecha+"' width='20%' style='text-align:center;text-size:0.5em;color:brown;background-color:yellow;' onclick='if(this.style.color==\"brown\"){this.style.color=\"#312e25\";this.style.backgroundColor=\"#f4f3e5\";}else{this.style.color=\"brown\";this.style.backgroundColor=\"yellow\";}'>";
                        }
                        else{
                            tabla_com+="<td id='"+resp.fechas[i].fecha+"' width='20%' style='text-align:center;text-size:0.5em;color:#312e25;' onclick='if(this.style.color==\"brown\"){this.style.color=\"#312e25\";this.style.backgroundColor=\"#f4f3e5\";}else{this.style.color=\"brown\";this.style.backgroundColor=\"yellow\";}'>";
                        }
                    }
                    else{
                        tabla_com+="<td id='"+resp.fechas[i].fecha+"' width='20%' style='text-align:center;text-size:0.5em;color:#312e25;' onclick='if(this.style.color==\"brown\"){this.style.color=\"#312e25\";this.style.backgroundColor=\"#f4f3e5\";}else{this.style.color=\"brown\";this.style.backgroundColor=\"yellow\";}'>";
                    }
                    tabla_com+=resp.fechas[i].dia_sem+"<br>"+resp.fechas[i].dia+"/"+resp.fechas[i].mes;
                    tabla_com+="</td>";
                }
                tabla_com+="</tr>";
                document.getElementById("comedor_dias").innerHTML=tabla_com;

            }).catch (error=>{
                ocultarPantallaEspera();
                var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
                alerta(msg,"ERROR DE CARGA");
            });
        }
        else if(resp.error=="password"){
            alerta("Contraseña incorrecta.","FALLO PASS");
        }
        else if(resp.error=="nousu"){
            alerta("El usuario no existe.","ERROR USUARIO");
        }
        else if(resp.dia<1 || resp.dia>4){
            alerta("La selección de días en los que el usuario no hará uso del comedor en la semana siguiente sólo está permitida de LUNES a JUEVES.","NO PERMITIDO");
        }
        else {
            alerta("Error en base de datos. Inténtelo en otro momento.","ERROR DB");
        }
        ocultarPantallaEspera();
    },"json");
}

