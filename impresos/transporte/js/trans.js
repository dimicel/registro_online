var pagina = 1;
var id_nie = "";
var id_nif = "";
var nombre = "";
var apellidos = "";
var email = "";
var mes_mat;
var dia_mat;
var anno_ini_curso;
var anno_curso;
var nif_nie_tutor1 = "";
var nif_nie_tutor2 = "";
var i = 0;
var _paginas = new Array();
var paginas_totales=7;
var error_server = false;
var telef_alumno="",email_alumno="",direccion="",cp="",localidad="",provincia="";
var tutor1="",email_tutor1="",tlf_tutor1="",tutor2="",email_tutor2="",tlf_tutor2="",nif_nie_tutor1="";
var primera_vez_pag_2=true;
var primera_vez_pag_3=true;
var iniciada_desde_matricula=false;


$(document).ready(function() {
    iniciada_desde_matricula=getParameterByName("origen");
    if (iniciada_desde_matricula=="mat"){
        mensaje = "Proceso finalizado correctamente.<br>Ahora continuará con la solicitud de transporte escolar.<br>";
        mensaje+="Si ya tenía registrada una solicitud de transporte escolar, será sustituida por la que va a cumplimentar ahora.";
        alerta(mensaje, "Registro correcto",500);
    }
    else{
        mensaje="Si ya tenía registrada una solicitud de transporte escolar, será sustituida por la que va a cumplimentar ahora.";
        alerta(mensaje, "AVISO",500);
    }
    dat1 = Promise.resolve($.post("../../php/sesion.php", { tipo_usu: "usuario" }, () => {}, "json"));
    dat2 = dat1.then((res1) => {
        id_nie = res1["id_nie"];
        id_nif = res1["id_nif"];
        nombre = res1["nombre"];
        apellidos = res1["apellidos"];
        email = res1["email"];
        anno_ini_curso = res1["anno_ini_curso"];
        mes_mat = res1["mes"];
        dia_mat = res1["dia"];
        document.getElementById("id_nie").value = id_nie;
        if (mes_mat != 6) {
            anno_curso = (anno_ini_curso) + "-" + (anno_ini_curso + 1);
            document.getElementById("anno_curso").value = (anno_ini_curso) + "-" + (anno_ini_curso + 1);
        } else {
            anno_curso = (anno_ini_curso + 1) + "-" + (anno_ini_curso + 2);
            document.getElementById("anno_curso").value = (anno_ini_curso + 1) + "-" + (anno_ini_curso + 2);
        }

        document.getElementById("email").value = email;
        if (id_nie.trim() == "" || anno_ini_curso.toString().trim() == "") {
            document.write("Error datos. Por favor, inténtelo más tarde.");
        }

        return $.post("../../php/usu_recdatospers.php",{id_nie:id_nie}, () => {}, "json");
    });
    dat3 = dat2.then((resp)=>{
        if (resp.error=="ok"){
            for (e in resp.datos){
                if(typeof(resp.datos[e])=="undefined" || resp.datos[e]==null) resp.datos[e]="";
            }
            f_nac=resp.datos.fecha_nac;
            if (f_nac!="")f_nac=f_nac.substr(8,2)+"/"+f_nac.substr(5,2)+"/"+f_nac.substr(0,4);
            fecha_nac=f_nac;
            if(telef_alumno=="") telef_alumno=resp.datos.telef_alumno;
            if(email_alumno=="")email_alumno=resp.datos.email;
            if(direccion=="")direccion=resp.datos.direccion;
            if(cp=="")cp=resp.datos.cp;
            if(localidad=="")localidad=resp.datos.localidad;
            if(provincia=="")provincia=resp.datos.provincia;
            if(tutor1=="")tutor1=resp.datos.tutor1;
            if(tlf_tutor1=="")tlf_tutor1=resp.datos.tlf_tutor1;
            if(email_tutor1=="")email_tutor1=resp.datos.email_tutor1;
            if(nif_nie_tutor1=="")nif_nie_tutor1=resp.datos.nif_nie_tutor1;
        }
    });
    dat3.then(() => {
        $("#pagina_1").load("trans_html/pagina1.html?q="+Date.now().toString(), function() {
            creaValidatorPagina1();
            $("#pagina_1").show();
            $("[data-paginacion]").html("Pág. 1/7");
            if (mes_mat != 6) _texto_curso=(anno_ini_curso) + "/" + (anno_ini_curso + 1);
            else _texto_curso=(anno_ini_curso + 1) + "/" + (anno_ini_curso + 2);
            if (mes_mat != 6) $("h7").text("SOLICITUD TRANSPORTE para el curso " + _texto_curso);
            else $("h7").text("SOLICITUD TRANSPORTE para el curso " + _texto_curso);
        });
    });

    $('[data-toggle="tooltip"]').tooltip(); //Inicializa todos los tooltips (bootstrap)
    document.getElementById("transporte").reset();

});

