// JavaScript Document




function b2c_seleccionIdioma(_idioma) {
    var lista = document.getElementById("b2c_esp_itin1");

    b2c_muestraEspecItin();

    for (i = 0; i < lista.length; i++) {
        if (_idioma == "ingles" && lista.options[i].innerHTML == "2ª Lengua Extranjera II (Inglés)") lista.options[i].innerHTML = "2ª Lengua Extranjera II (Francés)";
        else if (_idioma == "frances" && lista.options[i].innerHTML == "2ª Lengua Extranjera II (Francés)") lista.options[i].innerHTML = "2ª Lengua Extranjera II (Inglés)";
    }
    lista.selectedIndex = -1;
}


function b2c_muestraEspecItin() {
    var idioma = document.getElementById("b2c_ingles").checked || document.getElementById("b2c_frances").checked;
    
    if (idioma && document.querySelectorAll('input[name="b2c_mod"]:checked').length==2  && document.querySelectorAll('input[name="b2c_mat"]:checked').length==1) {
        $("#div_b2c_esp_itin_vacio").addClass("d-none");
        $("#div_b2c_esp_itin1").removeClass("d-none");
        $("#rot_epec_itin").css("margin-top","30px");
    } else {
        $("#div_b2c_esp_itin_vacio").removeClass("d-none");
        $("#div_b2c_esp_itin1").addClass("d-none");
        $("#rot_epec_itin").css("margin-top","0px");
    }
}



function b2c_generaDatosSerialize() {
    document.getElementById("b2c_eitin11").value = document.getElementById("b2c_esp_itin1").options[0].innerHTML;
    document.getElementById("b2c_eitin12").value = document.getElementById("b2c_esp_itin1").options[1].innerHTML;
    document.getElementById("b2c_eitin13").value = document.getElementById("b2c_esp_itin1").options[2].innerHTML;
    document.getElementById("b2c_eitin14").value = document.getElementById("b2c_esp_itin1").options[3].innerHTML;
    document.getElementById("b2c_eitin15").value = document.getElementById("b2c_esp_itin1").options[4].innerHTML;
    document.getElementById("b2c_eitin16").value = document.getElementById("b2c_esp_itin1").options[5].innerHTML;
    document.getElementById("b2c_eitin17").value = document.getElementById("b2c_esp_itin1").options[6].innerHTML;
    document.getElementById("b2c_eitin18").value = document.getElementById("b2c_esp_itin1").options[7].innerHTML;
    document.getElementById("b2c_eitin19").value = document.getElementById("b2c_esp_itin1").options[8].innerHTML;
    document.getElementById("b2c_eitin20").value = document.getElementById("b2c_esp_itin1").options[9].innerHTML;
    document.getElementById("b2c_eitin21").value = document.getElementById("b2c_esp_itin1").options[10].innerHTML;
    document.getElementById("b2c_eitin22").value = document.getElementById("b2c_esp_itin1").options[11].innerHTML;
    document.getElementById("b2c_eitin23").value = document.getElementById("b2c_esp_itin1").options[12].innerHTML;
    document.getElementById("b2c_eitin24").value = document.getElementById("b2c_esp_itin1").options[13].innerHTML;
    document.getElementById("b2c_eitin25").value = document.getElementById("b2c_esp_itin1").options[14].innerHTML;
    document.getElementById("b2c_eitin26").value = document.getElementById("b2c_esp_itin1").options[15].innerHTML;
    document.getElementById("b2c_eitin27").value = document.getElementById("b2c_esp_itin1").options[16].innerHTML;
    document.getElementById("b2c_eitin28").value = document.getElementById("b2c_esp_itin1").options[17].innerHTML;

}