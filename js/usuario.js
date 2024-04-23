var id_nie = "";
var id_nif = "";
var nombre = "";
var apellidos = "";
var email = "";
var premat_eso = false;
var premat_bach = false;
var mat_eso = false;
var mat_bach = false;
var mat_ciclos = false;
var mat_fpb = false;
var anno_ini_curso;
var anno_curso_usu;
var mes_sesion;
var dia_sesion;
var tipo_matricula;
var datos_usu_vacios=false;
var primera_carga=true;



$(function() {
    document.getElementById("cargando").style.display = "inherit";
    if (document.location.hostname!="registro.ulaboral.org")document.getElementById("servidor_pruebas").style.display="inherit";
    else document.getElementById("servidor_pruebas").style.display="none";
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
        maxDate: "-11y",
        nextText: "Siguiente",
        prevText: "Previo"
    });

    dat1 = Promise.resolve($.post("php/sesion.php", { tipo_usu: "usuario" }, () => {}, "json"));
    dat2=dat1.then((resp)=> {
        if (resp["error"] != "ok") document.write(resp["error"]);
        else {
            $("#cargando").css("display", "inherit");
            id_nie = resp["id_nie"];
            id_nif = resp["id_nif"];
            nombre = resp["nombre"];
            apellidos = resp["apellidos"];
            email = resp["email"];
            mes_sesion = resp["mes"];
            dia_sesion = resp["dia"];
            anno_ini_curso = resp["anno_ini_curso"];
            if (mes_sesion != 6) anno_curso_usu = (anno_ini_curso) + "-" + (anno_ini_curso + 1);
            else anno_curso_usu = (anno_ini_curso + 1) + "-" + (anno_ini_curso + 2);
            primera_carga=resp["primera_carga"];
        }
        return $.post("php/usu_verificapremat.php", {}, () => {}, "json");
    });
    dat3=dat2.then((resp)=>{
        if (resp["eso"] == 1) {
            premat_eso = true;
            document.getElementById("docs_premat_eso").setAttribute('href', "impresos/prematriculas/premat_eso.php?q=" + Date.now().toString());
            document.getElementById("docs_premat_eso").className = "enlaceEnabled";
        } else {
            premat_eso = false;
            document.getElementById("docs_premat_eso").setAttribute('href', "#");
            document.getElementById("docs_premat_eso").className = "enlaceDisabled";
        }

        if (resp["bach"] == 1) {
            premat_bach = true;
            document.getElementById("docs_premat_bach").setAttribute('href', "impresos/prematriculas/premat_bach.php?q=" + Date.now().toString());
            document.getElementById("docs_premat_bach").className = "enlaceEnabled";
        } else {
            premat_bach = false;
            document.getElementById("docs_premat_bach").setAttribute('href', "#");
            document.getElementById("docs_premat_bach").className = "enlaceDisabled";
        }
        return $.post("php/usu_verificamat.php", {}, () => {}, "json");
    });

    dat4=dat3.then((resp)=> {
        if (resp["eso"] == 1) {
            mat_eso = true;
            document.getElementById("docs_mat_eso").setAttribute('onclick', "lanzaAvisoMatricula('eso')");
            document.getElementById("docs_mat_eso").className = "enlaceEnabled";
        } else {
            mat_eso = false;
            document.getElementById("docs_mat_eso").setAttribute('onclick', "");
            document.getElementById("docs_mat_eso").className = "enlaceDisabled";
        }

        if (resp["bach"] == 1) {
            mat_bach = true;
            document.getElementById("docs_mat_bach").setAttribute('onclick', "lanzaAvisoMatricula('bach')");
            document.getElementById("docs_mat_bach").className = "enlaceEnabled";
        } else {
            mat_bach = false;
            document.getElementById("docs_mat_bach").setAttribute('onclick', "");
            document.getElementById("docs_mat_bach").className = "enlaceDisabled";
        }

        if (resp["ciclos"] == 1) {
            mat_ciclos = true;
            document.getElementById("docs_mat_ciclos").setAttribute('onclick', "lanzaAvisoMatricula('ciclos')");
            document.getElementById("docs_mat_ciclos").className = "enlaceEnabled";
        } else {
            mat_ciclos = false;
            document.getElementById("docs_mat_ciclos").setAttribute('onclick', "");
            document.getElementById("docs_mat_ciclos").className = "enlaceDisabled";
        }

        if (resp["ciclo_e"] == 1) {
            mat_ciclos = true;
            document.getElementById("docs_mat_ciclos-e").setAttribute('onclick', "lanzaAvisoMatricula('ciclos-e')");
            document.getElementById("docs_mat_ciclos-e").className = "enlaceEnabled";
        } else {
            mat_ciclos = false;
            document.getElementById("docs_mat_ciclos-e").setAttribute('onclick', "");
            document.getElementById("docs_mat_ciclos-e").className = "enlaceDisabled";
        }

        if (resp["fpb"] == 1) {
            mat_ciclos = true;
            document.getElementById("docs_mat_fpb").setAttribute('onclick', "lanzaAvisoMatricula('fpb')");
            document.getElementById("docs_mat_fpb").className = "enlaceEnabled";
        } else {
            mat_ciclos = false;
            document.getElementById("docs_mat_fpb").setAttribute('onclick', "");
            document.getElementById("docs_mat_fpb").className = "enlaceDisabled";
        }
        return $.post("php/usu_recdatospers.php", {id_nie: id_nie}, () => {}, "json");
    });

    dat5=dat4.then((resp) => {
        if (resp.error == "ok") {
            for (e in resp.datos) {
                if (typeof(resp.datos[e]) == "undefined" || resp.datos[e]==null) resp.datos[e] = "";
            }
            f_nac = resp.datos.fecha_nac;
            if (f_nac!="") f_nac = f_nac.substr(8, 2) + "/" + f_nac.substr(5, 2) + "/" + f_nac.substr(0, 4);
            form_mod_datos.dat_sexo.value = resp.datos.sexo;
            form_mod_datos.dat_fecha_nac.value = f_nac;
            form_mod_datos.dat_telefono.value = resp.datos.telef_alumno;
            form_mod_datos.dat_email.value = resp.datos.email;
            form_mod_datos.dat_direccion.value = resp.datos.direccion;
            form_mod_datos.dat_cp.value = resp.datos.cp;
            form_mod_datos.dat_localidad.value = resp.datos.localidad;
            form_mod_datos.dat_provincia.value = resp.datos.provincia;
            form_mod_datos.dat_tutor1.value = resp.datos.tutor1;
            form_mod_datos.dat_telef_tut1.value = resp.datos.tlf_tutor1;
            form_mod_datos.dat_email_tut1.value = resp.datos.email_tutor1;
            form_mod_datos.dat_tutor2.value = resp.datos.tutor2;
            form_mod_datos.dat_telef_tut2.value = resp.datos.tlf_tutor2;
            form_mod_datos.dat_email_tut2.value = resp.datos.email_tutor2;
        }
        if (resp.error != "ok" || (resp.datos.direccion=="" && resp.datos.localidad=="" && resp.datos.provincia=="")){
            datos_usu_vacios=true;
        }
       
        document.getElementById("cargando").style.display = "none";
        if (datos_usu_vacios && primera_carga)alerta("Es recomendable cumplimentar los datos en 'Mis datos', en la parte superior del menú.<br>Le facilitará el trabajo a la hora de cumplimentar los formularios.","SUGERENCIA");
        listaSolicitudes();
    });


    $("#div_mod_datos").dialog({
        autoOpen: false,
        dialogClass: "alert no-close",
        modal: true,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        title: "MODIFICACIÓN DATOS ALUMNO",
        maxHeight: 800,
        width: 800,
        buttons: [{
                class: "btn btn-success textoboton",
                text: "Aceptar",
                click: function() {
                    modificaDatos();
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Cancelar",
                click: function() {
                    $(this).dialog("close");
                    $("#form_mod_datos").trigger("reset");
                }
            }
        ]
    });

    $("#div_mod_pass").dialog({
        autoOpen: false,
        dialogClass: "alert no-close",
        modal: true,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        title: "MODIFICACIÓN CONTRASEÑA",
        maxHeight: 500,
        width: 400,
        buttons: [{
                class: "btn btn-success textoboton",
                text: "Aceptar",
                click: function() {
                    modificaPass();
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Cancelar",
                click: function() {
                    $(this).dialog("close");
                }
            }
        ]
    });


    $("#div_aviso_inicio_mat").dialog({
        autoOpen: false,
        dialogClass: "alert no-close",
        modal: true,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        title: "¡¡¡IMPORTANTE!!!",
        maxHeight: 850,
        width: 850,
        buttons: [{
            class: "btn btn-success textoboton",
            text: "Continuar",
            click: function() {
                $("#div_aviso_inicio_mat").dialog("close");
                if (tipo_matricula == "eso") {
                    window.open("impresos/matriculas/mat_eso.php?q=" + Date.now().toString(), "_self");
                } else if (tipo_matricula == "bach") {
                    window.open("impresos/matriculas/mat_bach.php?q=" + Date.now().toString(), "_self");
                } else if (tipo_matricula == "ciclos") {
                    window.open("impresos/matriculas/mat_ciclos.php?q=" + Date.now().toString(), "_self");
                } else if (tipo_matricula == "fpb") {
                    window.open("impresos/matriculas/mat_fpb.php?q=" + Date.now().toString(), "_self");
                } else if (tipo_matricula == "ciclos-e") {
                    window.open("impresos/matriculas/mat_ciclos-e.php?q=" + Date.now().toString(), "_self");
                } 
            }
        }]
    });

    $("#div_subida_archivos_usu").dialog({
        autoOpen: false,
        dialogClass: "alert no-close",
        modal: true,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        title: "SUBIDA DE DOCUMENTOS",
        maxHeight: 500,
        width: 600,
        buttons: [{
            class: "btn btn-success textoboton",
            text: "Cerrar",
            click: function() {
                document.getElementById("subida_doc").reset();
                $("#div_subida_archivos_usu").dialog("close");
            }
        }]
    });

    jQuery.validator.addMethod("nif_noduplicado", function(value, element) {
        if (value.miTrim() == '') return true;
        $.ajaxSetup({ async: false });
        var a = $.post("php/usu_nifduplicado.php", { nu_nif: value, id_nie: id_nie }, function(resp) {
            if (resp == "ok") _nif_duplicado = true;
            else _nif_duplicado = false;
        });
        $.ajaxSetup({ async: true });
        return _nif_duplicado;
    });

    $("#form_mod_datos").validate({
        rules: {
            nif_nie: {
                numero_nif: true
            },
            dat_email: {
                email: true
            },
            dat_email_tut1: {
                email: true
            },
            dat_email_tut2: {
                email: true
            }
        },
        messages: {
            nif_nie: {
                numero_nif: "NIF incorrecto."
            },
            dat_email: {
                email: "No es una dirección de correo electrónico."
            },
            dat_email_tut1: {
                email: "No es una dirección de correo electrónico."
            },
            dat_email_tut2: {
                email: "No es una dirección de correo electrónico."
            }
        },
        errorPlacement: function(error, element) {
            $(element).prev($('.errorTxt')).html(error);
        }
    });

    jQuery.validator.addMethod("password", function(value, element) {
        return /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$/.test(value);
    });

    jQuery.validator.addMethod("rep_password", function(value, element) {
        return document.getElementById("p1").value === value ? true : false;
    });

    /*jQuery.validator.addMethod("rep_email", function(value, element) {
        return document.getElementById("email").value === value ? true : false;
    });*/

    $("#form_cambioPass").validate({
        rules: {
            p1: {
                required: true,
                minlength: 8,
                password: true
            },
            p2: {
                required: true,
                rep_password: true
            }
        },
        messages: {
            p1: {
                required: "Debe completar el campo.",
                minlength: "Debe contener 8 caracteres como mínimo.",
                password: "Debe contener, al menos, una minúscula, una mayúscula y un número."
            },
            p2: {
                required: "Debe completar el campo.",
                rep_password: "Las contraseñas no coinciden."
            }
        },
        errorPlacement: function(error, element) {
            $(element).prev($('.errorTxt')).html(error);
        }
    });

    /*document.getElementById("rep_email").onpaste = function(e) {
        e.preventDefault();
        alerta('Esta acción está prohibida. Introduzca manualmente el email.', 'PEGAR');
    }*/

    $("#apartados").tabs({
        active: 0
    });
});



