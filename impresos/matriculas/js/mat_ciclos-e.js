var _curso;
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
var i = 0;
var _paginas = new Array();
var paginas_totales;
var sexo="",fecha_nac="",telef_alumno="",email_alumno="",direccion="",cp="",localidad="",provincia="";
var tutor1="",email_tutor1="",tlf_tutor1="",tutor2="",email_tutor2="",tlf_tutor2="";
var existe_foto=false, existe_dni_A=false, existe_dni_R=false, existe_seguro=false,existe_certificado=false;

var primera_vez_pag_2=true;
var primera_vez_pag_3=true;
var mensaje_docs;


$(document).ready(function() {
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
        //document.getElementById("rotulo_curso").innerHTML = "CURSO ACTUAL - " + anno_ini_curso + "/" + (anno_ini_curso + 1);
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
        mensaje_docs = "<p>Los documentos y sus formatos son los siguientes:";
        mensaje_docs += "<ul>";
        mensaje_docs += "    <li>Fotografía del alummno: en formato JPEG tomada con móvil en vertical y fondo blanco, como se muestra en la imagen:<br><center><img src='../../recursos/foto_carne.jpg'  style='width:128px;'></center></li>";
        mensaje_docs += "    <li>Fotografía del anverso y reverso del documento de identificación (DNI/NIE). Si sólo tiene pasaporte, el anverso será imagen JPEG de la página en la que salen los datos del alumno y su fotografía, y el reverso imagen JPEG en blanco. El documento se fotografiará con el móvil en horizontal y fondo blanco, por ejemplo, poniendo el documento sobre un folio en blanco.</li>";
        mensaje_docs += "    <li>EXCEPTO nacidos antes del 31/12/" + (anno_ini_curso-27) + ", una fotografía del resguardo del pago del seguro escolar, y del anverso y reverso del documento de identificación (DNI/NIE). (Móvil en horizontal y fondo blanco, por ejemplo, sobre un folio).</li>";
        mensaje_docs += "    <li>Si es alumno nuevo e inició los estudios de los que se matricula en otra comunidad autónoma, certificado de notas en formato PDF (puede escanearlo, por ejemplo, con la aplicación gratuita para móvil Microsoft Office Lens).</li>";
        mensaje_docs += "</ul>";
        mensaje_docs += "</p>";            


        return $.post("../../php/usu_recdatospers.php", {id_nie:id_nie }, () => {}, "json");
    });
    dat3 = dat2.then((resp) => {
        if (resp.error=="ok"){
            for (e in resp.datos){
                if(typeof(resp.datos[e])==="undefined" || resp.datos[e]===null) resp.datos[e]="";
            }
            f_nac=resp.datos.fecha_nac;
            if (f_nac!="")f_nac=f_nac.substr(8,2)+"/"+f_nac.substr(5,2)+"/"+f_nac.substr(0,4);
            fecha_nac=f_nac;
            if(sexo=="") sexo=resp.datos.sexo;
            if(telef_alumno=="") telef_alumno=resp.datos.telef_alumno;
            if(email_alumno=="")email_alumno=resp.datos.email;
            if(direccion=="")direccion=resp.datos.direccion;
            if(cp=="")cp=resp.datos.cp;
            if(localidad=="")localidad=resp.datos.localidad;
            if(provincia=="")provincia=resp.datos.provincia;
            if(tutor1=="")tutor1=resp.datos.tutor1;
            if(tlf_tutor1=="")tlf_tutor1=resp.datos.tlf_tutor1;
            if(email_tutor1=="")email_tutor1=resp.datos.email_tutor1;
            if(tutor2=="")tutor2=resp.datos.tutor2;
            if(tlf_tutor2=="")tlf_tutor2=resp.datos.tlf_tutor2;
            if(email_tutor2=="")email_tutor2=resp.datos.email_tutor2;
        }

        return $.post("php/comprueba_docs_matricula.php", { id_nie: id_nie, curso:anno_curso });
    });
    dat4=dat3.then((resp)=>{
        if (resp.indexOf('F')>-1)existe_foto=true;
        else existe_foto=false;
        if (resp.indexOf('A')>-1) existe_dni_A=true;
        else existe_dni_A=false;
        if (resp.indexOf('R')>-1) existe_dni_R=true;
        else existe_dni_R=false;
        if (resp.indexOf('S')>-1) existe_seguro=true;
        else existe_seguro=false;
        if (resp.indexOf('C')>-1) existe_certificado=true;
        else existe_certificado=false;

        $("#pagina_1").load("ciclos-e_html/pagina1.html?q="+Date.now().toString(), function() {
            creaValidatorPagina1();
            $("#pagina_1").fadeIn(500);
            $("[data-paginacion]").html("Pág. 1/6");
            paginas_totales = 6;
        });

        return $.post("../../php/usu_existe_mat.php", { id_nie: id_nie, curso: anno_curso },()=>{},"json");
    });
    dat4.then((r)=>{
        existe_mat=(r.error=="ok")?true:false;
        if (existe_mat) {
            mensajeNuevaMat = "Ya existe una matrícula registrada.<br>Si continúa el proceso, se eliminará la que tenga ya creada y se sustituirá por ésta.";
            confirmarnuevaMat(mensajeNuevaMat, "MATRÍCULA EXISTENTE", "Crear Nueva");
        }
    });

    $('[data-toggle="tooltip"]').tooltip(); //Inicializa todos los tooltips (bootstrap)
    document.getElementById("mat_ciclos").reset();
});


