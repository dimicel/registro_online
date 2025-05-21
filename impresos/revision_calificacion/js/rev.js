///REVISION DE CALIFICACION
id_nif = "";
nombre = "";
apellidos = "";
email = "";
var duracion = 0;
var backup_nombre = "";

$(document).ready(function() {
    $.post("../../php/sesion.php", { tipo_usu: "usuario" }, function(resp) {
        if (resp["error"] != "ok") document.write(resp["error"]);
        else {
            id_nie=resp["id_nie"];
            id_nif = resp["id_nif"];
            nombre = resp["nombre"];
            apellidos = resp["apellidos"];
            email = resp["email"];
            document.getElementById("id_nie").value = resp["id_nie"];
            document.getElementById("email").value = resp["email"];
            document.getElementById("id_nif").value = resp["id_nif"];
            document.getElementById("usuario").value = resp["nombre"] + " " + resp["apellidos"];
        }
    }, "json");
});


function validaDatos() {
    var lista_errores = "";
    var nom = document.getElementById("nombre").value.miTrim();
    var nif = document.getElementById("nif_nie").value.miTrim();
    var domicilio = document.getElementById("domicilio").value;
    var telefono = document.getElementById("telefono").value;
    var poblacion = document.getElementById("poblacion").value;
    var cp = document.getElementById("cp").value;
    var provincia = document.getElementById("provincia").value;
    var ciclo = document.getElementById("ciclo").value;
    var modulo = document.getElementById("modulo").value;
    var nota = document.getElementById("nota").value;
    var razones = document.getElementById("razones").value;
    if (nom.length == 0 || nom == "Seleccione en el desplegable de la izquierda.") {
        lista_errores += "- El Nombre no puede estar vacío.<br>";
    }
    if (nif.length == 0 || (!validaDNI_NIE(nif) && document.getElementById('nif').checked)) {
        lista_errores += "- Número de documento de identificación vacío o incorrecto.<br>";
    }
    if (domicilio.length == 0) {
        lista_errores += "- Falta el domicilio.<br>";
    }
    if (telefono.length == 0) {
        lista_errores += "- Falta un teléfono de contacto.<br>";
    }
    if (poblacion.length == 0) {
        lista_errores += "- Falta población de residencia.<br>";
    }
    if (cp.length == 0) {
        lista_errores += "- No se ha introducido el CP.<br>";
    }
    if (provincia.length == 0) {
        lista_errores += "- Introducir provincia de residencia.<br>";
    }
    if (ciclo.length == 0) {
        lista_errores += "- Hay que especificar el Ciclo Formativo.<br>";
    }
    if (modulo.length == 0) {
        lista_errores += "- Hay que especificar el módulo..<br>";
    }
    if (nota.length == 0) {
        lista_errores += "- Hay que especificar la calificación obtenida.<br>";
    }
    if (lista_errores.length > 0) {
        lista_errores = "ERRORES DETECTADOS EN EL FORMULARIO<br>" + lista_errores;
        alerta(lista_errores, "¡FALTAN DATOS!");
        return false;
    }
    return true;
}


function generaImpreso() {
    mostrarPantallaEspera(); 
    var pet = $.ajax({
        url: "php/generapdf.php",
        type: "POST",
        data: $("#rev_cal").serialize()
    });
    $.when(pet).done(function(resp) {
        ocultarPantallaEspera();
        if (resp == "servidor") {
            mensaje = "Ha habido un problema en el servidor. No se puede realizar el registro de su solicitud.<br>Por favor, vuelva a intentarlo más tarde.<br>";
            mensaje += "Si ve que el problema persiste, puede cumplimentar el formulario y presentarlo en ventanilla accediendo por Secretaría->Impresos de Secretaría->Para presentar en Ventanilla.";
            alerta(mensaje, "Error de servidor");
        } else if (resp.indexOf("registro_erroneo") != -1) {
            mensaje = "Ha habido un problema en el servidor. No se puede realizar el registro de su solicitud.<br>Por favor, vuelva a intentarlo más tarde.<br>";
            mensaje += "Si ve que el problema persiste, puede cumplimentar el formulario y presentarlo en ventanilla accediendo por Secretaría->Impresos de Secretaría->Para presentar en Ventanilla.";
            alerta(mensaje, "Error de servidor");
        } else if (resp.indexOf("envio_fallido") != -1) {
            num_reg = resp.slice(14);
            mensaje = "No se ha podido enviar a su correo el impreso y número de registro.";
            mensaje += "Aún así, parece que su impreso se ha registrado correctamente con el nº:<br><b>";
            mensaje += num_reg + "</b><br>";
        } else if (resp == "no_file") {
            alerta("Ha habido un error y no se ha podido generar el fichero con el formulario registrado.", "Error en servidor");
        } else if (resp.indexOf("envio_ok") != -1) {
            document.getElementById("num_registro").value = resp.slice(8);
            document.rev_cal.reset();
            alerta("Proceso finalizado correctamente.<br>Se le ha enviado el documento registrado a su correo junto con el número de registro asignado.<br>Si no ve el correo, revise la carpeta spam o correo no deseado.<br>También verá que el documento se ha descargado en el navegador en formato PDF.", "Registro correcto", true);
        }
    });
}


function seleccionListaDon() {
    if (
        document.getElementById("lista_don").value != "") {
        document.getElementById("nombre").readOnly = false;
        document.getElementById("nombre").value = backup_nombre;
    } else {
        document.getElementById("nombre").readOnly = true;
        backup_nombre = document.getElementById("nombre").value;
        document.getElementById("nombre").value = "Seleccione D. o Dña. en el desplegable anterior.";
    }
}


function selGrado(a) {
    if (a.value != "") {
        document.getElementById("ciclo").readOnly = false;
        document.getElementById("ciclo").value = "";
    } else {
        document.getElementById("ciclo").readOnly = true;
    }
}


function iniciaRegistro() {
    if (validaDatos()) generaImpreso();
}

function cancelaRegistro() {
    window.history.back();
}

// JavaScript Document