function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
    results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}


function pasaPagina(p) {
    if (pagina == 1) creaArrayPasapagina();
    if (p == '-') pagina--;
    else if (p == '+') pagina++;
    $("[data-paginacion]").html("Pág. " + pagina + "/" + paginas_totales);
    pag = "trans_html/" + _paginas[pagina - 1][0] + ".html?q="+Date.now().toString();
    pag_html = _paginas[pagina - 1][1];
    valid = _paginas[pagina - 1][2];
    validExec = "#" + _paginas[pagina - 1][3];

    if (p == "+") {
        if ($(validExec).valid()) {
            if (document.getElementById(pag_html).innerHTML.length == 0) {
                $("#" + pag_html).load(pag, function() {
                    if (valid != "") eval(valid);
                    pasaPagina('0');
                });
            } else pasaPagina('0');
        } else{
            pagina--;
            $("[data-paginacion]").html("Pág. " + pagina + "/" + paginas_totales);
        } 
    } else {
        for (i = 0; i < _paginas.length; i++) $("#" + _paginas[i][1]).css('display', 'none');
        $("#" + pag_html).css('display', 'inherit').fadeIn(500);

        if (pag_html == "pagina_2") {
            if (primera_vez_pag_2){
                document.getElementById("apellidos").value = apellidos;
                document.getElementById("nombre").value = nombre;
                document.getElementById("nif_nie").value = id_nif;
                form_pagina_2.telef_alumno.value=telef_alumno;
                form_pagina_2.email_alumno.value=email_alumno;
                primera_vez_pag_2=false;
            }
        }
        else if (pagina==3){
            if (primera_vez_pag_3){
                form_pagina_3.te_nombre_apellidos.value=tutor1;
                form_pagina_3.te_nif_nie.value=nif_nie_tutor1;
                form_pagina_3.te_direccion.value=direccion;
                form_pagina_3.te_cp.value=cp;
                form_pagina_3.te_localidad.value=localidad;
                form_pagina_3.te_provincia.value=provincia;
                form_pagina_3.te_email.value=email_tutor1;
                form_pagina_3.te_tlf_movil.value=tlf_tutor1;
                primera_vez_pag_3=false;
            }
        }
    }
}

function registraSolicitud() {
    var f = document.getElementById("transporte");
    var f1 = document.getElementById("form_pagina_1");
    var f2 = document.getElementById("form_pagina_2");
    var f3 = document.getElementById("form_pagina_3");
    var f4 = document.getElementById("form_pagina_4");
    var f5 = document.getElementById("form_pagina_5");
    f.action = "php/generapdf.php";
    f.appendChild(f2.nombre);
    f.appendChild(f2.apellidos);
    f.appendChild(f2.nif_nie);
    f.appendChild(f2.telef_alumno);
    f.appendChild(f2.email_alumno);
    f.appendChild(f3.te_nombre_apellidos);
    f.appendChild(f3.te_nif_nie);
    f.appendChild(f3.te_direccion);
    f.appendChild(f3.te_localidad);
    f.appendChild(f3.te_provincia);
    f.appendChild(f3.te_cp);
    f.appendChild(f3.te_tlf_movil);
    f.appendChild(f3.te_tlf_fijo);
    f.appendChild(f3.te_email);
    
    f._t_apartado.value = retornaValRadioButton(f4.apartado);
    f._t_modalidad.value = retornaValRadioButton(f4.modalidad);
    f.appendChild(f4.sillaruedas);
    f._t_aut_acred_iden.value = retornaValRadioButton(f5.acred_iden);
    f._t_aut_acred_domic.value = retornaValRadioButton(f5.acred_domic);

    mostrarPantallaEspera();
    $.post("php/generapdf.php",$("#transporte").serialize(),(r2)=>{
        ocultarPantallaEspera();
        if (r2.indexOf("envio_ok") != -1 || r2=="envio_ok") {
            mensaje = "Proceso finalizado correctamente.<br>";
            mensaje += "Puede descargar el impreso de solicitud de transporte escolar registrado desde el panel de control del usuario.";
            alerta(mensaje, "Registro correcto",true);
        } else if (r2 == "servidor") {
            mensaje = "Ha habido un problema en el servidor. No se puede realizar el registro de la solicitud.<br>Por favor, vuelva a intentarlo más tarde.";
            alerta(mensaje, "Error de servidor");
        } else if (r2.indexOf("registro_erroneo") != -1) {
            mensaje = "Ha habido un problema en el servidor. No se puede realizar el registro de la solicitud.<br>Por favor, vuelva a intentarlo más tarde.";
            alerta(mensaje, "Error de servidor");
        }
        if (iniciada_desde_matricula=="mat"){
            window.history.back();
            window.history.back();
        } 
        //else window.history.back();
    });
}


