var _nif_duplicado;

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

    $("#nuevoUsuario_div").dialog({
        autoOpen: false,
        dialogClass: "no-close",
        modal: true,
        draggable: false,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        title: "COMPLETE O REVISE SUS DATOS INICIALES",
        width: 650
    });



    /*jQuery.validator.addMethod("numero_nif", function(value, element) {
        if (value.miTrim() == '') return true;
        return /(([X-Z]{1})([-]?)(\d{7})([-]?)([A-Z]{1}))|((\d{8})([-]?)([A-Z]{1}))/.test(value.miTrim());
    });*/

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

    document.getElementById("nu_repemail").onpaste = function(e) {
        e.preventDefault();
        alerta('Esta acción está prohibida. Introduzca manualmente el email.', 'PEGAR');
    }

    document.getElementById("nu_reppassword").onpaste = function(e) {
        e.preventDefault();
        alerta('Esta acción está prohibida. Introduzca manualmente la contraseña.', 'PEGAR');
    }
});


function entra() {
    if (document.getElementById("form_login").checkValidity()) {
        $.post("php/index_login.php", $("#form_login").serialize(), function(resp) {
            if (resp.error == "server") alerta("Fallo de conexión al servidor", "ERROR SERVIDOR");
            else if (resp.error == "password") alerta("Contraseña inválida", "ERROR PASSWORD");
            else if (resp.error == "nousu") alerta("El usuario no existe. Consulte en la Secretaría del Centro.", "ERROR USUARIO");
            else if(resp.error=="inhabilitado") alerta("El usuario se ha inhabilitado por decisión del mismo, o por no ser ya alumno del centro. No podrá operar ni recibirá notificaciones.", "USUARIO INHABILITADO");
            else if (resp.error == "primera_vez") {
                document.getElementById("nu_nie").value = document.getElementById("usuario").value;
                document.getElementById("nu_apellidos").value=resp.datos.apellidos;
                document.getElementById("nu_nombre").value=resp.datos.nombre;
                document.getElementById("nu_email").value=resp.datos.email;
                document.getElementById("nu_repemail").value=resp.datos.email;
                document.getElementById("nu_nif").value=resp.datos.id_nif;
                $("#nuevoUsuario_div").dialog('open');
            } else if (resp.error == "ok") {
                document.location = resp.pagina;
            } else if(resp.error == "nodpto"){
                alerta ("No existe ningún Jefe de Departamento con esas credenciales.","ERROR USUARIO");
            }

        }, "json");
    } else document.getElementById("form_login").classList.add("was-validated");
}


function recuperaPass() {
    mostrarPantallaEspera();
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
    mostrarPantallaEspera();
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
            return true;
        });
    } else {
        document.getElementById("form_solicitaPass").classList.add("was-validated");
        return false;
    }
}




function solicitaRegistro() {
    if ($("#form_nuevoUsuario").valid()) {
        $.post("php/index_nuevousuario.php", $("#form_nuevoUsuario").serialize(), function(resp) {
            if (resp == "server") alerta("Problemas de conexión con el servidor. Inténtelo más tarde.", "Error de servidor");
            else if (resp == "registrado") alerta("Ya hay un usuario operando con este NIE (Número de Identificación Escolar). Por favor, póngase en contacto con la secretaría del centro.", "Usuario duplicado");
            else if (resp == "fallo_alta") alerta("No se han podido grabar los datos por un problema en el servidor. Inténtelo más tarde.", "Error de servidor");
            else if (resp == "ok") {
                alerta("Los datos se han grabado correctamente. Ya puede acceder al sistema.", "Proceso Correcto");
                document.getElementById("form_nuevoUsuario").reset();
                $("#nuevoUsuario_div").dialog('close');
                document.getElementById("form_login").reset();
            }
        });
    }
}