// JavaScript Document

var optativas1 = {
  'matematicasA': ['Economía y emprendimiento + Latín',
                   'Expresión artística + Latín', 
                   'Música + Latín',
                   'Formación y orientación personal y profesional + Latín',
                   'Formación y orientación personal y profesional + Expresión artística',
                   'Formación y orientación personal y profesional + Música',
                   'Formación y orientación personal y profesional + Tecnología',
                   'Formación y orientación personal y profesional + Economía y emprendimiento'],
  'matematicasB': ['Física y química + Biología y geología', 
                  'Física y química + Tecnología', 
                  'Biología y geología + Tecnología',
                  'Economía y emprendimiento + Latín',
                  'Economía y emprendimiento + Música',
                  'Economía y emprendimiento + Biología y geología',
                  'Economía y emprendimiento + Expresión artística',
                  'Expresión artística + Tecnología',
                  'Formación y orientación personal y profesional + Economía y emprendimiento',
                  'Formación y orientación personal y profesional + Tecnología']
};


function opcionSubeElemento(obj){
  subeElemento(obj);
  //mueveElementoAlFinal();
}

function opcionBajaElemento(obj){
  bajaElemento(obj);
  //mueveElementoAlFinal();
}

function optativasMate(t){
  document.getElementById("div_opt_ini").style.display="none";
  var longitud={"matematicasA":[8],"matematicasB":[10]};
  sel=document.getElementsByName("eso4_bloque1");
  for (i=0;i<10;i++) document.getElementById("div_opt_"+i).style.display="none";
  for (i=0; i<longitud[t][0];i++){
    document.getElementById("div_opt_"+i).style.display="";
    sel.value=optativas1[t][i];
    document.getElementById("l_eso4_bloque1"+i).innerText=optativas1[t][i];
  }
}

function eso4_seleccionIdioma(_idioma) {
    var lista_aplic = document.getElementById("eso4_opc_bloq3");

    for (i = 0; i < lista_aplic.length; i++) {
        if (_idioma == "ingles" && lista_aplic.options[i].innerHTML == "2ª Lengua Extranjera (Inglés)") lista_aplic.options[i].innerHTML = "2ª Lengua Extranjera (Francés)";
        else if (_idioma == "frances" && lista_aplic.options[i].innerHTML == "2ª Lengua Extranjera (Francés)") lista_aplic.options[i].innerHTML = "2ª Lengua Extranjera (Inglés)";
    }
    lista_aplic.selectedIndex = -1;

}

/*function mueveElementoAlFinal(){
    sel=document.querySelectorAll("input[name=eso4_bloque1]:checked")[0].value;
    if(sel=="Formación y Orientación Laboral") select = document.getElementById("eso4_opc_bloq3");
    else select = document.getElementById("eso4_opc_bloq2");
    
    const optionToMove = select.querySelector('option[value="'+sel+'"]'); // la opción que queremos mover, con el valor "B"
    if (optionToMove) select.appendChild(optionToMove);
    
}*/



function eso4_generaDatosSerialize() {
    //document.getElementById("eso4_opt1").value = document.getElementById("eso4_opc_bloq2").options[0].innerHTML;
    //document.getElementById("eso4_opt2").value = document.getElementById("eso4_opc_bloq2").options[1].innerHTML;
    //document.getElementById("eso4_opt3").value = document.getElementById("eso4_opc_bloq2").options[2].innerHTML;
    //document.getElementById("eso4_opt4").value = document.getElementById("eso4_opc_bloq2").options[3].innerHTML;
    document.getElementById("eso4_opt5").value = document.getElementById("eso4_opc_bloq3").options[0].innerHTML;
    document.getElementById("eso4_opt6").value = document.getElementById("eso4_opc_bloq3").options[1].innerHTML;
    document.getElementById("eso4_opt7").value = document.getElementById("eso4_opc_bloq3").options[2].innerHTML;
    document.getElementById("eso4_opt8").value = document.getElementById("eso4_opc_bloq3").options[3].innerHTML;
    document.getElementById("eso4_opt9").value = document.getElementById("eso4_opc_bloq3").options[4].innerHTML;
    //document.getElementById("eso4_opt10").value = document.getElementById("eso4_opc_bloq3").options[5].innerHTML;
    document.getElementById("eso4_opt11").value = document.getElementById("eso4_optativas").options[0].innerHTML;
    document.getElementById("eso4_opt12").value = document.getElementById("eso4_optativas").options[1].innerHTML;
    document.getElementById("eso4_opt13").value = document.getElementById("eso4_optativas").options[2].innerHTML;
    document.getElementById("eso4_opt14").value = document.getElementById("eso4_optativas").options[3].innerHTML;
    document.getElementById("eso4_opt15").value = document.getElementById("eso4_optativas").options[4].innerHTML;
}