function cambioDatosPers() {
    $.post("php/usu_recdatospers.php", { id_nie: id_nie }, (resp) => {
        if (resp.error == "ok") {
            for (e in resp.datos) {
                if (typeof(resp.datos[e]) == "undefined" || resp.datos[e]==null) resp.datos[e] = "";
            }
            f_nac = resp.datos.fecha_nac;
            if (f_nac!="") f_nac = f_nac.substr(8, 2) + "/" + f_nac.substr(5, 2) + "/" + f_nac.substr(0, 4);
            form_mod_datos.dat_sexo.value = resp.datos.sexo;
            form_mod_datos.dat_fecha_nac.value = f_nac;
            form_mod_datos.dat_telefono.value = resp.datos.telef_alumno;
            form_mod_datos.dat_email.value = resp.datos.email;
            form_mod_datos.dat_direccion.value = resp.datos.direccion;
            form_mod_datos.dat_cp.value = resp.datos.cp;
            form_mod_datos.dat_localidad.value = resp.datos.localidad;
            form_mod_datos.dat_provincia.value = resp.datos.provincia;
            form_mod_datos.dat_tutor1.value = resp.datos.tutor1;
            form_mod_datos.dat_telef_tut1.value = resp.datos.tlf_tutor1;
            form_mod_datos.dat_email_tut1.value = resp.datos.email_tutor1;
            form_mod_datos.dat_tutor2.value = resp.datos.tutor2;
            form_mod_datos.dat_telef_tut2.value = resp.datos.tlf_tutor2;
            form_mod_datos.dat_email_tut2.value = resp.datos.email_tutor2;
            $("#div_mod_datos").dialog("open");
        } else if (resp.error == "server") {
            alerta("No se han podido recuperar los datos del usuario.", "ERROR BASE DE DATOS");
        } 
        else if(resp.error == "no_usuarios"){
            $("#div_mod_datos").dialog("open");
        }
    }, "json");

}


