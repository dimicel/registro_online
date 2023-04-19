// JavaScript Document




function b2h_seleccionIdioma(_idioma) {
    var lista = document.getElementById("b2h_esp_itin1");

    b2h_muestraEspecItin();

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

function b2h_mat_click(val){
    $("#div_mod").show();
    $("#div_mod_empty").hide();
    document.getElementById("b2h_mod5").value=val;
    document.querySelectorAll("label[for='b2h_mod5']")[0].innerHTML=val;
}

function b2h_mod_click(obj){
    limitCheckboxes('input[name=\'b2h_mod\']', 2);
    b2h_cambiaOptativas(obj);
    b2h_muestraEspecItin();
}

function b2h_muestraEspecItin() {
    var idioma = document.getElementById("b2h_ingles").checked || document.getElementById("b2h_frances").checked;
    
    if (idioma &&  document.querySelectorAll('input[name="b2h_mod"]:checked').length>=2) {
        $("#div_b2h_esp_itin_vacio").addClass("d-none");
        $("#div_b2h_esp_itin1").removeClass("d-none");
        $("#rot_epec_itin").css("margin-top","30px");
    } else {
        $("#div_b2h_esp_itin_vacio").removeClass("d-none");
        $("#div_b2h_esp_itin1").addClass("d-none");
        $("#rot_epec_itin").css("margin-top","0px");
    }
}


function b2h_cambiaOptativas(m){
    if(m.value!="Latín II" && m.value!="Matemáticas aplicadas a las Ciencias Sociales II"){
        var mat_modalidad=["Griego II","Empresa y Diseño de Modelos de Negocio","Geografía","Historia del Arte"];
        var desp=document.getElementById("b2h_esp_itin1");
        if(document.querySelectorAll('input[name="b2h_mod"]:checked').length==2){
            //elimino del array los que están seleccionados
            _k=document.querySelectorAll('input[name="b2h_mod"]:checked');
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
                for(i=0;i<desp.length;i++){
                    indice=mat_modalidad.indexOf(desp[i].value);
                    if (indice>-1) mat_modalidad.splice(indice,1);
                }
                //Asigno la que he marcado a la desmarcada
                desm=desp.querySelectorAll("option[value='"+m.value+"']");
                desm[0].value=mat_modalidad[0];
                desm[0].innerHTML=mat_modalidad[0];
            }
        }
    }
}


function b2h_generaDatosSerialize() {
    document.getElementById("b2h_eitin11").value = document.getElementById("b2h_esp_itin1").options[0].innerHTML;
    document.getElementById("b2h_eitin12").value = document.getElementById("b2h_esp_itin1").options[1].innerHTML;
    document.getElementById("b2h_eitin13").value = document.getElementById("b2h_esp_itin1").options[2].innerHTML;
    document.getElementById("b2h_eitin14").value = document.getElementById("b2h_esp_itin1").options[3].innerHTML;
    document.getElementById("b2h_eitin15").value = document.getElementById("b2h_esp_itin1").options[4].innerHTML;
    document.getElementById("b2h_eitin16").value = document.getElementById("b2h_esp_itin1").options[5].innerHTML;
    document.getElementById("b2h_eitin17").value = document.getElementById("b2h_esp_itin1").options[6].innerHTML;
    document.getElementById("b2h_eitin18").value = document.getElementById("b2h_esp_itin1").options[7].innerHTML;
    document.getElementById("b2h_eitin19").value = document.getElementById("b2h_esp_itin1").options[8].innerHTML;
    document.getElementById("b2h_eitin20").value = document.getElementById("b2h_esp_itin1").options[9].innerHTML;
    document.getElementById("b2h_eitin21").value = document.getElementById("b2h_esp_itin1").options[10].innerHTML;
    document.getElementById("b2h_eitin22").value = document.getElementById("b2h_esp_itin1").options[11].innerHTML;
    document.getElementById("b2h_eitin23").value = document.getElementById("b2h_esp_itin1").options[12].innerHTML;
    document.getElementById("b2h_eitin24").value = document.getElementById("b2h_esp_itin1").options[13].innerHTML;
    document.getElementById("b2h_eitin25").value = document.getElementById("b2h_esp_itin1").options[14].innerHTML;
    document.getElementById("b2h_eitin26").value = document.getElementById("b2h_esp_itin1").options[15].innerHTML;
    document.getElementById("b2h_eitin27").value = document.getElementById("b2h_esp_itin1").options[16].innerHTML;
}