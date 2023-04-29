var _curso;
var pagina = 1;
var id_nie = "";
var nombre = "";
var apellidos = "";
var email = "";
var anno_ini_curso;
var pagina = 1;
var paginas_totales = 6;
var sexo,fecha_nac,telef_alumno,email_alumno;
var tutor1,email_tutor1,tlf_tutor1,tutor2,email_tutor3,tlf_tutor2;
var primera_vez_pag_2=true;
var primera_vez_pag_3=true;



$(document).ready(function() {

    document.body.style.overflowY = "scroll";

    dat1 = Promise.resolve($.post("../../php/sesion.php", { tipo_usu: "usuario" }, function(resp) {
        if (resp["error"] != "ok") document.write(resp["error"]);
        else {
            id_nie = resp["id_nie"];
            document.getElementById("id_nie").value = resp["id_nie"];
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
                sexo='';
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

    $("#pagina_1").load("eso_html/pagina1.html?q="+Date.now().toString(), function() {
        creaValidatorPagina1();
        $("#pagina_1").css('display', 'inherit').fadeIn(500);
        $("[data-paginacion]").html("Pág. 1/6");
    });


    document.getElementById("matricula_eso").reset();

    /*jQuery.validator.addMethod("miFecha", function(value, element) {
        return (/^\d{2}\/\d{2}\/\d{4}$/).test(value);
    });*/
});


function seleccionCurso() {
    _curso = document.getElementById("curso").value;
    document.getElementById("pagina_5").innerHTML = "";
    $("h7").text("PREMATRÍCULA para el curso " + (anno_ini_curso + 1) + "/" + (anno_ini_curso + 2) + " - " + _curso);
    if (_curso == "2º ESO PMAR" || _curso == "3º ESO DIV") {
        document.getElementById("prog_ling").checked = false;
    }
}

function generaImpreso() {
    if (_curso == "1º ESO") eso1_generaImpreso();
    else if (_curso == "2º ESO") eso2_generaImpreso();
    //else if (_curso == "2º ESO PMAR") eso2_pmar_generaImpreso();
    else if (_curso == "3º ESO") eso3_generaImpreso();
    else if (_curso == "3º ESO DIV") eso3_pmar_generaImpreso();
    else if (_curso == "4º ESO") eso4_generaImpreso();
}

function progLing(obj) {
    if (pagina == 1) {
        if ( _curso == "3º ESO DIV" || _curso == "4º ESO DIV") {
            obj.checked = false;
        }
        return false;
    }
    if (obj.checked) {
        if (_curso == "1º ESO") {
            document.getElementById("eso1_ingles").checked = true;
            document.getElementById("eso1_frances").disabled = true;;
            eso1_seleccionIdioma("ingles");
        } else if (_curso == "2º ESO") {
            document.getElementById("eso2_ingles").checked = true;
            document.getElementById("eso2_frances").disabled = true;;
            eso2_seleccionIdioma("ingles");
        } else if (_curso == "3º ESO") {
            document.getElementById("eso3_ingles").checked = true;
            document.getElementById("eso3_frances").disabled = true;;
            eso3_seleccionIdioma("ingles");
        } else if (_curso == "4º ESO") {
            document.getElementById("eso4_ingles").checked = true;
            document.getElementById("eso4_frances").disabled = true;;
            eso4_seleccionIdioma("ingles");
        }
    }
    if (!obj.checked) {
        if (_curso == "1º ESO") document.getElementById("eso1_frances").disabled = false;
        else if (_curso == "2º ESO") document.getElementById("eso2_frances").disabled = false;
        else if (_curso == "3º ESO") document.getElementById("eso3_frances").disabled = false;
        else if (_curso == "4º ESO") document.getElementById("eso4_frances").disabled = false;
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


function pasaPagina(p) {
    if (p == '-') pagina--;
    else if (p == '+') pagina++;

    arrayCursos={
        "1º ESO":"_1eso",
        "2º ESO":"_2eso",
        "3º ESO":"_3eso",
        "4º ESO":"_4eso",
        "3º ESO DIV":"_3esodiv",
        "4º ESO DIV":"_4esodiv"
    };

    arrayPaginas= {
        1:{
            pag:"eso_html/pagina1.html?q="+Date.now().toString(),
            valid:"creaValidatorPagina1()",
            validExec:""
        },
        2:{
            pag:"eso_html/pagina2.html?q="+Date.now().toString(),
            valid:"creaValidatorPagina2()",
            validExec:"#form_pagina_1"
        },
        3:{
            pag:"eso_html/pagina3.html?q="+Date.now().toString(),
            valid:"creaValidatorPagina3()",
            validExec:"#form_pagina_2"
        },
        4:{
            pag:"eso_html/pagina4.html?q="+Date.now().toString(),
            valid:"creaValidatorPagina4()",
            validExec:"#form_pagina_3"
        },
        5:{
            pag:"eso_html/pagina5"+arrayCursos[_curso]+".html?q="+Date.now().toString(),
            valid:"creaValidatorPagina5"+arrayCursos[_curso]+"()",
            validExec:"#form_pagina_4"
        },
        6:{
            pag:"eso_html/pagina_final.html?q="+Date.now().toString(),
            valid:"creaValidatorPagina6()",
            validExec:"#form_pagina_5"+arrayCursos[_curso]
        }
    };
    
    
    if(arrayPaginas[pagina].validExec==""){
        $('#pagina_2').css('display', 'none');
        $('#pagina_1').fadeIn(500);
    }
    else {
        if ($(arrayPaginas[pagina].validExec).valid()) {
            if (document.getElementById("pagina_"+pagina).innerHTML.length == 0) {
                $("#pagina_" + pagina).load(arrayPaginas[pagina].pag, function() {
                    if (pagina!=6 ) eval(arrayPaginas[pagina].valid);
                    pasaPagina_actualizaHTML(pagina);
                });
            } else {
                pasaPagina_actualizaHTML(pagina);
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
    } else if (_pagina == 5) {
        progLing(document.getElementById("prog_ling"));
    }
}


function registraMatricula() {
    var f = document.getElementById("matricula_eso");
    var f1 = document.getElementById("form_pagina_1");
    var f2 = document.getElementById("form_pagina_2");
    var f3 = document.getElementById("form_pagina_3");

    if (_curso == "2º ESO") {
        eso2_generaDatosSerialize();
        var f5 = document.getElementById("form_pagina_5_2eso");
        f.action = "php/premat2eso.php";
        f.eso_religion.value = retornaValRadioButton(f5.eso2_religion);
        f.eso_primer_idioma.value = retornaValRadioButton(f5.eso2_primer_idioma);
        f.appendChild(f5.eso2_opt1);
        f.appendChild(f5.eso2_opt2);
        f.appendChild(f5.eso2_opt3);
        f.appendChild(f5.eso2_opt4);
    } else if (_curso == "3º ESO") {
        eso3_generaDatosSerialize();
        var f5 = document.getElementById("form_pagina_5_3eso");
        f.action = "php/premat3eso.php";
        f.eso_religion.value = retornaValRadioButton(f5.eso3_religion);
        f.eso_primer_idioma.value = retornaValRadioButton(f5.eso3_primer_idioma);
        f.appendChild(f5.eso3_opt1);
        f.appendChild(f5.eso3_opt2);
        f.appendChild(f5.eso3_opt3);
        f.appendChild(f5.eso3_opt4);
    } else if (_curso == "4º ESO") {
        eso4_generaDatosSerialize();
        var f5 = document.getElementById("form_pagina_5_4eso");
        f.action = "php/premat4eso.php";
        f.matematicas.value = retornaValRadioButton(f5.eso4_matematicas);
        f.eso_religion.value = retornaValRadioButton(f5.eso4_religion);
        f.eso_primer_idioma.value = retornaValRadioButton(f5.eso4_primer_idioma);
        f.opcion_bloque1.value = retornaValRadioButton(f5.eso4_bloque1);
        
        f.appendChild(f5.eso4_opt1);
        f.appendChild(f5.eso4_opt2);
        f.appendChild(f5.eso4_opt3);
        f.appendChild(f5.eso4_opt4);
        f.appendChild(f5.eso4_opt5);
        f.appendChild(f5.eso4_opt6);
        f.appendChild(f5.eso4_opt7);
        f.appendChild(f5.eso4_opt8);
        f.appendChild(f5.eso4_opt9);
        f.appendChild(f5.eso4_opt10);
        f.appendChild(f5.eso4_opt11);
        f.appendChild(f5.eso4_opt12);
        f.appendChild(f5.eso4_opt13);
        f.appendChild(f5.eso4_opt14);
        f.appendChild(f5.eso4_opt15);

    }
    else if (_curso == "3º ESO DIV") {
        eso3_pmar_generaDatosSerialize();
        var f5 = document.getElementById("form_pagina_5_3esodiv");
        f.action = "php/premat3esodiv.php";
        f.eso_religion.value = retornaValRadioButton(f5.eso3_div_religion);
        f.appendChild(f5.eso3_div_opt1);
        f.appendChild(f5.eso3_div_opt2);
        f.appendChild(f5.eso3_div_opt3);
    } 
    else if (_curso == "4º ESO DIV") {
        eso4div_generaDatosSerialize();
        var f5 = document.getElementById("form_pagina_5_4esodiv");
        f.action = "php/premat4esodiv.php";
        f.eso_religion.value = retornaValRadioButton(f5.eso4div_religion);
        
        f.appendChild(f5.eso4_opt1);
        f.appendChild(f5.eso4_opt2);
        f.appendChild(f5.eso4_opt3);
        f.appendChild(f5.eso4_opt4);
        f.appendChild(f5.eso4_opt5);
        f.appendChild(f5.eso4_opt6);
        f.appendChild(f5.eso4_opt7);
        f.appendChild(f5.eso4_opt8);
        f.appendChild(f5.eso4_opt9);
        f.appendChild(f5.eso4_opt10);
        f.appendChild(f5.eso4_opt11);

    }

    f.appendChild(f1.curso);
    f.appendChild(f1.prog_ling);
    f.appendChild(f2.apellidos);
    f.appendChild(f2.nombre);
    f.appendChild(f2.sexo);
    f.appendChild(f2.fecha_nac);
    f.appendChild(f2.telef_alumno);
    f.appendChild(f2.email_alumno);
    f.appendChild(f3.tutor1);
    f.appendChild(f3.email_tutor1);
    f.appendChild(f3.tlf_tutor1);
    f.appendChild(f3.tutor2);
    f.appendChild(f3.email_tutor2);
    f.appendChild(f3.tlf_tutor2);
    document.getElementById("cargando").style.display = 'inherit';
    //f.submit();
    var pet = $.ajax({
        url: f.action,
        type: "POST",
        data: $("#matricula_eso").serialize()
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
            alerta("Proceso finalizado correctamente.<br>RECUERDE: El documento generado no es la matrícula del curso que viene.<br>El proceso de matriculación comienza hacia el mes de JULIO.<br>Puede descargar el formulario registrado desde el panel de control de usuario.", "Registro correcto", true, 500);
        }
    });
}

function cursoActual() {
    document.getElementById("sel_curso_act").value="";
    if (document.getElementById("curso").value == "2º ESO") {
        document.getElementById("sel_curso_act").disabled = true;
        document.getElementById("sel_curso_act").querySelector('option[value="1º ESO"]').style.display="inherit";
        document.getElementById("sel_curso_act").value = "1º ESO";
        document.getElementById("sel_grupo_curso_act").disabled = false;
        document.getElementById("curso_actual").value = "1º ESO";
    } else if (document.getElementById("curso").value == "3º ESO" || document.getElementById("curso").value == "3º ESO DIV") {
        document.getElementById("sel_curso_act").disabled = false;
        document.getElementById("sel_curso_act").querySelector('option[value="1º ESO"]').style.display="none";
        document.getElementById("sel_curso_act").querySelector('option[value="2º ESO"]').style.display="inherit";
        document.getElementById("sel_curso_act").querySelector('option[value="2º ESO PMAR"]').style.display="inherit";
        document.getElementById("sel_curso_act").querySelector('option[value="3º ESO"]').style.display="none";
        document.getElementById("sel_curso_act").querySelector('option[value="3º ESO DIV"]').style.display="none";
        document.getElementById("sel_grupo_curso_act").disabled = false;
    }  else if (document.getElementById("curso").value == "4º ESO" || document.getElementById("curso").value == "4º ESO DIV") {
        document.getElementById("sel_curso_act").disabled = false;
        document.getElementById("sel_curso_act").querySelector('option[value="1º ESO"]').style.display="none";
        document.getElementById("sel_curso_act").querySelector('option[value="2º ESO"]').style.display="none";
        document.getElementById("sel_curso_act").querySelector('option[value="2º ESO PMAR"]').style.display="none";
        document.getElementById("sel_curso_act").querySelector('option[value="3º ESO"]').style.display="inherit";
        document.getElementById("sel_curso_act").querySelector('option[value="3º ESO DIV"]').style.display="inherit";
        document.getElementById("sel_grupo_curso_act").disabled = false;
    } 
}

function seleccionCursoActual(c) {
    document.getElementById("curso_actual").value = c;
    if (c == "2º ESO PMAR") {
        document.getElementById("sel_grupo_curso_act").disabled = true;
        document.getElementById("sel_grupo_curso_act").value = "A";
        document.getElementById("grupo_curso_actual").value = "A";
    }
    else if (c == "3º ESO") {
        document.getElementById("sel_grupo_curso_act").disabled = false;
    } else if (c == "3º ESO DIV") {
        document.getElementById("sel_grupo_curso_act").disabled = true;
        document.getElementById("sel_grupo_curso_act").value = "A";
        document.getElementById("grupo_curso_actual").value = "A";
    }
    else if (c == "4º ESO") {
        document.getElementById("sel_grupo_curso_act").disabled = false;
    } else if (c == "4º ESO DIV") {
        document.getElementById("sel_grupo_curso_act").disabled = true;
        document.getElementById("sel_grupo_curso_act").value = "A";
        document.getElementById("grupo_curso_actual").value = "A";
    }
    else document.getElementById("sel_grupo_curso_act").disabled = false;

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