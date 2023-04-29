var _curso;
var pagina = 1;
var paginas_totales = 6;
var id_nie = "";
var id_nif = "";
var nombre = "";
var apellidos = "";
var email = "";
var anno_ini_curso;
var primera_vez_pag_2=true;
var primera_vez_pag_3=true;


$(document).ready(function() {
    dat1 = Promise.resolve($.post("../../php/sesion.php", { tipo_usu: "usuario" }, function(resp) {
        if (resp["error"] != "ok") document.write(resp["error"]);
        else {
            id_nie = resp["id_nie"];
            document.getElementById("id_nie").value = resp["id_nie"];
            id_nif = resp["id_nif"];
            nombre = resp["nombre"];
            apellidos = resp["apellidos"];
            email = resp["email"];
            anno_ini_curso = resp["anno_ini_curso"];
            document.getElementById("rotulo_curso").innerHTML = "CURSO ACTUAL - " + anno_ini_curso + "/" + (anno_ini_curso + 1);
            document.getElementById("anno_curso").value = (anno_ini_curso) + "-" + (anno_ini_curso + 1);
            document.getElementById("anno_curso_premat").value = (anno_ini_curso + 1) + "/" + (anno_ini_curso + 2);
            document.getElementById("email").value = email;
            if (id_nie.trim() == "" || anno_ini_curso.toString().trim() == "") {
                document.write("Error datos. Por favor, inténtelo más tarde.");
            }
        }
    }, "json"));
    dat2=dat1.then(()=>{
        $.post("../../php/usu_recdatospers.php",{id_nie:id_nie},(resp)=>{
            if (resp.error=="ok"){
                for (e in resp.datos){
                    if(typeof(resp.datos[e])=="undefined" || resp.datos[e]==null) resp.datos[e]="";
                }
                f_nac=resp.datos.fecha_nac;
                f_nac=f_nac.substr(8,2)+"/"+f_nac.substr(5,2)+"/"+f_nac.substr(0,4);
                sexo=resp.datos.sexo;
                fecha_nac=f_nac;
                telef_alumno=resp.datos.telef_alumno;
                email_alumno=resp.datos.email;
                tutor1=resp.datos.tutor1;
                tlf_tutor1=resp.datos.tlf_tutor1;
                email_tutor1=resp.datos.email_tutor1;
                tutor2=resp.datos.tutor2;
                tlf_tutor2=resp.datos.tlf_tutor2;
                email_tutor2=resp.datos.email_tutor2;
            }
            else{
                fecha_nac='';
                telef_alumno='';
                email_alumno='';
                tutor1='';
                tlf_tutor1='';
                email_tutor1='';
                tutor2='';
                tlf_tutor2='';
                email_tutor2='';
            }
        },"json");
    });
    dat3 = dat2.then(() => {
        curso = anno_ini_curso + "-" + (anno_ini_curso + 1);
        $.post("../../php/usu_existe_premat.php", { id_nie: id_nie, curso: curso }, function(resp) {
            if (resp["error"] != "noexiste" && resp["error"] != "server") {
                confirmarnuevaPrem("Ya existe una prematrícula registrada.<br>Si continúa, será sustituida por la que genere nueva.",
                    "PREMATRÍCULA EXISTENTE",
                    "Continuar");
            } else if (resp["error"] == "server") alerta("Problemas en el servidor. Inténtelo más tarde.", "ERROR SERVIDOR");
        }, "json");
    });


    $('[data-toggle="tooltip"]').tooltip(); //Inicializa todos los tooltips (bootstrap)

    $("#pagina_1").load("bach_html/pagina1.html?q="+Date.now().toString(), function() {
        creaValidatorPagina1();
        $("#pagina_1").fadeIn(500);
        $("#pagina_1").removeClass("col-9");
        $("#pagina_1").addClass("col-4");
        $("#div_curso_pag1").removeClass("col-4");
        $("[itemprop='matricula']").hide();
        $("[data-paginacion]").html("Pág. 1/5");
    });
    document.getElementById("matricula_bach").reset();

    /*jQuery.validator.addMethod("miFecha", function(value, element) {
        return (/^\d{2}\/\d{2}\/\d{4}$/).test(value);
    });*/
});


