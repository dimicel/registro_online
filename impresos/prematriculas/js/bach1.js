// JavaScript Document

var marcadas=0;


function b1_seleccionModalidad(_mod){
    $("[itemprop='sin_modalidad'").hide();
    $("[itemprop='con_modalidad'").show();
    
    if (_mod=='h'){
        document.getElementById("b1_oblig_cabecera").innerHTML="OBLIGATORIA (Marque una)";
    }
    else {
        document.getElementById("b1_oblig_cabecera").innerHTML="OBLIGATORIA";
    }
    
    if(_mod=="c"){
        $("[itemprop='mod_c'").show(); 
        $("[itemprop='mod_g'").hide();
        $("[itemprop='mod_h'").hide();
		$("[itemprop='mod_h_sin_selecc'").hide();
        $("[itemprop='mod_h_con_selecc'").hide();
    }
    else if(_mod=="h"){
        if (!document.getElementById("oblig1_h_latin").checked && !document.getElementById("oblig1_h_matematicas").checked){
            $("[itemprop='mod_h_sin_selecc'").show();
            $("[itemprop='mod_h_con_selecc'").hide();
        }
        else {
            $("[itemprop='mod_h_sin_selecc'").hide();
            $("[itemprop='mod_h_con_selecc'").show();
        }
        $("[itemprop='mod_c'").hide();
        $("[itemprop='mod_g'").hide();
		$("[itemprop='mod_h'").show();
    }
    else if(_mod=="g"){
        $("[itemprop='mod_c'").hide();
        $("[itemprop='mod_g'").show();
        $("[itemprop='mod_h'").hide();
		$("[itemprop='mod_h_sin_selecc'").hide();
        $("[itemprop='mod_h_con_selecc'").hide();
    }
    compruebaSelectores();
}

function b1_seleccionOblig1_h(_obl1){
    $("[itemprop='mod_h_sin_selecc'").hide();
    $("[itemprop='mod_h_con_selecc'").show();
    if (_obl1=="Latín I"){
        document.getElementById("ob2_selob1").innerHTML="Matemáticas Aplicadas a las Ciencias Sociales I";
        document.getElementById("o2h5").value="Matemáticas Aplicadas a las Ciencias Sociales I";
    }
    else if(_obl1="Matemáticas Aplicadas a las Ciencias Sociales I"){
        document.getElementById("ob2_selob1").innerHTML="Latín I";
        document.getElementById("o2h5").value="Latín I";
    }

    compruebaSelectores();
}


function selOblig2(obj,prop){
    var lista=$("[itemprop='"+prop+"']");
    clicados=0;
    for (i=0;i<lista.length;i++){
        if(lista[i].checked) clicados++;
    }
    if (clicados>2){
        obj.checked=false;
        $(".alert-warning").show();
        setTimeout(()=>{$(".alert-warning").fadeOut(1500);},1000);
    }
    compruebaSelectores();
    
}


function compruebaSelectores(){
    ll = new Array();
    l_c=$("#form_pagina_5_1 input:not([type='button'])[name!='b1_modalidad']:checked:visible");
    l_nc=$("#form_pagina_5_1 input:not([type='button'])[name!='b1_modalidad']:not(:checked):visible");
    marcadas=l_c.length;
    //l=$("#lista_optativas option:not([value^='2º Lengua Extranjera:'])");
    
    l_todas=new Array(
        'Anatomía Aplicada',
        'Desarrollo Digital',
        'Lenguaje y Práctica Musical',
        'Psicología',
        'Matemáticas I',
        'Biología, Geología y Ciencias Ambientales',
        'Dibujo Técnico I',
        'Física y Química',
        'Tecnología e Ingeniería I',
        'Latín I',
        'Matemáticas Aplicadas a las Ciencias Sociales I',
        'Economía',
        'Griego I',
        'Historia del Mundo Contemporáneo',
        'Literatura Universal',
        'Matemáticas Generales',
        'Economía, Emprendimiento y Actividad Empresarial'
    );

    l=new Array();
    for (i=0;i<document.getElementById("lista_optativas").options.length;i++){
        l.push(document.getElementById("lista_optativas").options[i].value);
    }

    for(i=0;i<l_todas.length;i++){
        esta=false;
        for(j=0;j<l.length;j++){
            if(l_todas[i]==l[j])esta=true;
        }
        if(!esta)l.push(l_todas[i]);
    }
    
    if (marcadas==5){
        $("[itemprop='list_opt_con_oblig']").show();
        $("[itemprop='list_opt_sin_oblig']").hide();
    }
    else {
        $("[itemprop='list_opt_sin_oblig']").show();
        $("[itemprop='list_opt_con_oblig']").hide();
    }


    //Eliminamos los option que no sean 2ª lengua extranjera
    //document.querySelectorAll("#lista_optativas option:not([value^='2ª Lengua Extranjera:'])").forEach(option => option.remove());
    document.getElementById("lista_optativas").innerHTML="";
    //Filtramos del array l y se quitan las que están marcadas como obligatorias
    ll=l;
    for(i=0;i<l_c.length;i++){
        ll=ll.filter((v)=>{
            return v != l_c[i].value;
        });
    }

    // Regeneramos select options añadiendo las opciones del array l filtrado
    for (i=0;i<ll.length;i++){
        var opt = document.createElement("option");
        opt.value = ll[i];
        opt.innerHTML = ll[i];
        document.getElementById("lista_optativas").appendChild(opt);
    }
        
    
}

function seleccionIdioma(){
    if (document.getElementById("b1_ingles").checked){
        var op=$("#lista_optativas option[value~='Inglés']");
        op[0].value="2ª Lengua Extranjera: Francés";
        op[0].innerHTML="2ª Lengua Extranjera: Francés";  
    }
    else {
        var op=$("#lista_optativas option[value~='Francés']");
        if (op.length!=0){
            op[0].value="2ª Lengua Extranjera: Inglés";
            op[0].innerHTML="2ª Lengua Extranjera: Inglés";
        }
    }
    compruebaSelectores();
}

