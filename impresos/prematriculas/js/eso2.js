// JavaScript Document
function eso2_seleccionIdioma(_idioma) {
    var lista = document.getElementById("eso2_lista_optativas");
    document.getElementById("eso2_optativas_vacio").style.display = "none";
    document.getElementById("eso2_optativas").style.display = "inline-block";
    for (i = 0; i < lista.length; i++) {
        if (_idioma == "ingles" && lista.options[i].innerHTML == "2ª Lengua Extranjera (Inglés)") lista.options[i].innerHTML = "2ª Lengua Extranjera (Francés)";
        else if (_idioma == "frances" && lista.options[i].innerHTML == "2ª Lengua Extranjera (Francés)") lista.options[i].innerHTML = "2ª Lengua Extranjera (Inglés)";
    }
    lista.selectedIndex = -1;
}

function eso2_generaDatosSerialize() {
    document.getElementById("eso2_opt1").value = document.getElementById("eso2_lista_optativas").options[0].innerHTML;
    document.getElementById("eso2_opt2").value = document.getElementById("eso2_lista_optativas").options[1].innerHTML;
    document.getElementById("eso2_opt3").value = document.getElementById("eso2_lista_optativas").options[2].innerHTML;
    document.getElementById("eso2_opt4").value = document.getElementById("eso2_lista_optativas").options[3].innerHTML;
}