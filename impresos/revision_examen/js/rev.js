///REVISION DE EXAMEN
var backup_nombre = "";
id_nif = "";
nombre = "";
apellidos = "";
email = "";

$(document).ready(function() {
    $.post("../../php/sesion.php", { tipo_usu: "usuario" }, function(resp) {
        if (resp["error"] != "ok") document.write(resp["error"]);
        else {
            id_nie = resp["id_nie"];
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
    var ER_cp = /[0-9]{5}/;
    var ER_email = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    var nom = document.getElementById("nombre").value.miTrim();
    var nif = document.getElementById("nif_nie").value.miTrim();
    var alum = document.getElementById("alum").value;
    var curso = document.getElementById("curso").value;
    var profesor = document.getElementById("profesor").value;
    var departamento = document.getElementById("dpto").value;
    var asignatura = document.getElementById("asignatura").value;
    var fecha = document.getElementById("fecha").value;
    if (nom.length == 0 || nom == "Seleccione en el desplegable de la izquierda.") {
        lista_errores += "- El Nombre no puede estar vacío.<br>";
    }
    if (nif.length == 0 || (!validaDNI_NIE(nif) && document.getElementById('nif').checked)) {
        lista_errores += "- Número de documento de identificación vacío o incorrecto.<br>";
    }
    if (alum.length == 0 && !document.getElementById('alumno').checked) {
        lista_errores += "- Nombre y apellidos del alumno en blanco.<br>";
    }
    if (curso.length == 0) {
        lista_errores += "- Debe poner el curso que cursa el alumno.<br>";
    }
    if (profesor.length == 0) {
        lista_errores += "- Debe poner el nombre del profesor examinador.<br>";
    }
    if (departamento.length == 0) {
        lista_errores += "- No se ha puesto el departamento al que se reclama.<br>";
    }
    if (asignatura.length == 0) {
        lista_errores += "- No se ha puesto la asignatura/módulo sobre la que se reclama el examen.<br>";
    }
    if (fecha.length == 0) {
        lista_errores += "- Hay que poner la fecha en la que se realizó el examen.<br>";
    }

    if (lista_errores.length > 0) {
        lista_errores = "ERRORES DETECTADOS EN EL FORMULARIO<br>" + lista_errores;
        alerta(lista_errores, "¡FALTAN DATOS!");
        return false;
    }
    return true;
}




function generaImpreso() {
    //document.getElementById('rev_exa').submit();return;
    //$.ajaxSetup({async:false});
    document.getElementById("cargando").style.display = 'inline-block';
    var pet = $.ajax({
        url: "php/generapdf.php",
        type: "POST",
        data: $("#rev_exa").serialize()
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
        } else if (resp.indexOf("envio_fallido") != -1) {
            num_reg = resp.slice(14);
            mensaje = "No se ha podido enviar a su correo el impreso y número de registro.";
            mensaje += "Aún así, parece que su impreso se ha registrado correctamente con el nº:<br><b>";
            mensaje += num_reg + "</b><br>";
        } else if (resp == "no_file") {
            alerta("Ha habido un error y no se ha podido generar el fichero con el formulario registrado.", "Error en servidor");
        } else if (resp.indexOf("envio_ok") != -1) {
            document.getElementById("num_registro").value = resp.slice(8);
            document.rev_exa.reset();
            alerta("Proceso finalizado correctamente.<br>Se le ha enviado el documento registrado a su correo junto con el número de registro asignado.<br>Si no ve el correo, revise la carpeta spam o correo no deseado.<br>También verá que el documento se ha descargado en el navegador en formato PDF.", "Registro correcto", true);
        }
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

function habAlumno() {
    if (document.getElementById("alumno").checked) {
        document.getElementById("en_calidad_de").style.display = "none";
        document.getElementById("alum").readOnly = true;
        document.getElementById("alum").value = "";
    }
    if (document.getElementById("padre").checked || document.getElementById("tutor").checked) {
        document.getElementById("alum").readOnly = false;
        document.getElementById("en_calidad_de").style.display = "block";
    }

}


function iniciaRegistro() {
    if (validaDatos()) generaImpreso();
}

function cancelaRegistro() {
    window.history.back();
}
// JavaScript Document