function seleccionCurso() {
    _curso = document.getElementById("sel_ciclos").value;
    if (mes_mat != 6) $("h7").text("MATRÍCULA para el curso " + (anno_ini_curso) + "/" + (anno_ini_curso + 1) + " - " + _curso);
    else $("h7").text("MATRÍCULA para el curso " + (anno_ini_curso + 1) + "/" + (anno_ini_curso + 2) + " - " + _curso);
}



function pasaPagina(p) {
    if (pagina == 1) creaArrayPasapagina();
    if (p == '-') pagina--;
    else if (p == '+') pagina++;
    $("[data-paginacion]").html("Pág. " + pagina + "/" + paginas_totales);
    pag = "ciclos-e_html/" + _paginas[pagina - 1][0] + ".html?q="+Date.now().toString();
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
            document.getElementById("apellidos").value = apellidos;
            document.getElementById("nombre").value = nombre;
            if (id_nif != '') {
                document.getElementById("nif_nie").value = id_nif;
            }
            if (primera_vez_pag_2){
                //form_pagina_2.sexo.value=sexo;
                form_pagina_2.fecha_nac.value=fecha_nac;
                form_pagina_2.telef_alumno.value=telef_alumno;
                form_pagina_2.email_alumno.value=email_alumno;
                primera_vez_pag_2=false;
            }
        }
        else if (pagina==3){
            if (primera_vez_pag_3){
                form_pagina_3.direccion.value=direccion;
                form_pagina_3.cp.value=cp;
                form_pagina_3.localidad.value=localidad;
                form_pagina_3.provincia.value=provincia;
                form_pagina_3.tutor1.value=tutor1;
                form_pagina_3.email_tutor1.value=email_tutor1;
                form_pagina_3.tlf_tutor1.value=tlf_tutor1;
                form_pagina_3.tutor2.value=tutor2;
                form_pagina_3.email_tutor2.value=email_tutor2;
                form_pagina_3.tlf_tutor2.value=tlf_tutor2;
                primera_vez_pag_3=false;
            }
        }
        else if (pag_html == "pagina_4") {
            $("#form_pagina_4").validate().resetForm();
            if (existe_foto){
                $("#div_fotografia").hide();
                $("#div_existe_fotografia").show();
                //document.getElementById("prev_foto").src="../../docs/fotos/"+id_nie+".jpeg?q="+Date();
            }
            if (existe_dni_A){
                $("#div_anverso_dni").hide();
                $("#div_existe_anverso_dni").show();
                //document.getElementById("prev_anverso_dni").src="../../docs/"+id_nie+"/dni/"+id_nie+"-A.jpeg?q="+Date();
            }
            if (existe_dni_R){
                $("#div_reverso_dni").hide();
                $("#div_existe_reverso_dni").show();
                //document.getElementById("prev_reverso_dni").src="../../docs/"+id_nie+"/dni/"+id_nie+"-R.jpeg?q="+Date();
            }
            if (mayor28()){
                $("#div_resguardo_seguro_escolar").hide();
                $("#div_existe_resguardo_seguro_escolar").hide();
            }
            else {
                if (existe_seguro){
                    $("#div_resguardo_seguro_escolar").hide();
                    $("#div_existe_resguardo_seguro_escolar").show();
                    //document.getElementById("prev_resguardo_seguro").src="../../docs/"+id_nie+"/seguro/"+anno_curso+"/"+id_nie+".jpeg?q="+Date();    
                }
                else{
                    $("#div_resguardo_seguro_escolar").show();
                    $("#div_existe_resguardo_seguro_escolar").hide();
                }
            }
            if(document.getElementById("oc_si").checked){
                if (existe_certificado){
                    $("#div_certificado").hide();
                    $("#div_existe_certificado").show();
                    document.getElementById("prev_certificado").href="../../docs/"+id_nie+"/certificado_notas/"+anno_curso+"/"+id_nie+".pdf?q="+Date();
                }
                else{
                    $("#div_certificado").show();
                    $("#div_existe_certificado").hide();
                }
            }
            else{
                $("#div_certificado").hide();
                $("#div_existe_certificado").hide();
            }
        }
        else if (pag_html == "pagina_5") {
            if (document.getElementById("mayor").checked) seleccMayorMenor("si");
            else if (document.getElementById("menor").checked) seleccMayorMenor("no");
            else seleccMayorMenor("");
        }
    }
}

