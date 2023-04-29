// JavaScript Document


function opcionSubeElemento(obj){
  subeElemento(obj);
  mueveElementoAlFinal();
}

function opcionBajaElemento(obj){
  bajaElemento(obj);
  mueveElementoAlFinal();
}


function eso4_seleccionIdioma(_idioma) {
    var lista_aplic = document.getElementById("eso4_opc_bloq3");

    for (i = 0; i < lista_aplic.length; i++) {
        if (_idioma == "ingles" && lista_aplic.options[i].innerHTML == "2ª Lengua Extranjera (Inglés)") lista_aplic.options[i].innerHTML = "2ª Lengua Extranjera (Francés)";
        else if (_idioma == "frances" && lista_aplic.options[i].innerHTML == "2ª Lengua Extranjera (Francés)") lista_aplic.options[i].innerHTML = "2ª Lengua Extranjera (Inglés)";
    }
    lista_aplic.selectedIndex = -1;

}

function mueveElementoAlFinal(){
    sel=document.querySelectorAll("input[name=eso4_bloque1]:checked")[0].value;
    if(sel=="Formación y Orientación Laboral") select = document.getElementById("eso4_opc_bloq3");
    else select = document.getElementById("eso4_opc_bloq2");
    
    const optionToMove = select.querySelector('option[value="'+sel+'"]'); // la opción que queremos mover, con el valor "B"
    if (optionToMove) select.appendChild(optionToMove);
    
}



function eso4_generaDatosSerialize() {
    document.getElementById("eso4_opt1").value = document.getElementById("eso4_opc_bloq2").options[0].innerHTML;
    document.getElementById("eso4_opt2").value = document.getElementById("eso4_opc_bloq2").options[1].innerHTML;
    document.getElementById("eso4_opt3").value = document.getElementById("eso4_opc_bloq2").options[2].innerHTML;
    document.getElementById("eso4_opt4").value = document.getElementById("eso4_opc_bloq2").options[3].innerHTML;
    document.getElementById("eso4_opt5").value = document.getElementById("eso4_opc_bloq3").options[0].innerHTML;
    document.getElementById("eso4_opt6").value = document.getElementById("eso4_opc_bloq3").options[1].innerHTML;
    document.getElementById("eso4_opt7").value = document.getElementById("eso4_opc_bloq3").options[2].innerHTML;
    document.getElementById("eso4_opt8").value = document.getElementById("eso4_opc_bloq3").options[3].innerHTML;
    document.getElementById("eso4_opt9").value = document.getElementById("eso4_opc_bloq3").options[4].innerHTML;
    document.getElementById("eso4_opt10").value = document.getElementById("eso4_opc_bloq3").options[5].innerHTML;
    document.getElementById("eso4_opt11").value = document.getElementById("eso4_optativas").options[0].innerHTML;
    document.getElementById("eso4_opt12").value = document.getElementById("eso4_optativas").options[1].innerHTML;
    document.getElementById("eso4_opt13").value = document.getElementById("eso4_optativas").options[2].innerHTML;
    document.getElementById("eso4_opt14").value = document.getElementById("eso4_optativas").options[3].innerHTML;
    document.getElementById("eso4_opt15").value = document.getElementById("eso4_optativas").options[4].innerHTML;
}