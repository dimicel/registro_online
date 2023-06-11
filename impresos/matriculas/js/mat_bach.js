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
var anno_ini_premat;
var anno_curso;
var consolidar_premat = false;
var curso_prematricula = "";
var nif_nie_tutor1 = "";
var nif_nie_tutor2 = "";
var i = 0;
var _paginas = new Array();
var paginas_totales;
var existe_premat = false;
var sexo="",fecha_nac="",telef_alumno="",email_alumno="",domicilio="",cp="",localidad="",provincia="";
var tutor1="",email_tutor1="",tlf_tutor1="",tutor2="",email_tutor2="",tlf_tutor2="";
var existe_foto=false, existe_dni_A=false, existe_dni_R=false, existe_seguro=false,existe_certificado=false;
var primera_vez_pag_2=true;
var primera_vez_pag_3=true;
var existe_mat=false;
var mensaje_docs = "<p>Los documentos y sus formatos son los siguientes:";
mensaje_docs += "<ul>";
mensaje_docs += "    <li>Fotografía del alummno: en formato JPEG tomada con móvil en vertical y fondo blanco, como se muestra en la imagen:<br><center><img src='../../recursos/foto_carne.jpg'  style='width:128px;'></center></li>";
mensaje_docs += "    <li>Fotografía del anverso y reverso del documento de identificación (DNI/NIE). Si sólo tiene pasaporte, el anverso será imagen JPEG de la página en la que salen los datos del alumno y su fotografía, y el reverso imagen JPEG en blanco. El documento se fotografiará con el móvil en horizontal y fondo blanco, por ejemplo, poniendo el documento sobre un folio en blanco.</li>";
mensaje_docs += "    <li>Fotografía del resguardo del pago del seguro escolar, y del anverso y reverso del documento de identificación (DNI/NIE). (Móvil en horizontal y fondo blanco, por ejemplo, sobre un folio).</li>";
mensaje_docs += "    <li>Si es alumno nuevo e inició los estudios de los que se matricula en otra comunidad autónoma, certificado de notas en formato PDF (puede escanearlo, por ejemplo, con la aplicación gratuita para móvil Microsoft Office Lens).</li>";
mensaje_docs += "</ul>";
mensaje_docs += "</p>";  

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
        if (mes_mat == 6) anno_ini_premat = (anno_ini_curso) + "-" + (anno_ini_curso + 1);
        else if (mes_mat >= 7 && mes_mat <= 9) anno_ini_premat = (anno_ini_curso - 1) + "-" + (anno_ini_curso);
        
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

        return $.post("../../php/usu_existe_premat_bach.php", { id_nie: id_nie, curso: anno_ini_premat }, () => {}, "json");
    });
    dat3 = dat2.then((res2) => {
        if (res2["error"] == "ok") {
            existe_premat = true;
            curso_prematricula = res2["curso_prematricula"];
            premat_prog_ling=res2["prog_ling"];
            (typeof(res2["sexo"]) != "undefined" || res2["sexo"]==null) ? sexo = res2["sexo"]: sexo = "";
            (typeof(res2["tutor1"]) != "undefined" || res2["tutor1"]==null) ? tutor1 = res2["tutor1"]: tutor1 = "";
            (typeof(res2["tutor2"]) != "undefined" || res2["tutor2"]==null) ? tutor2 = res2["tutor2"]: tutor2 = "";
            (typeof(res2["tlf_tutor1"]) != "undefined" || res2["tlf_tutor1"]==null) ? tlf_tutor1 = res2["tlf_tutor1"]: tlf_tutor1 = "";
            (typeof(res2["tlf_tutor2"]) != "undefined" || res2["tlf_tutor2"]==null) ? tlf_tutor2 = res2["tlf_tutor2"]: tlf_tutor2 = "";
            (typeof(res2["email_tutor1"]) != "undefined" || res2["email_tutor1"]==null) ? email_tutor1 = res2["email_tutor1"]: email_tutor1 = "";
            (typeof(res2["email_tutor2"]) != "undefined" || res2["email_tutor2"]==null) ? email_tutor2 = res2["email_tutor2"]: email_tutor2 = "";
            (typeof(res2["direccion"]) != "undefined" || res2["direccion"]==null) ? domicilio = res2["direccion"]: domicilio = "";
            (typeof(res2["cp"]) != "undefined" || res2["cp"]==null) ? cp = res2["cp"]: cp = "";
            (typeof(res2["localidad"]) != "undefined" || res2["localidad"]==null) ? localidad = res2["localidad"]: localidad = "";
            (typeof(res2["provincia"]) != "undefined" || res2["provincia"]==null) ? provincia = res2["provincia"]: provincia = "";
            (typeof(res2["email_alumno"]) != "undefined" || res2["email_alumno"]==null) ? email_alumno = res2["email_alumno"]: email_alumno = "";
            (typeof(res2["telef_alumno"]) != "undefined" || res2["telef_alumno"]==null) ? telef_alumno = res2["telef_alumno"]: telef_alumno = "";
            
        } else if (res2["error"] == "server") {
            alerta("Problemas en el servidor. Inténtelo más tarde.", "ERROR SERVIDOR", true);
        } else if (res2["error"] == "noexiste") {
            existe_premat = false;
        }
        return $.post("../../php/usu_recdatospers.php", {id_nie:id_nie }, () => {}, "json");
    });
    dat4 = dat3.then((resp) => {
        if (resp.error=="ok"){
            for (e in resp.datos){
                if(typeof(resp.datos[e])=="undefined" || resp.datos[e]==null) resp.datos[e]="";
            }
            f_nac=resp.datos.fecha_nac;
            if (f_nac!="")f_nac=f_nac.substr(8,2)+"/"+f_nac.substr(5,2)+"/"+f_nac.substr(0,4);
            fecha_nac=f_nac;
            if(sexo=="") sexo=resp.datos.sexo;
            if(telef_alumno=="") telef_alumno=resp.datos.telef_alumno;
            if(email_alumno=="")email_alumno=resp.datos.email;
            if(domicilio=="")domicilio=resp.datos.direccion;
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
    dat5=dat4.then((resp) => {
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

        $("#pagina_1").load("bach_html/pagina1.html?q="+Date.now().toString(), function() {
            creaValidatorPagina1();
            $("#pagina_1").fadeIn(500);
            $("[data-paginacion]").html("Pág. 1/6");
            paginas_totales = 6;
            if (existe_premat) {
                document.getElementById("alumno_nuevo_no").checked = true;
                document.getElementById("curso").value=curso_prematricula;
                document.getElementById("repetidor_no").checked = true;
                document.getElementById("div_consolida_premat").style.display="inline-block";
            }
        });

        return $.post("../../php/usu_existe_mat.php", { id_nie: id_nie, curso: anno_curso },()=>{},"json");
    });

    dat5.then((r)=>{
        existe_mat=(r.error=="ok")?true:false;
        if (existe_mat) {
            mensajeNuevaMat = "Ya existe una matrícula registrada.<br>Si continúa el proceso, se eliminará la que tenga ya creada y se sustituirá por ésta.";
            confirmarnuevaMat(mensajeNuevaMat, "MATRÍCULA EXISTENTE", "Crear Nueva");
        }
    });

    $('[data-toggle="tooltip"]').tooltip(); //Inicializa todos los tooltips (bootstrap)
    document.getElementById("matricula_bach").reset();

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

function seleccionCurso() {
    _curso = document.getElementById("curso").value;
    if (mes_mat != 6) $("h7").text("MATRÍCULA para el curso " + (anno_ini_curso) + "/" + (anno_ini_curso + 1) + " - " + _curso);
    else $("h7").text("MATRÍCULA para el curso " + (anno_ini_curso + 1) + "/" + (anno_ini_curso + 2) + " - " + _curso);
    if (existe_premat && curso_prematricula == _curso) {
        document.getElementById("repetidor_no").checked = true;
    } else if (existe_premat && curso_prematricula != _curso) {
        document.getElementById("repetidor_no").checked = true;
        document.getElementById("consolida_prem_no").checked = true;
    }
}

function alNuevo(){
    if (document.getElementById("alumno_nuevo_si").checked){
        document.getElementById("div_nuevo_otra_comunidad").style.display="inline-block";
        document.getElementById("div_consolida_premat").style.display="none";
        document.getElementById("consolida_prem_no").checked = true;
    } 
    else{
        $("#div_nuevo_otra_comunidad").hide();
        document.getElementById("div_consolida_premat").style.display="inline-block";
        document.getElementById("oc_no").checked=true;
    } 
}


function pasaPagina(p) {
    if (pagina == 1) creaArrayPasapagina();
    if (p == '-') pagina--;
    else if (p == '+') pagina++;
    $("[data-paginacion]").html("Pág. " + pagina + "/" + paginas_totales);
    pag = "bach_html/" + _paginas[pagina - 1][0] + ".html?q="+Date.now().toString();
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
            if (primera_vez_pag_2 && !existe_premat){
                form_pagina_2.sexo.value=sexo;
                form_pagina_2.fecha_nac.value=fecha_nac;
                form_pagina_2.telef_alumno.value=telef_alumno;
                form_pagina_2.email_alumno.value=email_alumno;
                primera_vez_pag_2=false;
            }
        }
        else if (pagina==3){
            if (primera_vez_pag_3 && !existe_premat){
                form_pagina_3.direccion.value=domicilio;
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
        else if (pag_html = "pagina_4") {
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
            if (existe_seguro){
                $("#div_resguardo_seguro_escolar").hide();
                $("#div_existe_resguardo_seguro_escolar").show();
                //document.getElementById("prev_resguardo_seguro").src="../../docs/"+id_nie+"/seguro/"+anno_curso+"/"+id_nie+".jpeg?q="+Date();
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
            document.getElementById("label_texto").innerHTML += "D./Dña. " + document.getElementById("tutor").value;
            document.getElementById("label_texto").innerHTML += ", como tutor/a legal del alumno/a " + nombre + " " + apellidos + ", mediante este formulario formaliza su matrícula en el Centro para el año escolar ";
            document.getElementById("label_texto").innerHTML += document.getElementById("anno_curso").value;
            document.getElementById("label_texto").innerHTML += ", para cursar las enseñanzas de ";
            document.getElementById("label_texto").innerHTML += _curso + ".";
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
    var f = document.getElementById("matricula_bach");
    var f1 = document.getElementById("form_pagina_1");
    var f2 = document.getElementById("form_pagina_2");
    var f3 = document.getElementById("form_pagina_3");
    var f5 = document.getElementById("form_pagina_5");
    if (f5.autor_fotos.checked) f._autor_fotos.value = "Si";
    else f._autor_fotos.value = "No";
    f.appendChild(f5.tutor);
    if (existe_premat && $("input:radio[name=consolida_prem]:checked").val()=="Si"){
        document.getElementById("consolida_premat").value="Si";
    }
    else{
        document.getElementById("consolida_premat").value="No";
    } 
    f._alumno_nuevo.value = $("input:radio[name=alumno_nuevo]:checked").val();   
    f._repetidor.value = $("input:radio[name=repetidor]:checked").val();
    f._interno.value = $("input:radio[name=interno]:checked").val();
    if(document.getElementById("div_nuevo_otra_comunidad").style.display=="none") f._nuevo_otra_comunidad.value="No";
    else f._nuevo_otra_comunidad.value=$("input:radio[name=nuevo_otra_comunidad]:checked").val();
    f.action = "php/regmat_bach.php";
    f.appendChild(f1.curso);  
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
    f.appendChild(f3.tutor1);
    f.appendChild(f3.email_tutor1);
    f.appendChild(f3.tlf_tutor1);
    f.appendChild(f3.tutor2);
    f.appendChild(f3.email_tutor2);
    f.appendChild(f3.tlf_tutor2);

    $("#cargando").show();

    var pet = $.ajax({
        url: f.action,
        type: "POST",
        data: $("#matricula_bach").serialize()
    });
    $.when(pet).done(function(resp) {
        $("#cargando").hide();
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


function subeFoto(obj) {
    if (obj.files[0].type != "image/jpeg") {
        alerta("Formato de archivo no válido", "NO VÁLIDO");
        obj.value = null;
        return;
    }

    datos = new FormData();
    datos.append("foto", obj.files[0]);
    datos.append("id_nie", id_nie);
    datos.append("anno_curso", anno_curso);
    document.getElementById("cargando").style.display = 'inline-block';
    $.ajax({
            url: "php/sube_foto.php",
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


function subeSeguro(obj) {
    if (obj.files[0].type != "image/jpeg") {
        obj.value = null;
        alerta("Formato de archivo no válido", "NO VÁLIDO");
        return;
    }
    
    datos = new FormData();
    datos.append("seguro", obj.files[0]);
    datos.append("id_nie", id_nie);
    datos.append("anno_curso", anno_curso);
    document.getElementById("cargando").style.display = 'inline-block';
    $.ajax({
            url: "php/sube_seguro.php",
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

function subeDNI(obj, parte) {
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
            url: "php/sube_dni.php",
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


function subeCertificado(obj) {
    if (obj.files[0].type != "application/pdf") {
        obj.value = null;
        alerta("Formato de archivo no válido", "NO VÁLIDO");
        return;
    }

    datos = new FormData();
    datos.append("certificado", obj.files[0]);
    datos.append("id_nie", id_nie);
    datos.append("anno_curso", anno_curso);
    document.getElementById("cargando").style.display = 'inline-block';
    $.ajax({
            url: "php/sube_certificado.php",
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
                document.getElementById("div_existe_resguardo_seguro_escolar").style.display="inherit";
                document.getElementById("div_resguardo_seguro_escolar").style.display="none";
            }
        });
}

function labelAutorizaciones(obj) {
    document.getElementById("label_texto").innerHTML = "";
    document.getElementById("label_texto").innerHTML += "D./Dña. " + obj.value;
    document.getElementById("label_texto").innerHTML += ", como tutor/a legal del alumno/a " + nombre + " " + apellidos + ", mediante este formulario formaliza su matrícula en el Centro para el año escolar ";
    document.getElementById("label_texto").innerHTML += document.getElementById("anno_curso").value;
    document.getElementById("label_texto").innerHTML += ", para cursar las enseñanzas de ";
    document.getElementById("label_texto").innerHTML += _curso + ".";

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
        width: 600,
        buttons: [
            {
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



function muestraEditor(_file,tipo){
    if (tipo=="dni_anverso" || tipo=="dni_reverso"){
        document.getElementById("texto_editor_imagen").innerHTML="Rota, haz zoom (con la rueda del ratón) y mueve la imagen para ajustarla al recuadro";
        _crop1=new Croppie(document.getElementById("div_imagen"), {
            viewport: { width: 450, height: 285 },
            boundary: { width: 675, height: 383 },
            showZoomer: false,
            enableOrientation: true
        });
        _fname_ajax="dni";
        if(tipo=="dni_anverso")_f_ajax=id_nie+"-A.jpeg";
        else _f_ajax=id_nie+"-R.jpeg";
        url="php/sube_dni.php";
        __ancho=500;
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
        url="php/sube_foto.php";
        __ancho=500;
    }
    else if(tipo=="seguro"){
        document.getElementById("texto_editor_imagen").innerHTML="Rota, haz zoom (con la rueda del ratón) en la imagen, y ajusta el recuadro al resguardo del seguro escolar.";
        _crop1=new Croppie(document.getElementById("div_imagen"), {
            viewport: { width: 300, height: 450 },
            boundary: { width: 675, height: 675 },
            showZoomer: false,
            enableOrientation: true
        });
        _fname_ajax="seguro";
        _f_ajax=id_nie+".jpeg";
        url="php/sube_seguro.php";
        __ancho=1000;
    }
    _crop1.bind({
        url: URL.createObjectURL(_file),
        orientation: 1
    });
    
    


    $("#div_edita_imagen").dialog({
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
                text: "Cancelar",
                click: function() {
                    _crop1.destroy();
                    $("#div_edita_imagen").dialog("close");
                    $("#div_edita_imagen").dialog("destroy");
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
                                    document.getElementById("div_existe_anverso_dni").style.display="inherit";
                                    document.getElementById("div_anverso_dni").style.display="none";
                                }
                                else if (tipo == "dni_reverso"){
                                    mm = "Reverso de documento subido.";
                                    document.getElementById("div_existe_reverso_dni").style.display="inherit";
                                    document.getElementById("div_reverso_dni").style.display="none";
                                }
                                else if (tipo == "foto"){
                                    mm = "Fotografía subida.";
                                    document.getElementById("div_existe_fotografia").style.display="inherit";
                                    document.getElementById("div_fotografia").style.display="none";
                                }
                                alerta(mm, "OK");
                            }
                        });
                    });
                   _crop1.destroy();
                    $("#div_edita_imagen").dialog("close");
                    $("#div_edita_imagen").dialog("destroy");
                }
            }
        ]
    });
    if (_tipoSelecc=="Documento de identificación (Pasaporte)"){
        $("#doc_ident_reverso").hide();
    }
    else if (_tipoSelecc=="Documento de identificación (DNI/NIE)"){
        $("#doc_ident_reverso").show();
    }
}