function cargaImagen(dest){
    if (dest=="prev_foto"){
        document.getElementById("prev_foto").src="../../docs/fotos/"+id_nie+".jpeg?q="+Date();
    }
    else if(dest=="prev_anverso_dni"){
        document.getElementById("prev_anverso_dni").src="../../docs/"+id_nie+"/dni/"+id_nie+"-A.jpeg?q="+Date();
    }
    else if(dest=="prev_reverso_dni"){
        document.getElementById("prev_reverso_dni").src="../../docs/"+id_nie+"/dni/"+id_nie+"-R.jpeg?q="+Date();
    }
    else if(dest=="prev_resguardo_seguro"){
        document.getElementById("prev_resguardo_seguro").src="../../docs/"+id_nie+"/seguro/"+anno_curso+"/"+id_nie+".jpeg?q="+Date();
    }
}


function creaArrayPasapagina() {
    _paginas = [];
    _paginas.push(new Array("pagina1", "pagina_1", "creaValidatorPagina1()", ""));
    _paginas.push(new Array("pagina2", "pagina_2", "creaValidatorPagina2()", "form_pagina_1"));
    _paginas.push(new Array("pagina3", "pagina_3", "creaValidatorPagina3()", "form_pagina_2"));
    _paginas.push(new Array("pagina4", "pagina_4", "creaValidatorPagina4()", "form_pagina_3"));
    _paginas.push(new Array("pagina5", "pagina_5", "creaValidatorPagina5()", "form_pagina_4"));
    _paginas.push(new Array("pagina_final", "pagina_6", "", "form_pagina_5"));
}

