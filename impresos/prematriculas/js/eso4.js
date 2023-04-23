// JavaScript Document


function eso4_seleccionIdioma(_idioma) {
    var lista_aplic = document.getElementById("eso4_opc_bloq3");


    for (i = 0; i < lista_aplic.length; i++) {
        if (_idioma == "ingles" && lista_aplic.options[i].innerHTML == "2ª Lengua Extranjera (Inglés)") lista_aplic.options[i].innerHTML = "2ª Lengua Extranjera (Francés)";
        else if (_idioma == "frances" && lista_aplic.options[i].innerHTML == "2ª Lengua Extranjera (Francés)") lista_aplic.options[i].innerHTML = "2ª Lengua Extranjera (Inglés)";
    }
    lista_aplic.selectedIndex = -1;

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