function modificaDatos() {
    document.getElementById("dat_idnie").value = id_nie;
    if ($("#form_mod_datos").valid()) {
        $.post("php/usu_moddatospers.php", $("#form_mod_datos").serialize(), function(resp) {
            if (resp === "ok") {
                alerta("Los cambios se han realizado con éxito.", "Operación OK");
                $("#div_mod_datos").dialog("close");
            } else alerta("Ha ocurrido un problema y los cambios no se han podido realizar.<br>Inténtelo en otro momento.", "FALLO EN OPERACIÓN");
        });
    }
}


function modificaPass() {
    if ($("#form_cambioPass").valid()) {
        $.post("php/usu_modificausu.php", {
                procedimiento: "password",
                password: $("#p1").val(),
                id_nie: id_nie
            },
            function(resp) {
                if (resp == "ok") {
                    document.getElementById("form_cambioPass").reset();
                    alerta("La contraseña ha sido cambiada.", "Operación OK");
                    $("#div_mod_pass").dialog("close");
                } else alerta("Ha ocurrido un problema y la contraseñano se ha podido cambiar.<br>Inténtelo en otro momento.", "FALLO EN OPERACIÓN");
            });
    }
}

function cierraSesion() {
    $.post("php/logout.php", {}, function(resp) {
        open("index.php?q=" + Date.now().toString(), "_self");
    });
}

