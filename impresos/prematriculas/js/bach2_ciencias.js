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

function b2c_seleccItin(op) {
    var lista = document.getElementById("b2c_esp_itin1");

    b2c_muestraEspecItin();

    if (op == "itinerario 3") {
        $("#div_b2c_op_vacio").addClass("d-none");
        $("#div_b2c_op1").removeClass("d-none");
        $("#div_b2c_op2").addClass("d-none");
    } else if (op == "itinerario 4") {
        $("#div_b2c_op_vacio").addClass("d-none");
        $("#div_b2c_op1").addClass("d-none");
        $("#div_b2c_op2").removeClass("d-none");
    }

    if (op == "itinerario 3") {
        op = "Biología";
        b2c_seleccTronOpItin1(retornaValRadioButton(document.getElementsByName("b2c_op1")));
    } else if (op == "itinerario 4") {
        op = "Física";
        b2c_seleccTronOpItin2(retornaValRadioButton(document.getElementsByName("b2c_op2")));
    }

    for (i = 0; i < lista.length; i++) {
        if (lista.options[i].value == "1") lista.options[i].innerHTML = op;
    }

    lista.selectedIndex = -1;
}



function b2c_seleccTronOpItin1(op) {
    var lista = document.getElementById("b2c_esp_itin1");

    b2c_muestraEspecItin();

    if (op == "Dibujo Técnico II") {
        for (i = 0; i < lista.length; i++) {
            if (lista.options[i].value == "2") lista.options[i].innerHTML = "Geología";
            if (lista.options[i].value == "3") lista.options[i].innerHTML = "Química";
        }
    } else if (op == "Química") {
        for (i = 0; i < lista.length; i++) {
            if (lista.options[i].value == "2") lista.options[i].innerHTML = "Geología";
            if (lista.options[i].value == "3") lista.options[i].innerHTML = "Dibujo Técnico II";
        }
    }
}

function b2c_seleccTronOpItin2(op) {
    var lista = document.getElementById("b2c_esp_itin1");

    b2c_muestraEspecItin();

    if (op == "Geología") {
        for (i = 0; i < lista.length; i++) {
            if (lista.options[i].value == "2") lista.options[i].innerHTML = "Dibujo Técnico II";
            if (lista.options[i].value == "3") lista.options[i].innerHTML = "Química";
        }
    } else if (op == "Química") {
        for (i = 0; i < lista.length; i++) {
            if (lista.options[i].value == "2") lista.options[i].innerHTML = "Dibujo Técnico II";
            if (lista.options[i].value == "3") lista.options[i].innerHTML = "Geología";
        }
    }
}




function b2c_muestraEspecItin() {
    var itin = retornaValRadioButton(document.getElementsByName("b2c_itin"));
    var idioma = document.getElementById("b2c_ingles").checked || document.getElementById("b2c_frances").checked;

    if (itin == "itinerario 3") var itinto = document.getElementById("b2c_op11").checked || document.getElementById("b2c_op12").checked;
    else if (itin == "itinerario 4") var itinto = document.getElementById("b2c_op21").checked || document.getElementById("b2c_op22").checked;
    else var itinto = false;

    if (idioma && itinto) {
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