function creaArrayPasapagina() {
    _paginas = [];
    _paginas.push(new Array("pagina1", "pagina_1", "creaValidatorPagina1()", ""));
    _paginas.push(new Array("pagina2", "pagina_2", "creaValidatorPagina2()", "form_pagina_1"));
    _paginas.push(new Array("pagina3", "pagina_3", "creaValidatorPagina3()", "form_pagina_2"));
    _paginas.push(new Array("pagina4", "pagina_4", "creaValidatorPagina4()", "form_pagina_3"));
    _paginas.push(new Array("pagina5", "pagina_5", "creaValidatorPagina5()", "form_pagina_4"));
    _paginas.push(new Array("pagina6", "pagina_6", "creaValidatorPagina6()", "form_pagina_5"));
    _paginas.push(new Array("pagina_final", "pagina_7", "", "form_pagina_6"));
}


function calculoDistancia() {
    var localidad = document.getElementById("te_localidad").value.trim().replace("    ", "-");
    //var localidad = miTrim(document.getElementById("te_localidad").value).replace("    ", "-");
    if (localidad.length == 0) {
        alerta("No ha introducido la localidad de residencia del alumno.", "DATOS INSUFICIENTES");
        return;
    }
    alerta("Tome nota de la distancia por carretera que le sale en la página que se ha abierto, e introduzca ese dato en la casilla.", "INFORMACIÓN");
    localidad = localidad.replace("   ", "-");
    localidad = localidad.replace("  ", "-");
    localidad = localidad.replace(" ", "-");
    destino = "http://www.distanciasentreciudades.com/distancia-toledo-a-" + localidad;
    open(destino, "_blank");
}


function selApartadoTransporte() {
    var selecc = retornaValRadioButton(document.getElementsByName("apartado"));
    if (selecc == "Art. 3 pto. 2 aptdo. e)") {
        document.getElementById("modalidad_semana").checked = true;
        document.getElementById("modalidad_semana").disabled = true;
        document.getElementById("modalidad_diario").disabled = true;
        //document.getElementsByName("modalidad")[0].disabled = true;
    } else {
        //document.getElementsByName("modalidad")[0].disabled = false;
        document.getElementById("modalidad_semana").disabled = false;
        document.getElementById("modalidad_diario").disabled = false;
    }
}


function confirmarnuevaMat(mensaje, titulo, botonAceptar) {
    dialogo_id=generaDivDialog();
    document.getElementById(dialogo_id).innerHTML = mensaje;
    $("#"+dialogo_id).dialog({
        title: titulo,
        autoOpen: true,
        dialogClass: "alert no-close",
        modal: true,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        width: 600,
        buttons: [{
                class: "btn btn-success textoboton",
                text: botonAceptar,
                click: function() {
                    $(this).dialog("destroy").remove();
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Cancelar",
                click: function() {
                    $(this).dialog("destroy").remove();
                    window.history.back();
                }
            }
        ]
    });
}