// JavaScript Document

function eso4_subeElemento() {
    subeElemento(document.getElementById("eso4_lista_optativas_acad"));
}

function eso4_bajaElemento() {
    bajaElemento(document.getElementById("eso4_lista_optativas_acad"));
}


function eso4_seleccionIdioma(_idioma) {
    var lista_aplic = document.getElementById("eso4_lista_optativas_aplic");
    var lista_acad = document.getElementById("eso4_lista_optativas_acad");

    optativas();

    for (i = 0; i < lista_aplic.length; i++) {
        if (_idioma == "ingles" && lista_aplic.options[i].innerHTML == "2ª Lengua Extranjera (Inglés)") lista_aplic.options[i].innerHTML = "2ª Lengua Extranjera (Francés)";
        else if (_idioma == "frances" && lista_aplic.options[i].innerHTML == "2ª Lengua Extranjera (Francés)") lista_aplic.options[i].innerHTML = "2ª Lengua Extranjera (Inglés)";
    }
    lista_aplic.selectedIndex = -1;

    for (i = 0; i < lista_acad.length; i++) {
        if (_idioma == "ingles" && lista_acad.options[i].innerHTML == "2ª Lengua Extranjera (Inglés)") lista_acad.options[i].innerHTML = "2ª Lengua Extranjera (Francés)";
        else if (_idioma == "frances" && lista_acad.options[i].innerHTML == "2ª Lengua Extranjera (Francés)") lista_acad.options[i].innerHTML = "2ª Lengua Extranjera (Inglés)";
    }
    lista_acad.selectedIndex = -1;
}


function optativas() {
    var idioma = retornaValRadioButton(document.getElementsByName("eso4_primer_idioma"));
    if ( idioma == "Inglés" || idioma == "Francés") {
        document.getElementById("div_eso4_esp_op_lib_conf_vacio").style.display = "none";
        document.getElementById("div_eso4_esp_op_lib_conf").style.display = "inherit";
        document.getElementById("exo4_esp_op_num").innerHTML = "<p>1</p><p style='margin-top:-23px'>2</p><p style='margin-top:-23px'>3</p><p style='margin-top:-23px'>4</p><p style='margin-top:-23px'>5</p><p style='margin-top:-23px'>6</p><p style='margin-top:-23px'>7</p><p style='margin-top:-23px'>8</p><p style='margin-top:-23px'>9</p>"
        document.getElementById("eso4_lista_optativas_acad").style.display = "inherit";
        document.getElementById("eso4_lista_optativas_aplic").style.display = "none";
    }
}


function eso4_generaDatosSerialize() {
    document.getElementById("eso4_opt1").value = document.getElementById("eso4_lista_optativas_acad").options[0].innerHTML;
    document.getElementById("eso4_opt2").value = document.getElementById("eso4_lista_optativas_acad").options[1].innerHTML;
    document.getElementById("eso4_opt3").value = document.getElementById("eso4_lista_optativas_acad").options[2].innerHTML;
    document.getElementById("eso4_opt4").value = document.getElementById("eso4_lista_optativas_acad").options[3].innerHTML;
    document.getElementById("eso4_opt5").value = document.getElementById("eso4_lista_optativas_acad").options[4].innerHTML;
    document.getElementById("eso4_opt6").value = document.getElementById("eso4_lista_optativas_acad").options[5].innerHTML;
    document.getElementById("eso4_opt7").value = document.getElementById("eso4_lista_optativas_acad").options[6].innerHTML;
    document.getElementById("eso4_opt8").value = document.getElementById("eso4_lista_optativas_acad").options[7].innerHTML;
    document.getElementById("eso4_opt9").value = document.getElementById("eso4_lista_optativas_acad").options[8].innerHTML;

    document.getElementById("eso4_esp_ob_mod").value = document.getElementById("eso4_espc_oblig_modalidad").innerHTML;
}