function registraMatricula() {
    var f = document.getElementById("mat_ciclos");
    var f1 = document.getElementById("form_pagina_1");
    var f2 = document.getElementById("form_pagina_2");
    var f3 = document.getElementById("form_pagina_3");
    var f5 = document.getElementById("form_pagina_5");

    f._autor_fotos.value=(f5.autor_fotos.checked)?"Si":"No";
    f._nuevo_otra_comunidad.value=(f1.oc_si.checked)?"Si":"No";
    f.action = "php/regmat_ciclos.php";
    f.appendChild(f1.sel_grado);
    f.appendChild(f1.sel_ciclos);
    f.appendChild(f1.sel_curso);
    f.appendChild(f1.sel_turno);
    f.appendChild(f2.apellidos);
    f.appendChild(f2.nombre);
    f.appendChild(f2.fecha_nac);
    f.appendChild(f2.telef_alumno);
    f.appendChild(f2.email_alumno);
    f.appendChild(f2.nif_nie);
    f.appendChild(f3.direccion);
    f.appendChild(f3.cp);
    f.appendChild(f3.localidad);
    f.appendChild(f3.provincia);
    f.appendChild(f5.tutor);
    f.appendChild(f5.autor_fotos);

    document.getElementById("cargando").style.display = 'inherit';

    var pet = $.ajax({
        url: f.action,
        type: "POST",
        data: $("#mat_ciclos").serialize()
    });
    $.when(pet).done(function(resp) {
        document.getElementById("cargando").style.display = 'none';
        if (resp == "servidor") {
            mensaje = "Ha habido un problema en el servidor. No se puede realizar el registro de la prematrícula.<br>Por favor, vuelva a intentarlo más tarde.";
            alerta(mensaje, "Error de servidor", true);
        } else if (resp.indexOf("registro_erroneo") != -1) {
            mensaje = "Ha habido un problema en el servidor. No se puede realizar el registro de la prematrícula.<br>Por favor, vuelva a intentarlo más tarde.";
            alerta(mensaje, "Error de servidor", true);
        } else if (resp.indexOf("envio_ok") != -1) {
            alerta("Proceso finalizado correctamente.<br>Puede descargar el impreso registrado desde el panel de control del usuario.", "Registro correcto", true);
        } else window.history.back();
    });
}


function seleccMayorMenor(resp) {
    if (resp != '') {
        document.getElementById("div_sin_selec_mayor_menor").style.display = "none";
        document.getElementById("div_texto").style.display = "inline-block";
        document.getElementById("mayor_edad").value = resp;
    }
    if (resp == "si") {
        document.getElementById("mayor_edad").value = "Si";
        document.getElementById("div_selec_mayor").style.display = "inherit";
        document.getElementById("div_selec_menor").style.display = "none";
        //document.getElementById("div_selec_menor_label").style.display = "none";
        _tt = document.getElementById("nombre").value + " " + document.getElementById("apellidos").value;
        if (document.getElementById("nif_nie").value.trim() != "") {
            _tt += ", con NIF/NIE/Pasaporte " + document.getElementById("nif_nie").value;
        }
        _tt += ", como alumno/a del Centro:";
        document.getElementById("texto_autor_mayor").innerHTML = _tt;

    } else if (resp == "no") {
        document.getElementById("mayor_edad").value = "No";
        document.getElementById("div_selec_mayor").style.display = "none";
        document.getElementById("div_selec_menor").style.display = "inherit";
        //document.getElementById("div_selec_menor_label").style.display = "inherit";
    }
}


function labelAutorizacionesMenor(txt) {
    document.getElementById("texto_autor_menor").innerHTML = "D.Dña. " + document.getElementById("tutor").value;
    document.getElementById("texto_autor_menor").innerHTML += ", como tutor/a legal del alumno/a ";
    document.getElementById("texto_autor_menor").innerHTML += document.getElementById("nombre").value + " " + document.getElementById("apellidos").value + ":";
}


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
                    window.history.back();
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



function confirmarnuevaMat(mensaje, titulo, botonAceptar) {
    document.getElementById('confirmarnuevaMat_div').innerHTML = mensaje;
    $("#confirmarnuevaMat_div").dialog({
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
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Cancelar",
                click: function() {
                    $(this).dialog("close");
                    window.history.back();
                }
            }
        ]
    });

    $("#confirmarnuevaMat_div").dialog('open');
}



