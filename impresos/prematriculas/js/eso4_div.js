// JavaScript Document



function eso4div_seleccionIdioma(_idioma) {
    var lista_aplic = document.getElementById("eso4div_opc");

    for (i = 0; i < lista_aplic.length; i++) {
        if (_idioma == "ingles" && lista_aplic.options[i].innerHTML == "2ª Lengua Extranjera (Inglés)") lista_aplic.options[i].innerHTML = "2ª Lengua Extranjera (Francés)";
        else if (_idioma == "frances" && lista_aplic.options[i].innerHTML == "2ª Lengua Extranjera (Francés)") lista_aplic.options[i].innerHTML = "2ª Lengua Extranjera (Inglés)";
    }
    lista_aplic.selectedIndex = -1;

}



function eso4div_generaDatosSerialize() {
    document.getElementById("eso4_opt1").value = document.getElementById("eso4div_opc").options[0].innerHTML;
    document.getElementById("eso4_opt2").value = document.getElementById("eso4div_opc").options[1].innerHTML;
    document.getElementById("eso4_opt3").value = document.getElementById("eso4div_opc").options[2].innerHTML;
    document.getElementById("eso4_opt4").value = document.getElementById("eso4div_opc").options[3].innerHTML;
    document.getElementById("eso4_opt5").value = document.getElementById("eso4div_opc").options[4].innerHTML;
    document.getElementById("eso4_opt6").value = document.getElementById("eso4div_opc").options[5].innerHTML;
    document.getElementById("eso4_opt7").value = document.getElementById("eso4div_optativas").options[0].innerHTML;
    document.getElementById("eso4_opt8").value = document.getElementById("eso4div_optativas").options[1].innerHTML;
    document.getElementById("eso4_opt9").value = document.getElementById("eso4div_optativas").options[2].innerHTML;
    document.getElementById("eso4_opt10").value = document.getElementById("eso4div_optativas").options[3].innerHTML;
    document.getElementById("eso4_opt11").value = document.getElementById("eso4div_optativas").options[4].innerHTML;
}