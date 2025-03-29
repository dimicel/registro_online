let backup_nombre = "";


$(document).ready(function() {

});


function confirmar() {
    document.getElementById('mensaje_div').innerHTML = "El proceso de registro será cancelado y se borrarán los datos del formulario.";
    $("#mensaje_div").dialog({
        title: "CANCELACIÓN DE PROCESO",
        autoOpen: false,
        dialogClass: "alert no-close",
        modal: true,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        buttons: [{
                class: "btn btn-success textoboton",
                text: "Aceptar",
                click: function() {
                    $(this).dialog("close");
                    if (iniciada_desde_matricula=="mat"){
                        window.history.back();
                        window.history.back();
                    } 
                    else window.history.back();
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Cancelar",
                click: function() {
                    $(this).dialog("close");
                    return false;
                }
            }
        ]
    });

    $("#mensaje_div").dialog('open');
}

function verificaEmail() {
    let exp_email = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
    if (document.getElementById("email2").value != document.getElementById("email3").value) {
        alerta("Los emails introducidos no son iguales.", "Error coincidencia.");
        return false;
    } else if (document.getElementById("email2").value.miTrim() == "") {
        alerta("La dirección de correo electrónico no puede estar vacía", "Email vacío");
        return false;
    }
    if (exp_email.test(document.getElementById("email2").value.miTrim())== true) {
        document.getElementById("email").value = document.getElementById("email2").value;
        return true;
    } else {
        alerta("El email introducido no es válido.", "Error Email");
        return false;
    }
}


function validaDatos() {
    let lista_errores = "";
    let ER_cp = /[0-9]{5}/;
    let ER_email = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    let nom = document.getElementById("nombre").value.miTrim();
    let nif = document.getElementById("nif_nie").value.miTrim();
    let ensenanza = document.getElementById("formacion").value;
    let fpb = document.getElementById("fpb").value;
    let gm = document.getElementById("gmedio").value;
    let gs = document.getElementById("gsuperior").value;
    if (nom.length == 0 || nom == "Seleccione en el desplegable de la izquierda.") {
        lista_errores += "- El Nombre no puede estar vacío.<br>";
    }
    if (nif.length == 0 || (!validaDNI_NIE(nif) && document.getElementById('nif').checked)) {
        lista_errores += "- Número de documento de identificación vacío o incorrecto.<br>";
    }
    if (ensenanza == "") lista_errores += "- No ha seleccionado formación.<br>";
    if (fpb == "" && gm == "" && gs == "") lista_errores += "- No ha seleccionado denominación de la enseñanza.<br>";

    if (lista_errores.length > 0) {
        lista_errores = "ERRORES DETECTADOS EN EL FORMULARIO<br>" + lista_errores;
        alerta(lista_errores, "¡FALTAN DATOS!");
        return false;
    }
    return true;
}



function generaImpreso() {
    document.getElementById("cargando").style.display = 'inline-block';
    let pet = $.ajax({
        url: "php/generapdf.php",
        type: "POST",
        data: $("#exenc").serialize()
    });
    $.when(pet).done(function(resp) {
        document.getElementById("cargando").style.display = 'none';
        if (resp == "servidor") {
            mensaje = "Ha habido un problema en el servidor. No se puede realizar el registro de su solicitud.<br>Por favor, vuelva a intentarlo más tarde.<br>";
            mensaje += "Si ve que el problema persiste, puede cumplimentar el formulario y presentarlo en ventanilla accediendo por Secretaría->Impresos de Secretaría->Para presentar en Ventanilla.";
            alerta(mensaje, "Error de servidor");
        } else if (resp == "registro_erroneo") {
            mensaje = "Ha habido un problema en el servidor. No se puede realizar el registro de su solicitud.<br>Por favor, vuelva a intentarlo más tarde.<br>";
            mensaje += "Si ve que el problema persiste, puede cumplimentar el formulario y presentarlo en ventanilla accediendo por Secretaría->Impresos de Secretaría->Para presentar en Ventanilla.";
            alerta(mensaje, "Error de servidor");
        } else if (resp.indexOf("envio2_fallido") != -1) {
            mensaje = "No se ha podido enviar a su correo el impreso y número de registro.<br>Por favor, revise si el email introducido es correcto y vuelva a intentarlo.<br>";
            mensaje += "Si el email es correcto, puede deberse a un fallo en el servidor. En este caso, vuelva a intentar más tarde el proceso.";
            mensaje += "Si ve que el problema persiste, puede cumplimentar el formulario y presentarlo en ventanilla accediendo por Secretaría->Impresos de Secretaría->Para presentar en Ventanilla.";
            alerta(mensaje, "Error en envío");
        } else if (resp.indexOf("envio1_fallido") != -1) {
            num_reg = resp.slice(14);
            mensaje = "No se ha podido enviar a su correo el impreso y número de registro.";
            mensaje += "Aún así, parece que su impreso se ha sido enviado correctamente a la Secretaría del Cento, con el nº de registro:<br><b>";
            mensaje += num_reg + "</b><br>";
            mensaje += "Puede ponerse en contacto con el personal de secretaría para verificarlo, en el teléfono 925 22 34 00 EXTENSIONES 272 y 236";
        } else if (resp == "no_file") {
            alerta("Ha habido un error y no se ha podido generar el fichero con el formulario registrado.", "Error en servidor");
        } else if (resp.indexOf("envio_ok") != -1) {
            num_reg = resp.slice(8);
            mensaje = "Proceso finalizado correctamente. Tome nota de su nº de registro:<br><br>";
            mensaje += "<center><b>" + num_reg + "</b></center><br><br>";
            mensaje += "RECUERDE QUE:<br><br>";
            mensaje += "-DEBE ENVIAR EL Nº DE REGISTRO POR <strong>PAPAS 2.0</strong>, AL GRUPO <strong>'Coordinadores de mi centro'</strong> (PUEDE SELECCIONAR Y COPIAR CON EL RATÓN EL Nº DE REGISTRO, Y PEGARLO EN EL MENSAJE DE PAPAS). DE LO CONTRARIO, EL FORMULARIO NO SE CONSIDERARÁ FIRMADO Y NO SERÁ VÁLIDO.<br>"
            mensaje += "-SI VE QUE NO RECIBE EL CORREO ELECTRÓNICO, DEBE REVISAR LA CARPETA SPAM O CORREO NO DESEADO.<br><br>";
            mensaje += "-SI EN EL PLAZO DE 24/48 HORAS EL PERSONAL DE SECRETARÍA NO HA RECIBIDO SU SOLICITUD, DEBE PONERSE EN CONTACTO CON ELLOS EN EL TELÉFONO 925 22 34 00 EXTENSIONES 272 Y 236";
            alerta(mensaje, "Registro correcto");
        }
        document.getElementById('exenc').reset();
        document.getElementById("email2").value = "";
        document.getElementById("email3").value = "";
    });
}


function seleccionListaDon() {
    if (document.getElementById("lista_don").value != "") {
        document.getElementById("nombre").readOnly = false;
        document.getElementById("nombre").value = backup_nombre;
    } else {
        document.getElementById("nombre").readOnly = true;
        backup_nombre = document.getElementById("nombre").value;
        document.getElementById("nombre").value = "Seleccione D. o Dña. en el desplegable anterior.";
    }
}


function iniciaGeneraPdf() {
    if (validaDatos()) $("#div_email").dialog('open');
}
// JavaScript Document