function CreaSelTurno() {
    curs=document.getElementById("sel_curso").value;
    c = document.getElementById("sel_ciclos").value;
    document.getElementById("sel_turno").innerHTML = "";
    if(curs=="Elear" || curs=="Libre"){
        document.getElementById("sel_turno").innerHTML = "<option value='-'>-</option>";
        return;
    }
    else if (curs == "3º") {
        document.getElementById("sel_turno").innerHTML = "<option value='Nocturno'>Nocturno</option>";
        return;
    }
    if (c == "Cocina y Gastronomía" || c == "Instalaciones Eléctricas y Automáticas" ||
        c == "Servicios en Restauración") {
        tu = "<option value=''>Seleccione uno...</option>";
        tu += "<option value='Diurno'>Diurno</option>";
        tu += "<option value='Vespertino'>Vespertino</option>";
        document.getElementById("sel_turno").innerHTML = tu;
    } else if (c == "Panadería, Repostería y Confitería" || c == "Gestión de Alojamientos Turísticos" ||
        c == "Mantenimiento de Instalaciones Térmicas y de Fluidos") {
        document.getElementById("sel_turno").innerHTML = "<option value='Vespertino'>Vespertino</option>";
    } else if (c == "Administración y Finanzas") {
        tu = "<option value=''>Seleccione uno...</option>";
        tu += "<option value='Diurno'>Diurno</option>";
        tu += "<option value='Nocturno'>Nocturno</option>";
        document.getElementById("sel_turno").innerHTML = tu;
    } else {
        document.getElementById("sel_turno").innerHTML = "<option value='Diurno'>Diurno</option>";
    }
   
}


function creaSelCiclos(g) {
    if (g != "") {
        document.getElementById("sel_curso").innerHTML = "<option value=''>Seleccione CICLO</option>";
        document.getElementById("sel_turno").innerHTML = "<option value=''>Seleccione CICLO</option>";
        if (g == "MEDIO") {
            sel = "<option value=''>Seleccione uno...</option>";
            sel += "<option value='Cocina y Gastronomía'>Cocina y Gastronomía</option>";
            sel += "<option value='Gestión Administrativa'>Gestión Administrativa</option>";
            sel += "<option value='Instalaciones de Producción de Calor'>Instalaciones de Producción de Calor</option>";
            sel += "<option value='Instalaciones Eléctricas y Automáticas'>Instalaciones Eléctricas y Automáticas</option>";
            sel += "<option value='Instalaciones Frigoríficas y de Climatización'>Instalaciones Frigoríficas y de Climatización</option>";
            sel += "<option value='Panadería, Repostería y Confitería'>Panadería, Repostería y Confitería</option>";
            sel += "<option value='Servicios en Restauración'>Servicios en Restauración</option>";
        } else if (g == "SUPERIOR") {
            sel = "<option value=''>Seleccione uno...</option>";
            sel += "<option value='Administración y Finanzas'>Administración y Finanzas</option>";
            sel += "<option value='Agencias de Viajes y Gestión de Eventos'>Agencias de Viajes y Gestión de Eventos</option>";
            sel += "<option value='Asistencia a la Dirección'>Asistencia a la Dirección</option>";
            sel += "<option value='Automatización y Robótica Industrial'>Automatización y Robótica Industrial</option>";
            sel += "<option value='Dirección de Cocina'>Dirección de Cocina</option>";
            sel += "<option value='Dirección de Servicios de Restauración'>Dirección de Servicios de Restauración</option>";
            sel += "<option value='Gestión de Alojamientos Turísticos'>Gestión de Alojamientos Turísticos</option>";
            sel += "<option value='Guía, Información y Asistencias Turísticas'>Guía, Información y Asistencias Turísticas</option>";
            sel += "<option value='Mantenimiento de Instalaciones Térmicas y de Fluidos'>Mantenimiento de Instalaciones Térmicas y de Fluidos</option>";
            sel += "<option value='Sistemas Electrotécnicos y Automatizados'>Sistemas Electrotécnicos y Automatizados</option>";
        }
        document.getElementById("sel_ciclos").innerHTML=sel;
        seleccionCurso();
    }
}