function confirmarBaja(mensaje, titulo, botonAceptar) {
    document.getElementById('mensaje_div').innerHTML = mensaje;
    $("#mensaje_div").dialog({
        title: titulo,
        autoOpen: false,
        dialogClass: "alert no-close",
        modal: true,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        buttons: [{
                class: "btn btn-success textoboton",
                text: botonAceptar,
                click: function() {
                    $(this).dialog("close");
                    if (botonAceptar == "Borrar") {
                        confirmarBaja("El proceso es irreversible.<br>¿Está seguro que desea causar baja en el sistema?", "¡¡¡ATENCIÓN!!!", "Confirmar Baja");
                    } else confirmadoBorradoCuenta();
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

function listaSolicitudes() {
    listado = document.getElementById("solicitudes");
    $.post("php/usu_listasolicitudes.php", { id_nie: id_nie }, function(data) {
        $("#cargando").css("display", "none");
        if (data["error"] == "noregistros") listado.innerHTML = "NO HAY REGISTRADAS SOLICITUDES";
        else if (data["error"] == "ok") {
            tabla = "<tr><th>Curso</th><th>Fecha</th><th>Número de Registro</th><th>Observaciones</th></tr>";
            Object.keys(data.proceso).forEach(function(proc) {
                tabla += "<tr><th colspan='5' style='text-align:left'><label style='font-size:1em;color:black !important'>" + proc.toUpperCase() + "</label></th></tr>";
                for (i = 0; i < data["proceso"][proc].length; i++) {
                    item = data["proceso"][proc][i];
                    f_reg = item["fecha_registro"];
                    tabla += "<tr><td style='width:90px;text-align:center;color:blue'>" + item["curso"] + "</td>";
                    tabla += "<td style='width:90px;text-align:center;color:blue'>" + f_reg.substr(8, 2) + "-" + f_reg.substr(5, 2) + "-" + f_reg.substr(0, 4) + "</td>";
                    if (proc=="Convalidaciones"){
                        tabla += "<td style='color:blue'><a style='color:blue' href='docs/"+id_nie+"/convalidaciones/"+ item["curso"] + "/"+item["registro"].slice(17)+"/"+ item["registro"] + ".pdf' target='_blank'>" + item["registro"] + "</a>";
                        if (item["resolucion"]=="FAVORABLE"){
                            tabla += "<a style='margin-left:10px;color:GREEN' href='docs/"+id_nie+"/convalidaciones/"+ item["curso"] + "/"+item["registro"].slice(17)+"/docs/resolucion/resolucion.pdf' target='_blank' title='Ver resolución'>(Resolución FAVORABLE)</a>"
                        }
                        else if(item["resolucion"]=="NO FAVORABLE"){
                            tabla += "<a style='margin-left:10px;color:BROWN' href='docs/"+id_nie+"/convalidaciones/"+ item["curso"] + "/"+item["registro"].slice(17)+"/docs/resolucion/resolucion.pdf' target='_blank' title='Ver resolución'>(Resolución NO FAVORABLE)</a>"
                        }
                        else if(item["resolucion"]=="PARCIAL"){
                            tabla += "<a style='margin-left:10px;color:ORANGE' href='docs/"+id_nie+"/convalidaciones/"+ item["curso"] + "/"+item["registro"].slice(17)+"/docs/resolucion/resolucion.pdf' target='_blank' title='Ver resolución'>(Resolución PARCIAL)</a>"
                        }
                        else{
                            tabla += " (Resolución EN ESPERA)"
                        }
                        tabla+="</td>";
                    }
                    else{
                        tabla += "<td style='color:blue'><a style='color:blue' href='docs/"+id_nie+"/"+item["dir"]+"/"+ item["curso"] + "/"+ item["registro"] + ".pdf' target='_blank'>" + item["registro"] + "</a></td>";
                    }
                    if (item["incidencias"].miTrim() != "") tabla += "<td style='text-align:center;color:blue !important'><a style='color:blue' href=javascript:alerta('" + item["incidencias"] + "','OBSERVACIONES')>Ver</a></td></tr>";
                    else tabla += "<td style='text-align:center;color:blue'>-</td></tr>";
                };
            });
            listado.innerHTML = tabla;
        }
    }, "json");
}

function extraeFechaDeRegistro(registro) {
    f = registro.substr(registro.length - 17 - 1, 8);
    fecha = f.substr(0, 2) + "-" + f.substr(2, 2) + "-" + f.substr(4, 4);
    return fecha;
}


/*
function USUsubeFoto(obj) {
    if (obj.files[0].type != "image/jpeg") {
        alerta("Formato de archivo no válido", "NO VÁLIDO");
        obj.value = null;
        return;
    }

    datos = new FormData();
    datos.append("foto", obj.files[0]);
    datos.append("id_nie", id_nie);
    document.getElementById("cargando").style.display = 'inline-block';
    $.ajax({
            url: "impresos/matriculas/php/sube_foto.php",
            type: 'POST',
            data: datos,
            contentType: false,
            processData: false,
            cache: false
        })
        .done(function(resp) {
            document.getElementById("cargando").style.display = 'none';
            if (resp == "archivo") {
                alerta("Ha habido un error al subir el archivo.", "Error carga");
                obj.value = null;
            } else if (resp == "almacenar") {
                alerta("Ha habido un error al copiar el archivo.", "Error copia");
                obj.value = null;
            } else if (resp == "ok") alerta("Fotografía subida.", "OK");

        });
}


function USUsubeSeguro(obj) {
    if (obj.files[0].type != "image/jpeg") {
        obj.value = null;
        alerta("Formato de archivo no válido", "NO VÁLIDO");
        return;
    }

    datos = new FormData();
    datos.append("seguro", obj.files[0]);
    datos.append("id_nie", id_nie);
    datos.append("anno_curso", anno_curso_usu);
    document.getElementById("cargando").style.display = 'inline-block';
    $.ajax({
            url: "impresos/matriculas/php/sube_seguro.php",
            type: 'POST',
            data: datos,
            contentType: false,
            processData: false,
            cache: false
        })
        .done(function(resp) {
            document.getElementById("cargando").style.display = 'none';
            if (resp == "archivo") {
                alerta("Ha habido un error al subir el archivo.", "Error carga");
                obj.value = null;
            } else if (resp == "almacenar") {
                alerta("Ha habido un error al copiar el archivo.", "Error copia");
                obj.value = null;
            } else if (resp == "ok") {
                alerta("Resguardo de seguro escolar subido.", "OK");
            }
        });
}


function USUsubeDNI(obj, parte) {
    if (obj.files[0].type != "image/jpeg") {
        obj.value = null;
        alerta("Formato de archivo no válido", "NO VÁLIDO");
        return;
    }
    datos = new FormData();
    datos.append("dni", obj.files[0]);
    datos.append("id_nie", id_nie);
    datos.append("parte", parte); //Anverso -> A   Reverso-> R
    document.getElementById("cargando").style.display = 'inline-block';
    $.ajax({
            url: "impresos/matriculas/php/sube_dni.php",
            type: 'POST',
            data: datos,
            contentType: false,
            processData: false,
            cache: false
        })
        .done(function(resp) {
            document.getElementById("cargando").style.display = 'none';
            if (resp == "archivo") {
                alerta("Ha habido un error al subir el archivo.", "Error carga");
                obj.value = null;
            } else if (resp == "almacenar") {
                alerta("Ha habido un error al copiar el archivo.", "Error copia");
                obj.value = null;
            } else if (resp == "ok") {
                if (parte == "A") mm = "Anverso de documento subido.";
                else mm = "Reverso de documento subido.";
                alerta(mm, "OK");
            }
        });
}
*/

function USUsubeCertificado(obj) {
    if (obj.files[0].type != "application/pdf") {
        obj.value = null;
        alerta("Formato de archivo no válido", "NO VÁLIDO");
        return;
    }

    datos = new FormData();
    datos.append("certificado", obj.files[0]);
    datos.append("id_nie", id_nie);
    datos.append("anno_curso", anno_curso_usu);
    document.getElementById("cargando").style.display = 'inline-block';
    $.ajax({
            url: "impresos/matriculas/php/sube_certificado.php",
            type: 'POST',
            data: datos,
            contentType: false,
            processData: false,
            cache: false
        })
        .done(function(resp) {
            document.getElementById("cargando").style.display = 'none';
            if (resp == "archivo") {
                alerta("Ha habido un error al subir el archivo.", "Error carga");
                obj.value = null;
            } else if (resp == "almacenar") {
                alerta("Ha habido un error al copiar el archivo.", "Error copia");
                obj.value = null;
            } else if (resp == "ok") {
                alerta("Certificado subido.", "OK");
            }
        });
}


function ocultaDivsSubeDocs(panel) {
    if (panel == "foto") {
        document.getElementById("div_fotografia").style.display = "inherit";
        document.getElementById("div_resguardo_seguro_escolar").style.display = "none";
        document.getElementById("div_anverso_dni").style.display = "none";
        document.getElementById("div_reverso_dni").style.display = "none";
        document.getElementById("div_certificado").style.display = "none";
    } else if (panel == "seguro") {
        document.getElementById("div_fotografia").style.display = "none";
        document.getElementById("div_resguardo_seguro_escolar").style.display = "inherit";
        document.getElementById("div_anverso_dni").style.display = "none";
        document.getElementById("div_reverso_dni").style.display = "none";
        document.getElementById("div_certificado").style.display = "none";
    } else if (panel == "dni") {
        document.getElementById("div_fotografia").style.display = "none";
        document.getElementById("div_resguardo_seguro_escolar").style.display = "none";
        document.getElementById("div_anverso_dni").style.display = "inherit";
        document.getElementById("div_reverso_dni").style.display = "inherit";
        document.getElementById("div_certificado").style.display = "none";
    } else if (panel == "certificado") {
        document.getElementById("div_fotografia").style.display = "none";
        document.getElementById("div_resguardo_seguro_escolar").style.display = "none";
        document.getElementById("div_anverso_dni").style.display = "none";
        document.getElementById("div_reverso_dni").style.display = "none";
        document.getElementById("div_certificado").style.display = "inherit";
    }
}


function lanzaAvisoMatricula(nivel_educ) {
    tipo_matricula=nivel_educ;
    if(nivel_educ=="eso"){
        mensaje = "<p>Por favor, tenga preparados los siguientes documentos:";
        mensaje += "<ul>";
        mensaje += "    <li>Una fotografía del alummno en formato JPEG tomada con móvil en vertical y fondo blanco, como se muestra en la imagen:<br><center><img src='recursos/foto_carne.jpg'  style='width:128px;'></center></li>";
        mensaje += "    <li>Fotografía del anverso y reverso del documento de identificación (DNI/NIE). Si sólo tiene pasaporte, el anverso será imagen JPEG de la página en la que salen los datos del alumno y su fotografía, y el reverso imagen JPEG en blanco. Los alumnos de 1º, 2º y 2º PMAR de ESO podrán subir las imágenes del documento si disponen de él, no siendo obligatorio en estos casos. El documento se fotografiará con el móvil en horizontal y fondo blanco, por ejemplo, poniendo el documento sobre un folio en blanco.</li>";
        mensaje += "    <li>Si la matrícula es para 3º de ESO, 3º de ESO DIVERSIFICACIÓN o 4º de ESO, prepare también en formato JPEG una fotografía del resguardo del pago del seguro escolar.</li>";
        mensaje += "    <li>Si es alumno nuevo e inició los estudios de los que se matricula en otra comunidad autónoma, certificado de notas en formato PDF (puede escanearlo, por ejemplo, con la aplicación gratuita para móvil Microsoft Office Lens).</li>";
        mensaje += "</ul>";
        mensaje += "</p>";
    }
    else if(nivel_educ=="bach"){
        mensaje = "<p>Por favor, tenga preparados los siguientes documentos:";
        mensaje += "<ul>";
        mensaje += "    <li>Una fotografía del alummno en formato JPEG tomada con móvil en vertical y fondo blanco, como se muestra en la imagen:<br><center><img src='recursos/foto_carne.jpg'  style='width:128px;'></center></li>";
        mensaje += "    <li>Fotografía del anverso y reverso del documento de identificación (DNI/NIE). Si sólo tiene pasaporte, el anverso será imagen JPEG de la página en la que salen los datos del alumno y su fotografía, y el reverso imagen JPEG en blanco. El documento se fotografiará con el móvil en horizontal y fondo blanco, por ejemplo, poniendo el documento sobre un folio en blanco.</li>";
        mensaje += "    <li>Una fotografía del resguardo del pago del seguro escolar, y del anverso y reverso del documento de identificación (DNI/NIE). (Móvil en horizontal y fondo blanco, por ejemplo, sobre un folio).</li>";
        mensaje += "    <li>Si es alumno nuevo e inició los estudios de los que se matricula en otra comunidad autónoma, certificado de notas en formato PDF (puede escanearlo, por ejemplo, con la aplicación gratuita para móvil Microsoft Office Lens).</li>";
        mensaje += "</ul>";
        mensaje += "</p>";            
    }
    else if(nivel_educ=="ciclos" || nivel_educ=="ciclos-e"){
        anno_seguro = anno_ini_curso - 27;
        mensaje = "<p>Por favor, tenga preparados los siguientes documentos:";
        mensaje += "<ul>";
        mensaje += "    <li>Una fotografía del alummno en formato JPEG tomada con móvil en vertical y fondo blanco, como se muestra en la imagen:<br><center><img src='recursos/foto_carne.jpg'  style='width:128px;'></center></li>";
        mensaje += "    <li>Fotografía del anverso y reverso del documento de identificación (DNI/NIE). Si sólo tiene pasaporte, el anverso será imagen JPEG de la página en la que salen los datos del alumno y su fotografía, y el reverso imagen JPEG en blanco. El documento se fotografiará con el móvil en horizontal y fondo blanco, por ejemplo, poniendo el documento sobre un folio en blanco.</li>";
        mensaje += "    <li>EXCEPTO nacidos antes del 31/12/" + anno_seguro + ", una fotografía del resguardo del pago del seguro escolar, y del anverso y reverso del documento de identificación (DNI/NIE). (Móvil en horizontal y fondo blanco, por ejemplo, sobre un folio).</li>";
        mensaje += "    <li>Si es alumno nuevo e inició los estudios de los que se matricula en otra comunidad autónoma, certificado de notas en formato PDF (puede escanearlo, por ejemplo, con la aplicación gratuita para móvil Microsoft Office Lens).</li>";
        mensaje += "</ul>";
        mensaje += "</p>";            
    }
    else if(nivel_educ=="fpb"){
        mensaje = "<p>Por favor, tenga preparados los siguientes documentos:";
        mensaje += "<ul>";
        mensaje += "    <li>Una fotografía del alummno en formato JPEG tomada con móvil en vertical y fondo blanco, como se muestra en la imagen:<br><center><img src='recursos/foto_carne.jpg'  style='width:128px;'></center></li>";
        mensaje += "    <li>Fotografía del anverso y reverso del documento de identificación (DNI/NIE). Si sólo tiene pasaporte, el anverso será imagen JPEG de la página en la que salen los datos del alumno y su fotografía, y el reverso imagen JPEG en blanco. El documento se fotografiará con el móvil en horizontal y fondo blanco, por ejemplo, poniendo el documento sobre un folio en blanco.</li>";
        mensaje += "    <li>Una fotografía del resguardo del pago del seguro escolar, y del anverso y reverso del documento de identificación (DNI/NIE). (Móvil en horizontal y fondo blanco, por ejemplo, sobre un folio).</li>";
        mensaje += "    <li>Si es alumno nuevo e inició los estudios de los que se matricula en otra comunidad autónoma, certificado de notas en formato PDF (puede escanearlo, por ejemplo, con la aplicación gratuita para móvil Microsoft Office Lens).</li>";
        mensaje += "</ul>";
        mensaje += "</p>";            
    }
    document.getElementById("div_aviso_inicio_mat").innerHTML = mensaje;
    $('#div_aviso_inicio_mat').dialog('open');
}

function muestraEditor_usu(_file,tipo){
    if (tipo=="dni_anverso" || tipo=="dni_reverso"){
        document.getElementById("texto_editor_imagen").innerHTML="Rota, haz zoom (con la rueda del ratón) y mueve la imagen para ajustar la CARA y CUELLO al recuadro";
        _crop1=new Croppie(document.getElementById("div_imagen"), {
            viewport: { width: 450, height: 285 },
            boundary: { width: 675, height: 383 },
            showZoomer: false,
            enableOrientation: true
        });
        _fname_ajax="dni";
        if(tipo=="dni_anverso")_f_ajax=id_nie+"-A.jpeg";
        else _f_ajax=id_nie+"-R.jpeg";
        url="impresos/matriculas/php/sube_dni.php";
        __ancho=700;
    }
    else if(tipo=="foto"){
        document.getElementById("texto_editor_imagen").innerHTML="Rota, haz zoom (con la rueda del ratón) y mueve la imagen para ajustarla al recuadro";
        _crop1=new Croppie(document.getElementById("div_imagen"), {
            viewport: { width: 190, height: 255 },
            boundary: { width: 300, height: 450 },
            showZoomer: false,
            enableOrientation: true
        });
        _fname_ajax="foto";
        _f_ajax=id_nie+".jpeg";
        url="impresos/matriculas/php/sube_foto.php";
        __ancho=500;
    }
    else if(tipo=="seguro"){
        document.getElementById("texto_editor_imagen").innerHTML="Rota, haz zoom (con la rueda del ratón) en la imagen, y ajusta el recuadro al resguardo del seguro escolar. NO IMPORTA QUE EL RESGUARDO SE VEA EN HORIZONTAL, si es el caso";
        _crop1=new Croppie(document.getElementById("div_imagen"), {
            viewport: { width: 630, height: 350 },
            boundary: { width: 675, height: 500 },
            showZoomer: false,
            enableOrientation: true
        });
        _fname_ajax="seguro";
        _f_ajax=id_nie+".jpeg";
        url="impresos/matriculas/php/sube_seguro.php";
        __ancho=1000;
    }
    _crop1.bind({
        url: URL.createObjectURL(_file),
        orientation: 1
    });

    $("#div_edita_imagen_usu").dialog({
        autoOpen: true,
        dialogClass: "alert no-close",
        modal: true,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        title: "EDICIÓN IMAGEN",
        width: __ancho,
        buttons: [
            {
                class: "btn btn-success textoboton",
                text: "Girar +90º",
                click: function() {
                    _crop1.rotate(-90);
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Girar -90º",
                click: function() {
                    _crop1.rotate(90);
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Cancelar",
                click: function() {
                    _crop1.destroy();
                    $("#div_edita_imagen_usu").dialog("destroy");
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Aceptar",
                click: function() {
                    _crop1.result({
                        type: 'blob'
                    }).then(function (blob) {
                        return fetch(window.URL.createObjectURL(blob))
                    }).then(function (response) {
                        return response.blob();
                    }).then(function (blob) {
                        formData= new FormData();
                        formData.append(_fname_ajax, blob, _f_ajax);
                        formData.append("id_nie",id_nie);
                        if (tipo=="dni_anverso")formData.append("parte","A");
                        else if(tipo=="dni_reverso")formData.append("parte","R");
                        if(tipo=="seguro") formData.append("anno_curso", anno_curso);
                        document.getElementById("cargando").style.display = 'inherit';
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            cache: false
                        })
                        .done(function(resp) {
                            document.getElementById("cargando").style.display = 'none';
                            if (resp == "archivo") {
                                alerta("Ha habido un error al subir el archivo.", "Error carga");
                                obj.value = null;
                            } else if (resp == "almacenar") {
                                alerta("Ha habido un error al copiar el archivo.", "Error copia");
                                obj.value = null;
                            } else if (resp == "ok") {
                                if (tipo == "dni_anverso"){
                                    mm = "Anverso de documento subido.";
                                }
                                else if (tipo == "dni_reverso"){
                                    mm = "Reverso de documento subido.";
                                }
                                else if (tipo == "foto"){
                                    mm = "Fotografía subida.";
                                }
                                else if (tipo == "seguro"){
                                    mm = "Resguardo del pago del seguro escolar subido.";
                                }
                                alerta(mm, "OK");
                            }
                        });
                    });
                    _crop1.destroy();
                    $("#div_subida_archivos_usu").dialog("close");
                    $("#div_edita_imagen_usu").dialog("destroy");
                }
            }
        ]
    });
}