function seleccionCurso() {
    _curso = document.getElementById("curso").value;
    document.getElementById("pagina_5").innerHTML = "";
    $("h7").text("PREMATRÍCULA para el curso " + (anno_ini_curso + 1) + "/" + (anno_ini_curso + 2) + " - " + _curso);
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

/* FUNCIÓN ANTIGUA
function pasaPagina(p) {
    if (p == '-') pagina--;
    else if (p == '+') pagina++;


    if (pagina > 1) {
        if (_curso == "1º Bachillerato") coletilla = "_1";
        else if (_curso == "2º Bach. HH.CC.SS.") coletilla = "_2hcs";
        else if (_curso == "2º Bach. Ciencias y Tecnología") coletilla = "_2c";
    }

    if (pagina != 5) {
        pag = "bach_html/pagina" + pagina + ".html?q="+Date.now().toString();
        valid = "creaValidatorPagina" + pagina + "()";
    } else {
        pag = "bach_html/pagina" + pagina + coletilla + ".html?q="+Date.now().toString();
        valid = "creaValidatorPagina" + pagina + coletilla + "()";
    }
    
    //Cuando pasa a pagina 6 tiene antes que validar el formulario de la 5
    if (pagina == 6) {
        validExec = "#form_pagina_5" + coletilla;
        pag = "bach_html/pagina_final.html?q="+Date.now().toString();
    } else validExec = "#form_pagina_" + (pagina - 1);


    if (pagina == 1) {
        $('#pagina_2').css('display', 'none');
        $('#pagina_1').fadeIn(500);
        $("#pagina_1").removeClass("col-9");
        $("#pagina_1").addClass("col-4");
        $("#div_curso_pag1").removeClass("col-4");
        $("[itemprop='matricula']").hide();
    } else {
        //if (true) {
        if ($(validExec).valid()) {
            if (document.getElementById("pagina_" + pagina).innerHTML.length == 0) {
                $("#pagina_" + pagina).load(pag, function() {
                    if (String(pag).indexOf("bach_html/pagina_final.html") ==-1) eval(valid);
                    pasaPagina('0');
                });
            } else {
                for (i = 1; i <= 6; i++) $("#pagina_" + i).css('display', 'none');
                $("#pagina_" + pagina).fadeIn(500);

                if(pagina==2){
                    form_pagina_2.apellidos.value = apellidos;
                    form_pagina_2.nombre.value = nombre;
                    if (primera_vez_pag_2){
                        form_pagina_2.sexo.value=sexo;
                        form_pagina_2.fecha_nac.value=fecha_nac;
                        form_pagina_2.telef_alumno.value=telef_alumno;
                        form_pagina_2.email_alumno.value=email_alumno;
                        primera_vez_pag_2=false;
                    }
                }
                else if (pagina==3){
                    if (primera_vez_pag_3){
                        form_pagina_3.tutor1.value=tutor1;
                        form_pagina_3.email_tutor1.value=email_tutor1;
                        form_pagina_3.tlf_tutor1.value=tlf_tutor1;
                        form_pagina_3.tutor2.value=tutor2;
                        form_pagina_3.email_tutor2.value=email_tutor2;
                        form_pagina_3.tlf_tutor2.value=tlf_tutor2;
                        primera_vez_pag_3=false;
                    }
                }
                else if(pagina==4){
                    cursoActual();
                }
            }
        } else pagina--;
    }
    $("[data-paginacion]").html("Pág. " + pagina + "/" + paginas_totales);
}
*/

//FUNCION MODIFICADA
function pasaPagina(p) {
    if (p == '-') pagina--;
    else if (p == '+') pagina++;

    arrayCursos={
        "1º Bachillerato":"_1",
        "2º Bach. HH.CC.SS.":"_2hcs",
        "2º Bach. Ciencias y Tecnología":"_2c"
    };

    arrayPaginas= {
        1:{
            pag:"bach_html/pagina1.html?q="+Date.now().toString(),
            valid:"creaValidatorPagina1()",
            validExec:""
        },
        2:{
            pag:"bach_html/pagina2.html?q="+Date.now().toString(),
            valid:"creaValidatorPagina2()",
            validExec:"#form_pagina_1"
        },
        3:{
            pag:"bach_html/pagina3.html?q="+Date.now().toString(),
            valid:"creaValidatorPagina3()",
            validExec:"#form_pagina_2"
        },
        4:{
            pag:"bach_html/pagina4.html?q="+Date.now().toString(),
            valid:"creaValidatorPagina4()",
            validExec:"#form_pagina_3"
        },
        5:{
            pag:"bach_html/pagina5"+arrayCursos[_curso]+".html?q="+Date.now().toString(),
            valid:"creaValidatorPagina5"+arrayCursos[_curso]+"()",
            validExec:"#form_pagina_4"
        },
        6:{
            pag:"bach_html/pagina_final.html?q="+Date.now().toString(),
            valid:"creaValidatorPagina6()",
            validExec:"#form_pagina_5"+arrayCursos[_curso]
        }
    };


    if (arrayPaginas[pagina].validExec=="") {
        $('#pagina_2').css('display', 'none');
        $('#pagina_1').fadeIn(500);
        $("#pagina_1").removeClass("col-9");
        $("#pagina_1").addClass("col-4");
        $("#div_curso_pag1").removeClass("col-4");
        $("[itemprop='matricula']").hide();
    } else {
        if ($(arrayPaginas[pagina].validExec).valid()) {
            if (document.getElementById("pagina_" + pagina).innerHTML.length == 0) {
                $("#pagina_" + pagina).load(arrayPaginas[pagina].pag, function() {
                    if (pagina!=6) eval(arrayPaginas[pagina].valid);
                    pasaPagina_actualizaHTML(pagina);
                });
            } else {
                pasaPagina_actualizaHTML(pagina)
            }
        } else pagina--;
    }
    $("[data-paginacion]").html("Pág. " + pagina + "/" + paginas_totales);
}


function pasaPagina_actualizaHTML(_pagina){
    for (i = 1; i <= 6; i++) $("#pagina_" + i).css('display', 'none');
    $("#pagina_" + _pagina).css('display', 'inherit').fadeIn(500);

    if(_pagina==2){
        form_pagina_2.apellidos.value = apellidos;
        form_pagina_2.nombre.value = nombre;
        if (primera_vez_pag_2){
            form_pagina_2.sexo.value=sexo;
            form_pagina_2.fecha_nac.value=fecha_nac;
            form_pagina_2.telef_alumno.value=telef_alumno;
            form_pagina_2.email_alumno.value=email_alumno;
            primera_vez_pag_2=false;
        }
        
    }
    else if (_pagina==3){
        if (primera_vez_pag_3){
            form_pagina_3.tutor1.value=tutor1;
            form_pagina_3.email_tutor1.value=email_tutor1;
            form_pagina_3.tlf_tutor1.value=tlf_tutor1;
            form_pagina_3.tutor2.value=tutor2;
            form_pagina_3.email_tutor2.value=email_tutor2;
            form_pagina_3.tlf_tutor2.value=tlf_tutor2;
            primera_vez_pag_3=false;
        }
    }
    else if (_pagina == 4) {
        cursoActual();
    }
}

function registraMatricula() {
    var f = document.getElementById("matricula_bach");
    var f1 = document.getElementById("form_pagina_1");
    var f2 = document.getElementById("form_pagina_2");
    var f3 = document.getElementById("form_pagina_3");
    var f4 = document.getElementById("form_pagina_4");
    
    if (_curso == "1º Bachillerato") {
        var f5 = document.getElementById("form_pagina_5_1");
        _modalidad=retornaValRadioButton(f5.b1_modalidad);
        f.primer_idioma.value = retornaValRadioButton(f5.b1_primer_idioma);
        f.religion.value=retornaValRadioButton(f5.b1_religion);
        f.b1_modalidad.value=_modalidad;
        f.action = "php/premat1bach.php";
        if (_modalidad=='Ciencias y Tecnología'){
            f.obligatoria1.value='Matemáticas I';
            var lista=$("#oblig2c :checked");
            f.obligatoria2.value=$("[for="+$(lista[0]).attr('id')+"]").html();
            f.obligatoria3.value=$("[for="+$(lista[1]).attr('id')+"]").html();
        } 
        else if (_modalidad=='Humanidades y Ciencias Sociales'){
            f.obligatoria1.value=$("#div_oblig1_h :checked").val();
            var lista=$("#oblig2h :checked");
            f.obligatoria2.value=$("[for="+$(lista[0]).attr('id')+"]").html();
            f.obligatoria3.value=$("[for="+$(lista[1]).attr('id')+"]").html();
        } /*
        else if (_modalidad=='General'){
            f.obligatoria1.value='Matemáticas Generales';
            var lista=$("#oblig2g :checked");
            f.obligatoria2.value=$("[for="+$(lista[0]).attr('id')+"]").html();
            f.obligatoria3.value=$("[for="+$(lista[1]).attr('id')+"]").html();
        }*/
        f.optativa1.value=document.getElementById("lista_optativas").options[0].value;
        f.optativa2.value=document.getElementById("lista_optativas").options[1].value;
        f.optativa3.value=document.getElementById("lista_optativas").options[2].value;
        f.optativa4.value=document.getElementById("lista_optativas").options[3].value;
        f.optativa5.value=document.getElementById("lista_optativas").options[4].value;
        f.optativa6.value=document.getElementById("lista_optativas").options[5].value;
        f.optativa7.value=document.getElementById("lista_optativas").options[6].value;
        f.optativa8.value=document.getElementById("lista_optativas").options[7].value;
        f.optativa9.value=document.getElementById("lista_optativas").options[8].value;
        f.optativa10.value=document.getElementById("lista_optativas").options[9].value;
        f.optativa11.value=document.getElementById("lista_optativas").options[10].value;
        f.optativa12.value=document.getElementById("lista_optativas").options[11].value;
        f.optativa13.value=document.getElementById("lista_optativas").options[12].value;
        f.optativa14.value=document.getElementById("lista_optativas").options[13].value;
        f.optativa15.value=document.getElementById("lista_optativas").options[14].value;
    }  
    else if (_curso == "2º Bach. Ciencias y Tecnología") {
        b2c_generaDatosSerialize();
        var f5 = document.getElementById("form_pagina_5_2c");
        f.action = "php/premat2bach_c.php";
        f.primer_idioma.value = retornaValRadioButton(f5.b2c_primer_idioma);
        f.modalidad1.value = retornaValRadioButton(document.getElementsByName("b2c_mat"));
        f.modalidad2.value=document.querySelectorAll('input[name="b2c_mod"]:checked')[0].value;
        f.modalidad3.value=document.querySelectorAll('input[name="b2c_mod"]:checked')[1].value;
        f.appendChild(f5.b2c_eitin11);
        f.appendChild(f5.b2c_eitin12);
        f.appendChild(f5.b2c_eitin13);
        f.appendChild(f5.b2c_eitin14);
        f.appendChild(f5.b2c_eitin15);
        f.appendChild(f5.b2c_eitin16);
        f.appendChild(f5.b2c_eitin17);
        f.appendChild(f5.b2c_eitin18);
        f.appendChild(f5.b2c_eitin19);
        f.appendChild(f5.b2c_eitin20);
        f.appendChild(f5.b2c_eitin21);
        f.appendChild(f5.b2c_eitin22);
        f.appendChild(f5.b2c_eitin23);
        f.appendChild(f5.b2c_eitin24);
        f.appendChild(f5.b2c_eitin25);
    } 
    else if (_curso == "2º Bach. HH.CC.SS.") {
        b2h_generaDatosSerialize();
        var f5 = document.getElementById("form_pagina_5_2hcs");
        f.action = "php/premat2bach_hcs.php";
        f.primer_idioma.value = retornaValRadioButton(f5.b2h_primer_idioma);
        f.modalidad1.value = retornaValRadioButton(document.getElementsByName("b2h_mat"));
        f.modalidad2.value=document.querySelectorAll('input[name="b2h_mod"]:checked')[0].value;
        f.modalidad3.value=document.querySelectorAll('input[name="b2h_mod"]:checked')[1].value;
        f.appendChild(f5.b2h_eitin11);
        f.appendChild(f5.b2h_eitin12);
        f.appendChild(f5.b2h_eitin13);
        f.appendChild(f5.b2h_eitin14);
        f.appendChild(f5.b2h_eitin15);
        f.appendChild(f5.b2h_eitin16);
        f.appendChild(f5.b2h_eitin17);
        f.appendChild(f5.b2h_eitin18);
        f.appendChild(f5.b2h_eitin19);
        f.appendChild(f5.b2h_eitin20);
        f.appendChild(f5.b2h_eitin21);
        f.appendChild(f5.b2h_eitin22);
        f.appendChild(f5.b2h_eitin23);
        f.appendChild(f5.b2h_eitin24);
        f.appendChild(f5.b2h_eitin25);
        f.appendChild(f5.b2h_eitin26);
    }

    f.appendChild(f1.curso);
    f.appendChild(f2.apellidos);
    f.appendChild(f2.nombre);
    f.appendChild(f2.fecha_nac);
    f.appendChild(f2.sexo);
    f.appendChild(f2.telef_alumno);
    f.appendChild(f2.email_alumno);
    f.appendChild(f3.tutor1);
    f.appendChild(f3.email_tutor1);
    f.appendChild(f3.tlf_tutor1);
    f.appendChild(f3.tutor2);
    f.appendChild(f3.email_tutor2);
    f.appendChild(f3.tlf_tutor2);
    f.appendChild(f4.sel_curso_act);
    f.appendChild(f4.sel_grupo_curso_act);

    document.getElementById("cargando").style.display = 'inherit';
    //f.submit();
    var pet = $.ajax({
        url: f.action,
        type: "POST",
        data: $("#matricula_bach").serialize()
    });
    $.when(pet).done(function(resp) {
        document.getElementById("cargando").style.display = 'none';
        if (resp == "servidor") {
            mensaje = "Ha habido un problema en el servidor. No se puede realizar el registro de la prematrícula.<br>Por favor, vuelva a intentarlo más tarde.";
            alerta(mensaje, "Error de servidor", true);
        } else if (resp.indexOf("registro_erroneo") != -1) {
            mensaje = "Ha habido un problema en el servidor. No se puede realizar el registro de la prematrícula.<br>Por favor, vuelva a intentarlo más tarde.";
            alerta(mensaje, "Error de servidor", true);
        } else if (resp == "no_file") {
            alerta("Ha habido un error y no se ha podido generar el fichero con el formulario registrado.", "Error en servidor", true);
        } else if (resp.indexOf("envio_ok") != -1) {
            alerta("Proceso finalizado correctamente.<br>Puede descargar el formulario registrado desde el panel de control de usuario.", "Registro correcto", true);
        }
    });
}

function confirmarnuevaPrem(mensaje, titulo, botonAceptar) {
    document.getElementById('confirmarnuevaPrem_div').innerHTML = mensaje;
    $("#confirmarnuevaPrem_div").dialog({
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

    $("#confirmarnuevaPrem_div").dialog('open');
}


function cursoActual() {
    if (document.getElementById("curso").value =="1º Bachillerato") {
        $("[itemprop='eso']").show();
        $("[itemprop='bach']").hide();
        $("[itemprop~='4']").show();
        $("#sel_curso_act").attr('disabled', true);
        document.getElementById("sel_curso_act").value = "4º ESO";
    } else if (document.getElementById("curso").value=="2º Bach. HH.CC.SS."){
        $("[itemprop='eso']").hide();
        $("[itemprop='bach']").show();
        $("[itemprop~='4']").hide();
        $("#sel_curso_act").attr("disabled", false);
        document.getElementById("sel_curso_act").value = "";
    } 
    else if(document.getElementById("curso").value=="2º Bach. Ciencias y Tecnología"){
        $("[itemprop='eso']").hide();
        $("[itemprop='bach']").show();
        $("[itemprop~='4']").hide();
        $curso_actual.value='';
        $("#sel_curso_act").attr("disabled", false);
        document.getElementById("sel_curso_act").value = "";
    }
}

function seleccionCursoActual(_c){
    if (_c=="1º Bach. HH.CC.SS."){
        document.getElementById("sel_grupo_curso_act").disabled=false;
        $("[itemprop~='4']").hide();
        $("[itemprop~='c']").hide();
        $("[itemprop~='hcs']").show();
    }
    else if(_c=="1º Bach. Ciencias"){
        document.getElementById("sel_grupo_curso_act").disabled=false;
        $("[itemprop~='4']").hide();
        $("[itemprop~='c']").show();
        $("[itemprop~='hcs']").hide();
    }
    else {
        document.getElementById("sel_grupo_curso_act").disabled=false;
        $("[itemprop~='4']").show();
    }
}

function subeElemento(obj) {
    var lista = obj;
    var lista_aux = new Array();
    var seleccionado = lista.selectedIndex;
    var num_elem = lista.length;

    if (seleccionado > 0) {
        for (i = 0; i < seleccionado - 1; i++) {
            lista_aux[i] = new Array(lista.options[i].value, lista.options[i].innerHTML);
        }
        lista_aux[i++] = new Array(lista.options[seleccionado].value, lista.options[seleccionado].innerHTML);
        lista_aux[i++] = new Array(lista.options[seleccionado - 1].value, lista.options[seleccionado - 1].innerHTML);
        for (i = seleccionado + 1; i < num_elem; i++) {
            lista_aux[i] = new Array(lista.options[i].value, lista.options[i].innerHTML);
        }

        lista.innerHTML = "";

        for (i = 0; i < num_elem; i++) {
            var opt = document.createElement("option");
            opt.value = lista_aux[i][0];
            opt.innerHTML = lista_aux[i][1];
            lista.appendChild(opt);
        }
        lista.selectedIndex = seleccionado - 1;
    }
}


function bajaElemento(obj) {
    var lista = obj;
    var lista_aux = new Array();
    var seleccionado = lista.selectedIndex;
    var num_elem = lista.length;

    if (seleccionado < num_elem - 1) {
        for (i = 0; i < seleccionado; i++) {
            lista_aux[i] = new Array(lista.options[i].value, lista.options[i].innerHTML);
        }
        lista_aux[i++] = new Array(lista.options[seleccionado + 1].value, lista.options[seleccionado + 1].innerHTML);
        lista_aux[i++] = new Array(lista.options[seleccionado].value, lista.options[seleccionado].innerHTML);
        for (i = seleccionado + 2; i < num_elem; i++) {
            lista_aux[i] = new Array(lista.options[i].value, lista.options[i].innerHTML);
        }

        lista.innerHTML = "";

        for (i = 0; i < num_elem; i++) {
            var opt = document.createElement("option");
            opt.value = lista_aux[i][0];
            opt.innerHTML = lista_aux[i][1];
            lista.appendChild(opt);
        }
        lista.selectedIndex = seleccionado + 1;
    }
}