function CreaSelCurso(c) {
    if (c != "") {
        document.getElementById("sel_curso").innerHTML = "";
        cu = "<option value=''>Seleccione uno...</option>";
        cu += "<option value='Modular'>Modular</option>";
        cu += "<option value='1º'>1º</option>";
        cu += "<option value='2º'>2º</option>";
        if (c == "Administración y Finanzas") cu += "<option value='3º'>3º</option>";
        document.getElementById("sel_curso").innerHTML = cu;
        seleccionCurso();
    }
}

function CreaSelTurno(t) {
    if (t != "") {
        c = document.getElementById("sel_ciclos").value;
        document.getElementById("sel_turno").innerHTML = "";
        if (c == "Cocina y Gastronomía"  || c == "Servicios en Restauración") {
            tu = "<option value=''>Seleccione uno...</option>";
            tu += "<option value='Diurno'>Diurno</option>";
            tu += "<option value='Vespertino'>Vespertino</option>";
            document.getElementById("sel_turno").innerHTML = tu;
        }
        else if(c == "Instalaciones Eléctricas y Automáticas"){
            tu = "<option value=''>Seleccione uno...</option>";
            tu += "<option value='Diurno'>Diurno</option>";
            tu += "<option value='Vespertino'>Vespertino</option>";
            if(t=="Modular") tu += "<option value='E-learning'>E-learning</option>";
            document.getElementById("sel_turno").innerHTML = tu;
        } 
        else if (c == "Panadería, Repostería y Confitería" || c == "Gestión de Alojamientos Turísticos" ||
            c == "Mantenimiento de Instalaciones Térmicas y de Fluidos" || c=="Dirección de Servicios de Restauración") {
            document.getElementById("sel_turno").innerHTML = "<option value='Vespertino'>Vespertino</option>";
        } else if (c == "Administración y Finanzas") {
            tu = "<option value=''>Seleccione uno...</option>";
            tu += "<option value='Diurno'>Diurno</option>";
            tu += "<option value='Nocturno'>Nocturno</option>";
            if(t=="Modular")tu += "<option value='E-learning'>E-learning</option>";
            document.getElementById("sel_turno").innerHTML = tu;
        }
        else if(c=="Guía, Información y Asistencias Turísticas"){
            tu = "<option value=''>Seleccione uno...</option>";
            tu += "<option value='Diurno'>Diurno</option>";
            if(t=="Modular") tu += "<option value='E-learning'>E-learning</option>";
            document.getElementById("sel_turno").innerHTML = tu;
        }
        else {
            document.getElementById("sel_turno").innerHTML = "<option value='Diurno'>Diurno</option>";
        }
        if (t == "3º") {
            document.getElementById("sel_turno").innerHTML = "<option value='Nocturno'>Nocturno</option>";
        }
        seleccionCurso();
    }
}

function mayor28() {
    //dia_nac = document.getElementById("fecha_nac").value.substr(0, 2);
    //mes_nac = document.getElementById("fecha_nac").value.substr(3, 2);
    anno_nac = parseInt(document.getElementById("fecha_nac").value.substr(6, 4));
    if (mes_mat != 6) anno_limit = anno_ini_curso;
    else anno_limit = anno_ini_curso + 1;
    edad = (anno_limit + 1900) - anno_nac;
    if (edad > 1900) {
        edad -= 1900;
    }
    if (edad < 28) return false;
    else return true;
}


$("#div_ayuda_docs").dialog({
    autoOpen: false,
    dialogClass: "alert no-close",
    modal: true,
    hide: { effect: "fade", duration: 0 },
    resizable: false,
    show: { effect: "fade", duration: 0 },
    title: "AYUDA FORMATOS DOCUMENTOS",
    maxHeight: 850,
    width: 850,
    buttons: [{
        class: "btn btn-success textoboton",
        text: "Cerrar",
        click: function() {
            $("#div_ayuda_docs").dialog("close");
        }
    }]
});


function muestraAyudaDocs(){
    document.getElementById("div_ayuda_docs").innerHTML = mensaje_docs;
    $('#div_ayuda_docs').dialog('open');
}

