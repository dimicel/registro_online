// JavaScript Document




function b2c_seleccionIdioma(_idioma) {
    var lista = document.getElementById("b2c_esp_itin1");

    b2c_muestraEspecItin();

    for (i = 0; i < lista.length; i++) {
        if (_idioma == "ingles" && lista.options[i].innerHTML == "2ª Lengua Extranjera II (Inglés)") {
            lista.options[i].innerHTML = "2ª Lengua Extranjera II (Francés)";
            lista.options[i].value = "2ª Lengua Extranjera II (Francés)";
        }
        else if (_idioma == "frances" && lista.options[i].innerHTML == "2ª Lengua Extranjera II (Francés)") {
            lista.options[i].innerHTML = "2ª Lengua Extranjera II (Inglés)";
            lista.options[i].value = "2ª Lengua Extranjera II (Inglés)";
        }
    }
    lista.selectedIndex = -1;
}


function b2c_mod_click(obj){
    limitCheckboxes('input[name=\'b2c_mod\']', 2);
    b2c_cambiaOptativas(obj);
    b2c_muestraEspecItin();
}

function b2c_muestraEspecItin() {
    var idioma = document.getElementById("b2c_ingles").checked || document.getElementById("b2c_frances").checked;
    var matem = document.getElementById("b2c_mat").checked || document.getElementById("b2c_mat_acs").checked;
    
    if (idioma && matem  && document.querySelectorAll('input[name="b2c_mod"]:checked').length==2) {
        $("#div_b2c_esp_itin_vacio").addClass("d-none");
        $("#div_b2c_esp_itin1").removeClass("d-none");
        $("#rot_epec_itin").css("margin-top","30px");
    } else {
        $("#div_b2c_esp_itin_vacio").removeClass("d-none");
        $("#div_b2c_esp_itin1").addClass("d-none");
        $("#rot_epec_itin").css("margin-top","0px");
    }
}


function b2c_cambiaOptativas(m){
    var mat_modalidad=["Biología","Dibujo Técnico II","Física","Química","Biología y Ciencias Ambientales","Tecnología e Ingeniería II"]
    var desp=document.getElementById("b2c_esp_itin1");
    if(document.querySelectorAll('input[name="b2c_mod"]:checked').length==2){
        //elimino del array los que están seleccionados
        _k=document.querySelectorAll('input[name="b2c_mod"]:checked');
        for (i=0; i<_k.length;i++){
            mat_modalidad.splice(mat_modalidad.indexOf(_k[i].value),1);
        }
        //Si los options están vacío (inicialmente son 4) se le dan valores de las materis de modalidad no marcadas
        _kk=desp.querySelectorAll("option[value='']");
        if(_kk.length==4){
            for (i=0;i<_kk.length;i++){
                _kk[i].value=mat_modalidad[i];
                _kk[i].innerHTML=mat_modalidad[i];
            }
        }
        //Si ya estaban con valores, busco el seleccionado y lo cambio por el que se ha quedado deseleccionado, sin modificar el orden de los option
        else{
            //Al cambiar una de las materias, una de las del array mat_modalidad no está, y debe de ser la desmarcada.
            alert(mat_modalidad.length)
            for(i=0;i<desp.length;i++){
                mat_modalidad.splice(mat_modalidad.indexOf(desp[i].value),1);
            }
            //Asigno la que he marcado a la desmarcada
            alert(mat_modalidad.length+"    "+mat_modalidad[0])
            desm=desp.querySelectorAll("option[value='"+mat_modalidad[0]+"']");
            desm.value=m.value;
            desm.innerHTML=m.value;
        }
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